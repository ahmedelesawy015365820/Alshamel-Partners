<?php

namespace Modules\ClubMembers\Repositories\CmMemberSetting;

interface CmMemberSettingInterface
{

    public function all($request);

    public function find($id);

    public function create($request);

    public function update($request, $id);

    public function logs($id);

    public function delete($id);

}
