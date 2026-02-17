<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class ReportModal extends Component
{
    public $showModal = false;
    public $songId;
    public $title;
    public $content;

    #[On('openReportModal')]
    public function openReportModal($songId = null)
    {
        $this->reset(['title', 'content']);
        $this->songId = $songId;
        $this->showModal = true;
    }

    public function submitReport()
    {
        \Illuminate\Support\Facades\Log::info('submitReport triggered', [
            'songId' => $this->songId,
            'title' => $this->title,
            'user_id' => Auth::id()
        ]);

        if (!Auth::check()) {
            \Illuminate\Support\Facades\Log::warning('Submit failed: User not authenticated');
            $this->dispatch(
                'toast',
                message: 'Please login to send a report',
                type: 'warning'
            );
            return;
        }

        try {
            $this->validate([
                'songId' => 'required|exists:songs,id',
                'title' => 'required|string|max:255',
                'content' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Validation failed', ['errors' => $e->errors()]);
            throw $e;
        }

        try {
            $source = request()->header('Referer') ?? url()->previous() ?? 'unknown';
            $source = substr($source, 0, 255);

            $report = Report::create([
                'song_id' => $this->songId,
                'user_id' => Auth::id(),
                'title' => $this->title,
                'content' => $this->content ?? 'No details provided',
                'source' => $source,
                'status' => Report::STATUS_PENDING,
            ]);

            \Illuminate\Support\Facades\Log::info('Report created successfully', ['id' => $report->id, 'song_id' => $this->songId]);

            $this->showModal = false;
            $this->reset(['songId', 'title', 'content']);

            $this->dispatch(
                'toast',
                message: "Thanks for your report! [ID: #{$report->id} | Song #{$this->songId}]",
                type: 'success'
            );
        } catch (\Throwable $th) {
            $this->dispatch(
                'toast',
                message: 'Failed to submit report: ' . $th->getMessage(),
                type: 'error'
            );
        }
    }

    public function render()
    {
        return view('livewire.report-modal');
    }
}
