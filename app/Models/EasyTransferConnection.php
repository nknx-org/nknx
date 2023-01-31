<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EasyTransferConnection
 *
 * @property int $id
 * @property int $user_id
 * @property string $ip
 * @property string $endpoint_addr
 * @property string $last_active
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|EasyTransferConnection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EasyTransferConnection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EasyTransferConnection query()
 * @method static \Illuminate\Database\Eloquent\Builder|EasyTransferConnection whereEndpointAddr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EasyTransferConnection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EasyTransferConnection whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EasyTransferConnection whereLastActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EasyTransferConnection whereUserId($value)
 * @mixin \Eloquent
 */
class EasyTransferConnection extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['endpoint_addr', 'user_id','last_active','ip'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
