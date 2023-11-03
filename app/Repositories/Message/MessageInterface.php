<?php

namespace App\Repositories\Message;

interface MessageInterface
{

    public function all($request);

    public function logs($id);

    public function find($id);

    public function create($request);

    public function update($request, $id);


    public function delete($id);

}
