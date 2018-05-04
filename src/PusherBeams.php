<?php

namespace Neo\PusherBeams;

use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\Notification;
use Pusher\PushNotifications\PushNotifications;
use Illuminate\Notifications\Events\NotificationFailed;

class PusherBeams
{
    /**
     * @var \Pusher\PushNotifications\PushNotifications
     */
    protected $beams;

    /**
     * @param \Pusher\PushNotifications\PushNotifications $beams
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function __construct(PushNotifications $beams, Dispatcher $events)
    {
        $this->beams = $beams;
        $this->events = $events;
    }

    /**
     * @var \Illuminate\Events\Dispatcher
     */
    protected $events;

    /**
     * Send the given notification.
     *
     * @param mixed $notificable
     * @param \Illuminate\Notifications\Notification $notification
     */
    public function send($notifiable, Notification $notification)
    {
        $interest = $notifiable->routeNotificationFor('PusherBeams')
            ?: $this->interestName($notifiable);

        $response = $this->beams->publish(
            [$interest],
            $notification->toPushNotification($notifiable)->toArray()
        );

        if (array_get($response, 'publishId')) {
            $this->events->fire(
                new NotificationFailed($notifiable, $notification, 'pusher-beams', $response)
            );
        }
    }

    /**
     * Get the interest name for the notifiable.
     *
     * @param  string $notifiable
     * @return string
     */
    protected function interestName($notifiable)
    {
        $class = str_replace('\\', '.', get_class($notifiable));

        return $class . '.' . $notifiable->getKey();
    }
}
