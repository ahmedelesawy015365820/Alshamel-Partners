<?php

namespace App\Repositories\Serial;

interface SerialRepositoryInterface
{
    public function getAll($request);
    public function setting($request);

    public function find($id);

    public function create(array $data);

    public function getSetting($user_id, $screen_id);
    public function logs($id);

    public function update($request,$id);
    public function delete($id);

    public function getName($request);




}
