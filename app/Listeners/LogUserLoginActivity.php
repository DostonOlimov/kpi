<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use App\Models\LoginActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Request;

class LogUserLoginActivity
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UserLoggedIn  $event
     * @return void
     */
    public function handle(UserLoggedIn $event)
    {
        $userAgent = Request::userAgent();
        
        LoginActivity::create([
            'user_id' => $event->user->id,
            'ip_address' => Request::ip(),
            'user_agent' => $userAgent,
            'browser' => $this->getBrowser($userAgent),
            'platform' => $this->getPlatform($userAgent),
            'device_type' => $this->getDeviceType($userAgent),
            'login_at' => now(),
        ]);
    }

    /**
     * Get browser name from user agent.
     *
     * @param string|null $userAgent
     * @return string
     */
    private function getBrowser($userAgent)
    {
        if (!$userAgent) {
            return 'Unknown';
        }

        if (strpos($userAgent, 'Chrome') !== false && strpos($userAgent, 'Edg') === false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false && strpos($userAgent, 'Chrome') === false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Edg') !== false) {
            return 'Edge';
        } elseif (strpos($userAgent, 'Opera') !== false || strpos($userAgent, 'OPR') !== false) {
            return 'Opera';
        } elseif (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) {
            return 'Internet Explorer';
        }

        return 'Unknown';
    }

    /**
     * Get platform/OS name from user agent.
     *
     * @param string|null $userAgent
     * @return string
     */
    private function getPlatform($userAgent)
    {
        if (!$userAgent) {
            return 'Unknown';
        }

        if (strpos($userAgent, 'Windows') !== false) {
            return 'Windows';
        } elseif (strpos($userAgent, 'Macintosh') !== false || strpos($userAgent, 'Mac OS X') !== false) {
            return 'MacOS';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            return 'Linux';
        } elseif (strpos($userAgent, 'Android') !== false) {
            return 'Android';
        } elseif (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            return 'iOS';
        }

        return 'Unknown';
    }

    /**
     * Get device type from user agent.
     *
     * @param string|null $userAgent
     * @return string
     */
    private function getDeviceType($userAgent)
    {
        if (!$userAgent) {
            return 'Unknown';
        }

        if (strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false || strpos($userAgent, 'iPhone') !== false) {
            return 'Mobile';
        } elseif (strpos($userAgent, 'Tablet') !== false || strpos($userAgent, 'iPad') !== false) {
            return 'Tablet';
        }

        return 'Desktop';
    }
}
