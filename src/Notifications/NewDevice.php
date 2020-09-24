<?php

namespace Jslmariano\AuthenticationLog\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Jslmariano\AuthenticationLog\AuthenticationLog;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Messages\SlackMessage;

class NewDevice extends Notification
{
    use Queueable;

    /**
     * The authentication log.
     *
     * @var \Jslmariano\AuthenticationLog\AuthenticationLog
     */
    public $authenticationLog;

    /**
     * Create a new notification instance.
     *
     * @param  \Jslmariano\AuthenticationLog\AuthenticationLog  $authenticationLog
     * @return void
     */
    public function __construct(AuthenticationLog $authenticationLog)
    {
        $this->authenticationLog = $authenticationLog;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $notifiable->notifyAuthenticationLogVia();
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $fields = [
            'account' => $notifiable,
            'time' => $this->authenticationLog->login_at,
            'ipAddress' => $this->authenticationLog->ip_address,
            'browser' => $this->authenticationLog->user_agent,
        ];

        if ($this->authenticationLog->location) {
            $fields['location'] = $this->authenticationLog->location;
        }

        return (new MailMessage)
            ->subject(trans('authentication-log::messages.subject'))
            ->markdown('authentication-log::emails.new', $fields);
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        $fields = [
            'Email' => $notifiable->email,
            'Time' => $this->authenticationLog->login_at->toCookieString(),
            'IP Address' => $this->authenticationLog->ip_address,
            'Browser' => $this->authenticationLog->user_agent,
        ];

        if ($this->authenticationLog->location) {
            $fields['Location'] = $this->authenticationLog->location;
        }

        return (new SlackMessage)
            ->from(config('app.name'))
            ->warning()
            ->content(trans('authentication-log::messages.content', ['app' => config('app.name')]))
            ->attachment(function ($attachment) use ($notifiable, $fields) {
                $attachment->fields($fields);
            });
    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\NexmoMessage
     */
    public function toNexmo($notifiable)
    {
        return (new NexmoMessage)
            ->content(trans('authentication-log::messages.content', ['app' => config('app.name')]));
    }
}