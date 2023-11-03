<?php
namespace Modules\RecievablePayable\Repositories;


use App\Models\UserSettingScreen;
use Illuminate\Support\Facades\DB;
use Modules\RecievablePayable\Entities\RpInstallmentCondation;
use Modules\RecievablePayable\Entities\RpInstallmentStatus;

interface RpInstallmentCondationRepositoryInterface
{
    public function all($request);

    public function logs($id);

    public function find($id);

    public function create($request);

    public function update($request, $id);

    public function delete($id);

    public function setting($request);

    public function getSetting($user_id, $screen_id);

}
