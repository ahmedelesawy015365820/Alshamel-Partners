<?php

namespace Modules\ClubMembers\Repositories\CmSponser;

interface CmSponserInterface
{

    public function all($request);

    public function find($id);

    public function create($request);

    public function update($request, $id);

    public function logs($id);

    public function delete($id);

    public function getRootNodes();

    public function getChildNodes($id);

}
