<?php

namespace App\Services;

use Smalot\PdfParser\Parser as PdfTextParser;

/**
 * Parses the plain text extracted from a Coretax "Faktur Pajak Masukan"
 * (FPM) PDF into a structured array. Built from the concrete sample PDF
 * reviewed during requirements discussion - NOT from assumed structure.
 *
 * Known layout quirks handled here:
 *  - Item rows are ONE merged text block, not separate columns:
 *      "Rp 430.000,00 x 1,00 Kegiatan"
 *      "Potongan Harga = Rp 0,00"
 *      "PPnBM (0,00%) = Rp 0,00"
 *  - "Tanggal Faktur" is taken from the e-signature line at the bottom
 *    of the document (there is no separate explicit date field).
 *  - Item code ("Kode Barang/Jasa"), buyer identity, PPnBM, and the
 *    footer "Referensi" code are deliberately NOT extracted (per
 *    requirement decisions).
 */
class FpmPdfParser
{
    /**
     * @throws FpmParseException
     */
    public function parse(string $absolutePdfPath): array
    {
        try {
            $pdf = (new PdfTextParser)->parseFile($absolutePdfPath);
            $text = $pdf->getText();
        } catch (\Throwable $e) {
            throw new FpmParseException('File PDF tidak dapat dibaca / corrupt.');
        }

        $nomorFaktur = $this->extractNomorFaktur($text);
        if (! $nomorFaktur) {
            throw new FpmParseException('Nomor faktur tidak ditemukan pada PDF.');
        }

        $supplier = $this->extractSupplier($text);
        if (! $supplier['npwp']) {
            throw new FpmParseException('Data supplier (NPWP) tidak ditemukan pada PDF.');
        }

        $tanggalFaktur = $this->extractTanggalFaktur($text);
        if (! $tanggalFaktur) {
            throw new FpmParseException('Tanggal faktur tidak ditemukan pada PDF.');
        }

        $items = $this->extractItems($text);
        if (empty($items)) {
            throw new FpmParseException('Detail barang tidak ditemukan pada PDF.');
        }

        $totals = $this->extractTotals($text);

        return [
            'nomor_faktur' => $nomorFaktur,
            'tanggal_faktur' => $tanggalFaktur,
            'supplier' => $supplier,
            'items' => $items,
            'termin' => $totals['termin'],
            'potongan' => $totals['potongan'],
            'uang_muka' => $totals['uang_muka'],
            'dpp' => $totals['dpp'],
            'ppn' => $totals['ppn'],
        ];
    }

    private function extractNomorFaktur(string $text): ?string
    {
        if (preg_match('/Kode dan Nomor Seri Faktur Pajak\s*:\s*([0-9]+)/i', $text, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    private function extractTanggalFaktur(string $text): ?string
    {
        // e.g. "KOTA SEMARANG, 18 Juni 2026"
        if (preg_match('/,\s*(\d{1,2})\s+(\p{L}+)\s+(\d{4})\s*$/mu', $text, $m)) {
            return $this->indonesianDateToIso($m[1], $m[2], $m[3]);
        }

        return null;
    }

    private function indonesianDateToIso(string $day, string $monthName, string $year): ?string
    {
        $months = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
        ];

        $month = $months[mb_strtolower($monthName)] ?? null;
        if (! $month) {
            return null;
        }

        return sprintf('%04d-%02d-%02d', (int) $year, $month, (int) $day);
    }

    private function extractSupplier(string $text): array
    {
        // The "Pengusaha Kena Pajak" block is the seller (supplier) identity.
        preg_match(
            '/Pengusaha Kena Pajak:\s*Nama\s*:\s*(.+?)\s*Alamat\s*:\s*(.+?)\s*NPWP\s*:\s*([0-9]+)/is',
            $text,
            $m
        );

        return [
            'nama' => isset($m[1]) ? trim($m[1]) : null,
            'alamat' => isset($m[2]) ? trim($m[2]) : null,
            'npwp' => $m[3] ?? null,
        ];
    }

    private function extractItems(string $text): array
    {
        // Matches a merged item block, e.g.:
        // "LOLO 40 Feet\nRp 430.000,00 x 1,00 Kegiatan\nPotongan Harga = Rp 0,00"
        preg_match_all(
            '/([A-Za-z0-9 .\/\-]+?)\s*\n?\s*Rp\s*([0-9.,]+)\s*x\s*([0-9.,]+)\s*([A-Za-z]+)\s*\n?\s*Potongan Harga\s*=\s*Rp\s*([0-9.,]+)/u',
            $text,
            $matches,
            PREG_SET_ORDER
        );

        $items = [];
        foreach ($matches as $m) {
            $items[] = [
                'nama_barang' => trim($m[1]),
                'harga_satuan' => $this->toDecimal($m[2]),
                'quantity' => $this->toDecimal($m[3]),
                'satuan' => trim($m[4]),
                // potongan per-item ($m[5]) is not currently stored anywhere
                // separate - header-level "potongan" is what's persisted.
            ];
        }

        return $items;
    }

    private function extractTotals(string $text): array
    {
        return [
            'termin' => $this->extractLabeledAmount($text, 'Harga Jual\s*\/\s*Penggantian\s*\/\s*Uang Muka\s*\/\s*Termin'),
            'potongan' => $this->extractLabeledAmount($text, 'Dikurangi Potongan Harga'),
            'uang_muka' => $this->extractLabeledAmount($text, 'Dikurangi Uang Muka yang telah diterima'),
            'dpp' => $this->extractLabeledAmount($text, 'Dasar Pengenaan Pajak'),
            'ppn' => $this->extractLabeledAmount($text, 'Jumlah PPN[^\\n]*'),
        ];
    }

    private function extractLabeledAmount(string $text, string $labelPattern): ?string
    {
        if (preg_match('/'.$labelPattern.'\s*([0-9.,]+)/iu', $text, $m)) {
            return $this->toDecimal($m[1]);
        }

        // Field genuinely absent on this PDF (e.g. "Uang Muka" often has
        // no value at all) - caller decides whether that's acceptable.
        return null;
    }

    /**
     * Converts Indonesian-formatted number ("430.000,00") to a plain
     * decimal string ("430000.00") safe for the database.
     */
    private function toDecimal(string $value): string
    {
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return $value;
    }
}
