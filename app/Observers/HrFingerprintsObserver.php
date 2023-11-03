<?php

namespace App\Observers;

use Modules\HR\Entities\Fingerprint;

use App\Traits\sendMessage;


class HrFingerprintsObserver
{
    use sendMessage;
    
    /**
     * Handle the Fingerprint "created" event.
     *
     * @param  \App\Models\Fingerprint  $fingerprint
     * @return void
     */
    public function created(Fingerprint $fingerprint)
    {
       
        $notHandledFingerprints = Fingerprint::where('handle_type', 'N')->get();

        

        $this->sendMessage($notHandledFingerprints);
    }

    /**
     * Handle the Fingerprint "updated" event.
     *
     * @param  \App\Models\Fingerprint  $fingerprint
     * @return void
     */
    public function updated(Fingerprint $fingerprint)
    {
        //
    }

    /**
     * Handle the Fingerprint "deleted" event.
     *
     * @param  \App\Models\Fingerprint  $fingerprint
     * @return void
     */
    public function deleted(Fingerprint $fingerprint)
    {
        //
    }

    /**
     * Handle the Fingerprint "restored" event.
     *
     * @param  \App\Models\Fingerprint  $fingerprint
     * @return void
     */
    public function restored(Fingerprint $fingerprint)
    {
        //
    }

    /**
     * Handle the Fingerprint "force deleted" event.
     *
     * @param  \App\Models\Fingerprint  $fingerprint
     * @return void
     */
    public function forceDeleted(Fingerprint $fingerprint)
    {
        //
    }
}
