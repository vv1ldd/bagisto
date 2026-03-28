<?php

namespace Webkul\Customer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\HasApiTokens;
use Shetabit\Visitor\Traits\Visitor;
use Webkul\Checkout\Models\CartProxy;
use Webkul\Core\Models\ChannelProxy;
use Webkul\Core\Models\SubscribersListProxy;
use Webkul\Customer\Contracts\Customer as CustomerContract;
use Webkul\Customer\Models\CustomerTransaction;
use Webkul\Customer\Models\CustomerTransactionProxy;
use Webkul\Customer\Database\Factories\CustomerFactory;
use Webkul\Product\Models\ProductReviewProxy;
use Webkul\Sales\Models\InvoiceProxy;
use Webkul\Sales\Models\OrderProxy;
use Webkul\Shop\Mail\Customer\ResetPasswordNotification;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Concerns\InteractsWithPasskeys;
use Webkul\Customer\Models\CustomerBalanceProxy;
use Webkul\Customer\Services\ExchangeRateService;
use Webkul\Customer\Models\HandshakeProxy;

class Customer extends Authenticatable implements CustomerContract, HasPasskeys
{
    use HasApiTokens, HasFactory, Notifiable, Visitor, InteractsWithPasskeys;

    /**
     * Get the name for the passkey.
     * Overriding default implementation from InteractsWithPasskeys to support null emails.
     */
    public function getPasskeyName(): string
    {
        return $this->email ?? $this->username;
    }

