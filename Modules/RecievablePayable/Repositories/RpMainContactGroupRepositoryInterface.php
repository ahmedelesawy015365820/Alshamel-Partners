<?php

namespace Modules\RecievablePayable\Repositories;

interface RpMainContactGroupRepositoryInterface
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
