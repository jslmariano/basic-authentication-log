<?php

namespace Jslmariano\AuthenticationLog;

trait EventMap
{
    /**
     * The Authentication Log event / listener mappings.
     *
     * @var array
     */
    protected $events = [
        'Illuminate\Auth\Events\Login' => [
            'Jslmariano\AuthenticationLog\Listeners\LogSuccessfulLogin',
        ],

        'Illuminate\Auth\Events\Logout' => [
            'Jslmariano\AuthenticationLog\Listeners\LogSuccessfulLogout',
        ],
    ];
}
