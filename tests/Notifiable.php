<?php

namespace Rich2k\PusherBeams\Test;

class Notifiable
{
    use \Illuminate\Notifications\Notifiable;

    /**
     * @return int
     */
    public function getKey(): int
    {
        return 1;
    }
}
