<?php

namespace Webkul\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Customer\Contracts\CustomerLoginLog as CustomerLoginLogContract;
use Webkul\Customer\Models\CustomerProxy;

class CustomerLoginLog extends Model implements CustomerLoginLogContract
{
    protected $table = 'customer_login_logs';

    protected $fillable = [
        'customer_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_name',
        'platform',
        'browser',
        'last_active_at',
        'logged_out_at',
    ];

    protected $appends = ['location'];

    /**
     * Get the customer record associated with the log.
     */
    public function customer()
    {
        return $this->belongsTo(CustomerProxy::modelClass());
    }

    /**
     * Get the geographical location of the IP address.
     * Caches the result forever for each IP to avoid rate limits.
     * 
     * @return string
     */
    public function getLocationAttribute()
    {
        if (empty($this->ip_address) || $this->ip_address === '127.0.0.1' || $this->ip_address === '::1') {
            return 'Локальный хост';
        }

        return \Illuminate\Support\Facades\Cache::rememberForever("geoip_{$this->ip_address}", function () {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(3)->get("http://ip-api.com/json/{$this->ip_address}?lang=ru");
                if ($response->successful()) {
                    $data = $response->json();
                    if ($data['status'] === 'success') {
                        $city = $data['city'] ?? '';
                        $country = $data['country'] ?? '';
                        return trim("{$city}, {$country}", ', ');
                    }
                }
            } catch (\Exception $e) {
                // Return empty string on failure to not break the UI
                return '';
            }
            return '';
        });
    }
}