    /**
     * Get the ID for the passkey.
     * Overriding to support transient/unsaved users during registration flow.
     */
    public function getPasskeyId(): string
    {
        return (string) ($this->credits_id ?? $this->id ?? $this->transient_passkey_id ?? '');
    }


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customers';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'subscribed_to_news_letter' => 'boolean',
        'balance' => 'decimal:4',
        'is_investor' => 'boolean',
        'is_call_enabled' => 'boolean',
        'is_b2b_enabled' => 'boolean',
        'is_crypto_enabled' => 'boolean',
        'mnemonic_verified_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'gender',
        'date_of_birth',
        'birth_city',
        'country_of_residence',
        'citizenship',
        'email',
        'phone',
        'password',
        'api_token',
        'token',
        'customer_group_id',
        'channel_id',
        'subscribed_to_news_letter',
        'status',
        'is_verified',
        'is_suspended',
        'is_investor',
        'is_call_enabled',
        'last_login_ip',
        'verification_code',
        'registration_ip',
        'balance',
        'credits_id',
        'credits_alias',
        'is_b2b_enabled',
        'is_crypto_enabled',
        'mnemonic_hash',
        'encrypted_private_key',
        'mnemonic_verified_at',
        'public_key',
        'public_key_hash',
        'telegram_chat_id',
        'telegram_token',
        'matrix_user_id',
        'matrix_access_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'api_token',
        'remember_token',
        'encrypted_private_key',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url'];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get image url for the customer profile.
     *
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        return $this->image_url();
    }

    /**
     * Get the customer full name.
     */
    public function getNameAttribute(): string
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    /**
     * Get image url for the customer image.
     *
     * @return string|null
     */
    public function image_url()
    {
        if (!$this->image) {
            return;
        }

        return Storage::url($this->image);
    }

    /**
     * Is email exists or not.
     *
     * @param  string  $email
     */
    public function emailExists($email): bool
    {
        $results = $this->where('email', $email);

        if ($results->count() === 0) {
            return false;
        }

        return true;
    }

    /**
     * Get the customer group that owns the customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(CustomerGroupProxy::modelClass(), 'customer_group_id');
    }

    /**
     * Get the customer address that owns the customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(CustomerAddressProxy::modelClass(), 'customer_id');
    }

    /**
     * Get the customer organizations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function organizations()
    {
        return $this->hasMany(OrganizationProxy::modelClass(), 'customer_id');
    }

    /**
     * Get default customer address that owns the customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function default_address()
    {
        return $this->hasOne(CustomerAddressProxy::modelClass(), 'customer_id')
            ->where('default_address', 1);
    }

    /**
     * Customer's relation with invoice .
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasManyThrough
     */
    public function invoices()
    {
        return $this->hasManyThrough(InvoiceProxy::modelClass(), OrderProxy::modelClass());
    }

    /**
     * Customer's relation with wishlist items.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wishlist_items()
    {
        return $this->hasMany(WishlistProxy::modelClass(), 'customer_id');
    }

    /**
     * Is wishlist shared by the customer.
     */
    public function isWishlistShared(): bool
    {
        return (bool) $this->wishlist_items()->where('shared', 1)->first();
    }

    /**
     * Get wishlist shared link.
     *
     * @return string|null
     */
    public function getWishlistSharedLink()
    {
        return $this->isWishlistShared()
            ? URL::signedRoute('shop.customer.wishlist.shared', ['id' => $this->id])
            : null;
    }

    /**
     * Get all cart inactive cart instance of a customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function all_carts()
    {
        return $this->hasMany(CartProxy::modelClass(), 'customer_id');
    }

    /**
     * Get inactive cart instance of a customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inactive_carts()
    {
        return $this->hasMany(CartProxy::modelClass(), 'customer_id')
            ->where('is_active', 0);
    }

    /**
     * Get active cart instance of a customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function active_carts()
    {
        return $this->hasMany(CartProxy::modelClass(), 'customer_id')
            ->where('is_active', 1);
    }

    /**
     * Get all orders of a customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(OrderProxy::modelClass(), 'customer_id');
    }

    /**
     * Get all reviews of a customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(ProductReviewProxy::modelClass(), 'customer_id');
    }

    /**
     * Get all notes of a customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany(CustomerNoteProxy::modelClass(), 'customer_id');
    }

    /**
     * Get all credits of a customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function credits()
    {
        return $this->hasMany(CustomerTransactionProxy::modelClass(), 'customer_id');
    }

    /**
     * Get all native crypto balances of a customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function balances()
    {
        return $this->hasMany(CustomerBalanceProxy::modelClass(), 'customer_id');
    }

    /**
     * Calculate the total fiat value of all native crypto balances based on live exchange rates.
     *
     * @return float
     */
    public function getTotalFiatBalance(): float
    {
        $totalFiat = 0.0;
        $exchangeRateService = app(ExchangeRateService::class);

        foreach ($this->balances as $balance) {
            $rate = $exchangeRateService->getRate($balance->currency_code);
            $totalFiat += ($balance->amount * $rate);
        }

        return $totalFiat;
    }

    /**
     * Get all crypto addresses of a customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function crypto_addresses()
    {
        return $this->hasMany(CryptoAddressProxy::modelClass(), 'customer_id');
    }

    /**
     * Get all crypto transactions of a customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function crypto_transactions()
    {
        return $this->hasMany(CryptoTransactionProxy::modelClass(), 'customer_id');
    }

    /**
     * Get the customer's subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function subscription()
    {
        return $this->hasOne(SubscribersListProxy::modelClass(), 'customer_id');
    }

    /**
     * Get all handshakes initiated by this customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function handshakesSent()
    {
        return $this->hasMany(HandshakeProxy::modelClass(), 'sender_id');
    }

    /**
     * Get all handshakes received by this customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function handshakesReceived()
    {
        return $this->hasMany(HandshakeProxy::modelClass(), 'receiver_id');
    }

    /**
     * Get all handshakes (both sent and received).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function handshakes()
    {
        return $this->handshakesSent->merge($this->handshakesReceived);
    }

    /**
     * Check if a handshake exists with another customer.
     *
     * @param  int  $customerId
     * @return \Webkul\Customer\Models\Handshake|null
     */
    public function getHandshakeWith($customerId)
    {
        return HandshakeProxy::where(function ($query) use ($customerId) {
            $query->where('sender_id', $this->id)
                ->where('receiver_id', $customerId);
        })->orWhere(function ($query) use ($customerId) {
            $query->where('sender_id', $customerId)
                ->where('receiver_id', $this->id);
        })->first();
    }

    /**
     * Get the channel that owns the customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(ChannelProxy::modelClass(), 'channel_id');
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Webkul\Customer\Database\Factories\CustomerFactory
     */
    protected static function newFactory()
    {
        return CustomerFactory::new();
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($customer) {
            if (!$customer->credits_id) {
                $customer->credits_id = static::generateUniqueCreditsId();
            }

            if (!$customer->username) {
                $customer->username = static::generateUniqueCreditsAlias();
            }

            if (!$customer->credits_alias) {
                $customer->credits_alias = $customer->username;
            }
        });

        static::saving(function ($customer) {
            if (!$customer->credits_id) {
                $customer->credits_id = static::generateUniqueCreditsId();
            }

            if (!$customer->username) {
                $customer->username = static::generateUniqueCreditsAlias();
            }

            if ($customer->isDirty('username') && empty($customer->credits_alias)) {
                $customer->credits_alias = $customer->username;
            }

            // Sync credits_id to CryptoAddress record for background syncing
            if ($customer->id && $customer->isDirty('credits_id') && !empty($customer->credits_id)) {
                $customer->crypto_addresses()->updateOrCreate(
                    ['network' => 'arbitrum_one'],
                    [
                        'address'     => $customer->credits_id,
                        'is_active'   => true,
                        'verified_at' => now(), // Trusted since we generated it
                    ]
                );
            }
        });

        static::created(function ($customer) {
            // Ensure CryptoAddress is created for new customers with credits_id
            if (!empty($customer->credits_id)) {
                $customer->crypto_addresses()->updateOrCreate(
                    ['network' => 'arbitrum_one'],
                    [
                        'address'     => $customer->credits_id,
                        'is_active'   => true,
                        'verified_at' => now(),
                    ]
                );
            }
        });
    }

    /**
     * Generate a unique credits ID.
     */
    public static function generateUniqueCreditsId(): string
    {
        do {
            $id = '0x' . bin2hex(random_bytes(20));
        } while (static::where('credits_id', $id)->exists());

        return $id;
    }

    /**
     * Generate a unique credits alias.
     */
    public static function generateUniqueCreditsAlias(): string
    {
        do {
            $alias = 'u_' . strtolower(bin2hex(random_bytes(5)));
        } while (static::where('username', $alias)->exists());

        return $alias;
    }

    /**
     * Get the total amount of credits (balance) in the system across all customers.
     */
    public static function getTotalSystemCredits(): float
    {
        return (float) static::sum('balance');
    }

    /**
     * Generate a new telegram token for linking.
     */
    public function generateTelegramToken(): string
    {
        $token = \Illuminate\Support\Str::random(64);
        
        $this->update(['telegram_token' => $token]);

        return $token;
    }

    /**
     * Get the telegram link for deep linking.
     */
    public function getTelegramLinkAttribute(): string
    {
        $botUsername = config('services.telegram.bot_username', 'MeanlyBot');
        
        return "https://t.me/{$botUsername}?start={$this->telegram_token}";
    }

    /**
     * Get the Matrix user ID for the customer.
     */
    public function getMatrixIdAttribute(): string
    {
        $domain = config('services.matrix.homeserver_domain', 'meanly.ru');
        
        return "@{$this->credits_id}:{$domain}";
    }
}
