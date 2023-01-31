<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BtcPaySubscription
 *
 * @property int $id
 * @property int $user_id
 * @property string $plan_id
 * @property \Illuminate\Support\Carbon|null $trial_ends_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|BtcPaySubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BtcPaySubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BtcPaySubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|BtcPaySubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BtcPaySubscription whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BtcPaySubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BtcPaySubscription wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BtcPaySubscription whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BtcPaySubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BtcPaySubscription whereUserId($value)
 * @mixin \Eloquent
 */
class BtcPaySubscription extends Model
{
    use HasFactory;

    protected $fillable = ['plan_id', 'trial_ends_at','ends_at'];

    protected $dates = [
        'created_at',
        'updated_at',
        'trial_ends_at',
        'ends_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
