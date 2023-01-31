<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\WalletSnapshot
 *
 * @property int $id
 * @property int $wallet_id
 * @property float $balance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Wallet $wallet
 * @method static \Illuminate\Database\Eloquent\Builder|WalletSnapshot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WalletSnapshot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WalletSnapshot query()
 * @method static \Illuminate\Database\Eloquent\Builder|WalletSnapshot whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletSnapshot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletSnapshot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletSnapshot whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletSnapshot whereWalletId($value)
 * @mixin \Eloquent
 */
class WalletSnapshot extends Model
{

    protected $fillable = ['wallet_id', 'created_at', 'balance', 'updated_at'];

    public function wallet()
    {
    	return $this->belongsTo(Wallet::class);
    }
}
