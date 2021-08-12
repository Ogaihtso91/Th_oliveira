<?php

namespace App\Observers;

use App\SocialTecnologyImage;
use App\Filesystem\Storage;

class SocialTecnologyImageObserver
{
    /**
     * Handle the social tecnologies images "created" event.
     *
     * @param  \App\SocialTecnologyImage  $socialTecnologiesImages
     * @return void
     */
    public function created(SocialTecnologyImage $socialTecnologiesImages)
    {
        //
    }

    /**
     * Handle the social tecnologies images "updated" event.
     *
     * @param  \App\SocialTecnologyImage  $socialTecnologiesImages
     * @return void
     */
    public function updated(SocialTecnologyImage $socialTecnologiesImages)
    {
        //
    }

    /**
     * Handle the social tecnologies images "deleted" event.
     *
     * @param  \App\SocialTecnologyImage  $socialTecnologiesImages
     * @return void
     */
    public function deleted(SocialTecnologyImage $socialTecnologiesImages)
    {
        // Delete file from storage
        Storage::delete('socialtecnologies/'.$socialTecnologiesImages->socialtecnology_id.'/images/'.$socialTecnologiesImages->image);
    }

    /**
     * Handle the social tecnologies images "restored" event.
     *
     * @param  \App\SocialTecnologyImage  $socialTecnologiesImages
     * @return void
     */
    public function restored(SocialTecnologyImage $socialTecnologiesImages)
    {
        //
    }

    /**
     * Handle the social tecnologies images "force deleted" event.
     *
     * @param  \App\SocialTecnologyImage  $socialTecnologiesImages
     * @return void
     */
    public function forceDeleted(SocialTecnologyImage $socialTecnologiesImages)
    {
        //
    }
}
