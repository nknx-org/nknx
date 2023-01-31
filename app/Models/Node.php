<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Node
 *
 * @property int $id
 * @property string|null $syncState
 * @property int|null $jsonRpcPort
 * @property string $addr
 * @property int|null $height
 * @property string|null $nodeId
 * @property string|null $publicKey
 * @property int|null $websocketPort
 * @property int|null $relayMessageCount
 * @property int|null $sversion
 * @property string|null $version
 * @property string|null $walletAddress
 * @property int $blocksMined
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $relayPerHour
 * @property int $daily_changes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NodeSnapshot[] $node_snapshots
 * @property-read int|null $node_snapshots_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Node filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Node newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Node newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Node query()
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereAddr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereBlocksMined($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereDailyChanges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereJsonRpcPort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereNodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node wherePublicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereRelayMessageCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereRelayPerHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereSversion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereSyncState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereWalletAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereWebsocketPort($value)
 * @mixin \Eloquent
 */
class Node extends Model
{
    protected $fillable = ['syncState', 'jsonRpcPort', 'addr', 'height', 'nodeId', 'publicKey', 'latestBlockHeight', 'sversion', 'websocketPort', 'relayMessageCount', 'relayPerHour', 'walletAddress','version','daily_changes'];

    protected $hidden = [
        'blocksmined'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('hostname', 'validHostname', 'label', 'notified_offline', 'notified_outdated', 'notified_stuck');
    }
    public function node_snapshots()
    {
        return $this->hasMany(NodeSnapshot::class)->orderBy('updated_at');
    }
    public function getAddrAttribute($value)
    {
        $addr = str_replace('tcp://',"",$value);
        $addr = str_replace(':30001',"",$addr);
        // preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $value, $matches);
        return ($addr);
    }



    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('addr', 'like', '%'.$search.'%')
                      ->orWhere('node_user.label', 'ilike', '%'.$search.'%');
            });
        })->when($filters['syncState'] ?? null, function ($query, $syncState) {
            if ($syncState !== 'ALL'){
                $query->where('syncState', $syncState);
            }
        });
    }

}
