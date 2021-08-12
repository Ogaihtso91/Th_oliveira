<?php

namespace App\Observers;

use App\SocialTecnologyFile;
use App\Filesystem\Storage;

class SocialTecnologyFileObserver
{
    /**
     * Handle the social tecnologies files "created" event.
     *
     * @param  \App\SocialTecnologyFile  $socialTecnologiesFiles
     * @return void
     */
    public function created(SocialTecnologyFile $socialTecnologiesFiles)
    {
        //
    }

    /**
     * Handle the social tecnologies files "updated" event.
     *
     * @param  \App\SocialTecnologyFile  $socialTecnologiesFiles
     * @return void
     */
    public function updated(SocialTecnologyFile $socialTecnologiesFiles)
    {
        //
    }

    /**
     * Handle the social tecnologies files "deleted" event.
     *
     * @param  \App\SocialTecnologyFile  $socialTecnologiesFiles
     * @return void
     */
    public function deleted(SocialTecnologyFile $socialTecnologiesFiles)
    {
        // Delete file from storage
        Storage::delete('socialtecnologies/'.$socialTecnologiesFiles->socialtecnology_id.'/files/'.$socialTecnologiesFiles->file);
    }

    /**
     * Handle the social tecnologies files "restored" event.
     *
     * @param  \App\SocialTecnologyFile  $socialTecnologiesFiles
     * @return void
     */
    public function restored(SocialTecnologyFile $socialTecnologiesFiles)
    {
        //
    }

    /**
     * Handle the social tecnologies files "force deleted" event.
     *
     * @param  \App\SocialTecnologyFile  $socialTecnologiesFiles
     * @return void
     */
    public function forceDeleted(SocialTecnologyFile $socialTecnologiesFiles)
    {
        //
    }
}
