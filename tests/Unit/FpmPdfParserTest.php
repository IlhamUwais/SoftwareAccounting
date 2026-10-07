<?php

namespace Tests\Unit;

use App\Services\FpmPdfParser;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Regression tests for the two real parser bugs found during the security
 * audit: the PPN/amount regex only capturing the last digit on
 * space-separated (not tab-separated) label/value pairs, and supplier
 * addresses retaining raw embedded newlines from the PDF text layer.
 */
class FpmPdfParserTest extends TestCase
{
    private function extractLabeledAmount(string $text, string $labelPattern): ?string
    {
        $parser = new FpmPdfParser();
        $method = new ReflectionMethod(FpmPdfParser::class, 'extractLabeledAmount');
        $method->setAccessible(true);

        return $method->invoke($parser, $text, $labelPattern);
    }

    public function test_extracts_full_amount_when_label_and_value_are_tab_separated(): void
    {
        $text = "Jumlah PPN\t54.120,00\n";

        $this->assertSame('54120.00', $this->extractLabeledAmount($text, 'Jumlah PPN[^\\n]*'));
    }

    public function test_extracts_full_amount_when_label_and_value_are_space_separated(): void
    {
        // Previously the greedy regex matched too much of the gap before
        // the number and only captured the final digit ("0" instead of
        // "54120.00") when the PDF text layer used spaces instead of a
        // tab to align the amount column.
        $text = "Jumlah PPN  54.120,00\n";

        $this->assertSame('54120.00', $this->extractLabeledAmount($text, 'Jumlah PPN[^\\n]*'));
    }

    public function test_returns_null_when_label_not_found(): void
    {
        $this->assertNull($this->extractLabeledAmount('tidak ada apa-apa', 'Jumlah PPN[^\\n]*'));
    }

    public function test_extract_supplier_normalizes_embedded_newlines_in_address(): void
    {
        $parser = new FpmPdfParser();
        $method = new ReflectionMethod(FpmPdfParser::class, 'extractSupplier');
        $method->setAccessible(true);

        $text = "Pengusaha Kena Pajak:\nNama : PT Contoh Supplier\nAlamat : Jl. Contoh No. 1\nBlok A\nKelurahan X\nNPWP : 01234567890123456789\n";

        $result = $method->invoke($parser, $text);

        $this->assertArrayHasKey('alamat', $result);
        $this->assertNotNull($result['alamat']);
        $this->assertStringNotContainsString("\n", $result['alamat']);
    }
}
