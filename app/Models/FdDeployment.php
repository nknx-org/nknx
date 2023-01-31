<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FdDeployment
 *
 * @property int $id
 * @property int $fd_configuration_id
 * @property string $provider
 * @property string|null $label
 * @property string|null $ip
 * @property string $secret
 * @property string $architecture
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FdConfiguration $fd_configuration
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FdEvent[] $fd_events
 * @property-read int|null $fd_events_count
 * @method static \Illuminate\Database\Eloquent\Builder|FdDeployment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FdDeployment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FdDeployment query()
 * @method static \Illuminate\Database\Eloquent\Builder|FdDeployment whereArchitecture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdDeployment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdDeployment whereFdConfigurationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdDeployment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdDeployment whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdDeployment whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdDeployment whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdDeployment whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdDeployment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FdDeployment extends Model
{
    protected $fillable = ['provider', 'label', 'ip', 'secret', 'architecture', 'fd_configurations_id'];


    public function fd_configuration()
    {
        return $this->belongsTo(FdConfiguration::class);
    }
    public function fd_events()
    {
        return $this->hasMany(FdEvent::class);
    }
}
