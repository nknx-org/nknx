<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SshKey
 *
 * @property int $id
 * @property string $name
 * @property string $pubkey
 * @property string $fingerprint
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|SshKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SshKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SshKey query()
 * @method static \Illuminate\Database\Eloquent\Builder|SshKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SshKey whereFingerprint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SshKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SshKey whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SshKey wherePubkey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SshKey whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SshKey whereUserId($value)
 * @mixin \Eloquent
 */
class SshKey extends Model
{
    protected $fillable = [
        'name',
        'pubkey',
        'fingerprint'
    ];

    /**
     * Get the user for the notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
