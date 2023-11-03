<?php

namespace App\Repositories\DatabaseBackup;

interface DatabaseBackupInterface
{

    public function all($request);

    public function find($id);

    public function create($request);

    public function logs($id);

    public function delete($id);

    // public function storeBackupPath(string $path): void;

}
