<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\BTCPay\Invoice;

use Carbon\Carbon;

use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;


use Illuminate\Support\Facades\Log;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property bool $is_admin
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $profile_photo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property int $fd_count
 * @property array $notificationPreferences
 * @property bool $blocked_gen_payment
 * @property int $failed_payments_counter
 * @property-read \App\Models\BtcPaySubscription|null $btc_pay_subscription
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EasyTransferConnection[] $easy_transfer_connections
 * @property-read int|null $easy_transfer_connections_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FdConfiguration[] $fd_configurations
 * @property-read int|null $fd_configurations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FdEvent[] $fd_events
 * @property-read int|null $fd_events_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GenIdJob[] $gen_id_jobs
 * @property-read int|null $gen_id_jobs_count
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Node[] $nodes
 * @property-read int|null $nodes_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SshKey[] $ssh_keys
 * @property-read int|null $ssh_keys_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\VpsKey[] $vps_keys
 * @property-read int|null $vps_keys_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Wallet[] $wallets
 * @property-read int|null $wallets_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBlockedGenPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFailedPaymentsCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFdCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotificationPreferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'vat_id',
        'billing_address',
        'billing_address_line_2',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'billing_country'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'notificationPreferences' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];


    public function nodes()
    {
        return $this->belongsToMany(Node::class)->withPivot('hostname', 'validHostname', 'label', 'notified_offline', 'notified_outdated', 'notified_stuck');
    }
    public function wallets()
    {
        return $this->belongsToMany(Wallet::class)->withPivot('label');
    }

    public function vps_keys()
    {
        return $this->hasMany(VpsKey::class);
    }

    public function fd_configurations()
    {
        return $this->hasMany(FdConfiguration::class);
    }

    public function fd_events()
    {
        return $this->hasMany(FdEvent::class);
    }

    public function ssh_keys()
    {
        return $this->hasMany(SshKey::class);
    }

    public function gen_id_jobs()
    {
        return $this->hasMany(GenIdJob::class);
    }

    public function easy_transfer_connections()
    {
        return $this->hasMany(EasyTransferConnection::class);
    }

    public function btc_pay_subscription()
    {
        return $this->hasOne(BtcPaySubscription::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }


    public function createInvoiceForProduct(string $name, string $price, array $metadata = [])
    {
        $client = new Invoice();
        $invoice = $client->createInvoiceForProduct($name, $this, $price, $metadata);
        return $invoice;
    }

}
