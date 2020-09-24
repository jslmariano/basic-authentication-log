<?php

namespace Jslmariano\AuthenticationLog\Listeners;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Auth\Events\Login;
use Jslmariano\AuthenticationLog\AuthenticationLog;
use Jslmariano\AuthenticationLog\Notifications\NewDevice;

class LogSuccessfulLogin
{
    /**
     * The request.
     *
     * @var \Illuminate\Http\Request
     */
    public $request;

    /**
     * Create the event listener.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        $ip = $this->request->ip();
        $userAgent = $this->request->userAgent();
        $known = $user->authentications()->whereIpAddress($ip)->first();

        $location = null;
        if (config('authentication-log.ipstack_key')) {

            try {
                $ch = curl_init("http://api.ipstack.com/76.65.195.237?access_key=" . config('authentication-log.ipstack_key'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                $data = curl_exec($ch);
                curl_close($ch);

                $data = json_decode($data);

                $location = "$data->city, $data->region_code $data->zip, $data->country_name";

            } catch (\Exception $e) {

            }
        }

        $authenticationLog = new AuthenticationLog([
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'login_at' => Carbon::now(),
            'location' => $location,
        ]);

        $user->authentications()->save($authenticationLog);

        if (!$known && config('authentication-log.notify')) {
            $user->notify(new NewDevice($authenticationLog));
        }
    }
}
