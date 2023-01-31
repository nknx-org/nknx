<?php

namespace App\Actions\Jetstream;

use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     *
     * @param  mixed  $user
     * @return void
     */
    public function delete($user)
    {
        $user->deleteProfilePhoto();
        $user->tokens->each->delete();
        $user->wallets()->detach();
        $user->nodes()->detach();
        $user->fd_configurations->each->delete();
        $user->gen_id_jobs->each->delete();
        $user->delete();
    }
}
