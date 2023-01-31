<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FdEvent
 *
 * @property int $id
 * @property int $fd_deployment_id
 * @property int $user_id
 * @property string $event_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FdDeployment $fd_deployment
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|FdEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FdEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FdEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|FdEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdEvent whereEventCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdEvent whereFdDeploymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdEvent whereUserId($value)
 * @mixin \Eloquent
 */
class FdEvent extends Model
{
    protected $fillable = ['event_code', 'fd_deployments_id','user_id'];


    public function fd_deployment()
    {
        return $this->belongsTo(FdDeployment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
