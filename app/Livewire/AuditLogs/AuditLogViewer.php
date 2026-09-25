<?php

namespace App\Livewire\AuditLogs;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

#[Layout('layouts.app')]
class AuditLogViewer extends Component
{
    use WithPagination;

    public string $filterEvent = '';
    public string $filterSubject = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
    }

    public function updating($property): void
    {
        if (in_array($property, ['filterEvent', 'filterSubject'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $activities = Activity::with(['causer', 'subject'])
            ->when($this->filterEvent, fn ($q) => $q->where('event', $this->filterEvent))
            ->when($this->filterSubject, fn ($q) => $q->where('subject_type', 'like', "%{$this->filterSubject}"))
            ->latest()
            ->paginate(20);

        return view('livewire.audit-logs.audit-log-viewer', [
            'activities' => $activities,
        ]);
    }
}
