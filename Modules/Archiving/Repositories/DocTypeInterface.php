<?php

namespace Modules\Archiving\Repositories;

interface DocTypeInterface
{

    public function all($request);

    public function find($id);

    public function logs($id);

    public function create($request);

    public function update($request, $id);

    public function delete($id);

    public function documentStatusExist($documentStatusId, $docTypeId);

    public function removeStatusFromDocumentType($documentStatusId, $docTypeId);

    public function addStatusToDocumentType($request);

    public function tree();

    public function nodesLevelTwo($request);

}
