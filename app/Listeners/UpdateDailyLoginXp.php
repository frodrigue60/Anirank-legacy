<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Services\XpService;

class UpdateDailyLoginXp
{
    protected $xpService;

    /**
     * Create the event listener.
     */
    public function __construct(XpService $xpService)
    {
        $this->xpService = $xpService;
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $this->xpService->award($event->user, 'daily_login');
    }
}
