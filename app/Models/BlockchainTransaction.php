<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

/**
 * App\Models\BlockchainTransaction
 *
 * @property int $id
 * @property string $hash
 * @property string $txType
 * @property int $block_height
 * @property string|null $senderWallet
 * @property string|null $recipientWallet
 * @property int|null $reward
 * @property string|null $signerPk
 * @property string $added_at
 * @property string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|BlockchainTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlockchainTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlockchainTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|BlockchainTransaction whereAddedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockchainTransaction whereBlockHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockchainTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockchainTransaction whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockchainTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockchainTransaction whereRecipientWallet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockchainTransaction whereReward($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockchainTransaction whereSenderWallet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockchainTransaction whereSignerPk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockchainTransaction whereTxType($value)
 * @mixin \Eloquent
 */
class BlockchainTransaction extends Model
{
    use HasFactory;
    protected $fillable = ['hash', 'txType','block_height','senderWallet','recipientWallet','reward', 'signerPk','created_at'];
    protected $hidden = ['added_at'];
    public $timestamps = false;

    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = Carbon::createFromTimestamp($value)->toDateTimeString();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->added_at = $model->freshTimestamp();
        });
    }

}
