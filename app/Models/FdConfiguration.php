<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FdConfiguration
 *
 * @property int $id
 * @property int $user_id
 * @property string $uuid
 * @property string|null $label
 * @property string $beneficiary_addr
 * @property bool $download_chain
 * @property bool $enable_web_ui
 * @property bool $disable_ufw
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $send_email
 * @property bool $fast_sync
 * @property bool $light_sync
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FdDeployment[] $fd_deployments
 * @property-read int|null $fd_deployments_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|FdConfiguration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FdConfiguration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FdConfiguration query()
 * @method static \Illuminate\Database\Eloquent\Builder|FdConfiguration whereBeneficiaryAddr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdConfiguration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdConfiguration whereDisableUfw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdConfiguration whereDownloadChain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdConfiguration whereEnableWebUi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdConfiguration whereFastSync($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdConfiguration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdConfiguration whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdConfiguration whereLightSync($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdConfiguration whereSendEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdConfiguration whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdConfiguration whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FdConfiguration whereUuid($value)
 * @mixin \Eloquent
 */
class FdConfiguration extends Model
{
    protected $fillable = ['uuid', 'label', 'beneficiary_addr', 'download_chain', 'fast_sync', 'light_sync', 'enable_web_ui', 'disable_ufw', 'send_email', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function fd_deployments()
    {
        return $this->hasMany(FdDeployment::class);
    }
}
