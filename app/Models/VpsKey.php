<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VpsKey
 *
 * @property int $id
 * @property int $user_id
 * @property string $provider
 * @property string $profile_name
 * @property string $api_token
 * @property string|null $api_secret
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|VpsKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VpsKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VpsKey query()
 * @method static \Illuminate\Database\Eloquent\Builder|VpsKey whereApiSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VpsKey whereApiToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VpsKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VpsKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VpsKey whereProfileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VpsKey whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VpsKey whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VpsKey whereUserId($value)
 * @mixin \Eloquent
 */
class VpsKey extends Model
{
    protected $fillable = ['provider', 'profile_name', 'api_token', 'api_secret', 'user_id'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
