<?php

namespace App\Notifications;

use App\Models\MembershipTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MembershipApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public MembershipTransaction $transaction)
    {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $member = $this->transaction->member;

        return (new MailMessage)
            ->subject('Axiom membership application received')
            ->greeting('Hello '.$member->full_name.',')
            ->line('We received your membership application.')
            ->line('Reference number: '.$this->transaction->reference_no)
            ->line('Status: Pending')
            ->line('Membership level: '.$this->transaction->level->name)
            ->line('Keep this reference number for your records.')
            ->salutation('Axiom Membership');
    }
}
