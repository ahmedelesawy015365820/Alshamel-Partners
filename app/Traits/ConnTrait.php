<?php


namespace App\Traits;

trait ConnTrait
{

    public function __construct ()
    {
        $this->connection = config ('database.connections.gen');
    }

}
