<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Wallet
 *
 * @property int $id
 * @property string $address
 * @property float $balance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\WalletSnapshot[] $wallet_snapshots
 * @property-read int|null $wallet_snapshots_count
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet query()
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Wallet extends Model
{

    protected $fillable = [
        'address', 'balance'
    ];

    public function users()
    {
    	return $this->belongsToMany(User::class)->withPivot('label');
    }
    public function wallet_snapshots()
    {
    	return $this->hasMany(WalletSnapshot::class)->orderBy('updated_at');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('address', 'like', '%'.$search.'%')
                      ->orWherePivot('label', 'ilike', '%'.$search.'%');
            });
        });
    }
}
