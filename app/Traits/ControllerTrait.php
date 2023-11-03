<?php

namespace App\Traits;

trait ControllerTrait
{

    public function create()
    {
        return $this->repository->create($this->request->all());
    }

}
