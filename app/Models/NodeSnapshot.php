<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\NodeSnapshot
 *
 * @property int $id
 * @property int $node_id
 * @property int $mined
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $mined_amount
 * @property-read \App\Models\Node $node
 * @method static \Illuminate\Database\Eloquent\Builder|NodeSnapshot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NodeSnapshot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NodeSnapshot query()
 * @method static \Illuminate\Database\Eloquent\Builder|NodeSnapshot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NodeSnapshot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NodeSnapshot whereMined($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NodeSnapshot whereMinedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NodeSnapshot whereNodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NodeSnapshot whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class NodeSnapshot extends Model
{

    protected $fillable = ['node_id','mined', 'mined_amount', 'created_at', 'updated_at'];


    public function node()
    {
    	return $this->belongsTo(Node::class);
    }
}
