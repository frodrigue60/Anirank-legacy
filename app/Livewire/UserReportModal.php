<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\UserReport;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class UserReportModal extends Component
{
    public $showModal = false;
    public $reportedUserId;
    public $reason;
    public $content;
    public $isSubmitting = false;

    #[On('openUserReportModal')]
    public function openUserReportModal($reportedUserId = null)
    {
        $this->reset(['reason', 'content', 'isSubmitting']);
        $this->reportedUserId = $reportedUserId;
        $this->showModal = true;
    }

    public function submitReport()
    {
        if ($this->isSubmitting) return;
        $this->isSubmitting = true;

        if (!Auth::check()) {
            $this->dispatch(
                'toast',
                message: 'Please login to send a report',
                type: 'warning'
            );
            $this->isSubmitting = false;
            return;
        }

        if (Auth::id() == $this->reportedUserId) {
            $this->dispatch(
                'toast',
                message: 'You cannot report yourself.',
                type: 'error'
            );
            $this->isSubmitting = false;
            $this->showModal = false;
            return;
        }

        $this->validate([
            'reportedUserId' => 'required|exists:users,id',
            'reason' => 'required|string|max:100',
            'content' => 'required|string',
        ]);

        try {
            $source = request()->header('Referer') ?? url()->previous() ?? 'web';

            UserReport::create([
                'reported_user_id' => $this->reportedUserId,
                'reporter_user_id' => Auth::id(),
                'reason' => $this->reason,
                'content' => $this->content,
                'source' => substr($source, 0, 50),
                'status' => UserReport::STATUS_PENDING,
            ]);

            $this->showModal = false;
            $this->reset(['reportedUserId', 'reason', 'content']);

            $this->dispatch(
                'toast',
                message: "User report submitted successfully.",
                type: 'success'
            );
        } catch (\Throwable $th) {
            $this->dispatch(
                'toast',
                message: 'Failed to submit report: ' . $th->getMessage(),
                type: 'error'
            );
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function render()
    {
        return view('livewire.user-report-modal');
    }
}
