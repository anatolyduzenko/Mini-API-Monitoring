<?php

namespace App\Notifications;

use App\Models\Endpoint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EndpointFailed extends Notification implements ShouldQueue
{
    use Queueable;

    protected Endpoint $endpoint;

    /**
     * Create a new notification instance.
     */
    public function __construct(Endpoint $endpoint)
    {
        $this->endpoint = $endpoint;
        $this->onConnection('redis');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("API DOWN: {$this->endpoint->name}")
            ->line("The API endpoint `{$this->endpoint->url}` failed during monitoring.")
            ->line("Method: {$this->endpoint->method}")
            ->line('Please investigate the issue.')
            ->action('View Dashboard', url('/dashboard'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
