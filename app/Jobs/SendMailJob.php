<?php

namespace App\Jobs;

use App\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $type;
    public $params;

    public function __construct(string $type, array $params)
    {
        $this->type = $type;
        $this->params = $params;
    }

    public function handle(MailService $mailService): void
    {
        if ($this->type === 'welcome') {
            $mailService->sendWelcome($this->params['email'], $this->params['name']);
        } elseif ($this->type === 'booking') {
            $mailService->sendBookingConfirmation($this->params['user'], $this->params['booking']);
        } elseif ($this->type === 'reset') {
            $mailService->sendPasswordReset($this->params['email'], $this->params['name'], $this->params['resetUrl']);
        }
    }
}
