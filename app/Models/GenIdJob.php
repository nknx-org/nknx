<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GenIdJob
 *
 * @property int $id
 * @property int $user_id
 * @property int $node_id
 * @property string|null $invoice_id
 * @property string $address
 * @property string $ip
 * @property string $status
 * @property string|null $tx
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|GenIdJob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GenIdJob newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GenIdJob query()
 * @method static \Illuminate\Database\Eloquent\Builder|GenIdJob whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GenIdJob whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GenIdJob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GenIdJob whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GenIdJob whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GenIdJob whereNodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GenIdJob whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GenIdJob whereTx($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GenIdJob whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GenIdJob whereUserId($value)
 * @mixin \Eloquent
 */
class GenIdJob extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'node_id', 'address','ip','status'];


    /**
     * Get the user for the notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }



}
