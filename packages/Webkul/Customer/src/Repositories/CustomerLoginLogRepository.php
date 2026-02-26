<?php

namespace Webkul\Customer\Repositories;

use Webkul\Core\Eloquent\Repository;

class CustomerLoginLogRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return 'Webkul\Customer\Models\CustomerLoginLog';
    }

    /**
     * Log customer login.
     *
     * @param  \Webkul\Customer\Models\Customer  $customer
     * @return void
     */
    public function log($customer)
    {
        $userAgent = request()->userAgent();

        $currentIp = request()->header('CF-Connecting-IP')
            ?? request()->header('X-Forwarded-For')
            ?? request()->header('X-Real-IP')
            ?? request()->ip();

        if (str_contains($currentIp, ',')) {
            $currentIp = trim(explode(',', $currentIp)[0]);
        }

        $this->create([
            'customer_id' => $customer->id,
            'session_id' => session()->getId(),
            'ip_address' => $currentIp,
            'user_agent' => $userAgent,
            'device_name' => $this->getDeviceName($userAgent),
            'platform' => $this->getPlatform($userAgent),
            'browser' => $this->getBrowser($userAgent),
            'logged_at' => now(),
        ]);
    }

    /**
     * Get platform from user agent.
     */
    protected function getPlatform($userAgent)
    {
        if (stripos($userAgent, 'windows') !== false)
            return 'Windows';
        if (stripos($userAgent, 'ipad') !== false)
            return 'iPad';
        if (stripos($userAgent, 'iphone') !== false)
            return 'iPhone';
        if (stripos($userAgent, 'macintosh') !== false)
            return 'macOS';
        if (stripos($userAgent, 'android') !== false)
            return 'Android';
        if (stripos($userAgent, 'linux') !== false)
            return 'Linux';
        return 'Unknown';
    }

    /**
     * Get browser from user agent.
     */
    protected function getBrowser($userAgent)
    {
        if (stripos($userAgent, 'edge') !== false)
            return 'Edge';
        if (stripos($userAgent, 'opera') !== false || stripos($userAgent, 'opr') !== false)
            return 'Opera';
        if (stripos($userAgent, 'chrome') !== false)
            return 'Chrome';
        if (stripos($userAgent, 'safari') !== false)
            return 'Safari';
        if (stripos($userAgent, 'firefox') !== false)
            return 'Firefox';
        if (stripos($userAgent, 'msie') !== false || stripos($userAgent, 'trident') !== false)
            return 'IE';
        return 'Unknown';
    }

    /**
     * Get device name from user agent.
     */
    protected function getDeviceName($userAgent)
    {
        if (stripos($userAgent, 'iphone') !== false)
            return 'iPhone';
        if (stripos($userAgent, 'ipad') !== false)
            return 'iPad';
        if (stripos($userAgent, 'macintosh') !== false)
            return 'Mac';
        if (stripos($userAgent, 'android') !== false)
            return 'Android Device';
        if (stripos($userAgent, 'windows') !== false)
            return 'Windows PC';
        return 'Device';
    }
}
