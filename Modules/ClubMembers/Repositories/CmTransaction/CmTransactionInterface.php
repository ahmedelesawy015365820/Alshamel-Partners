<?php

namespace Modules\ClubMembers\Repositories\CmTransaction;

interface CmTransactionInterface
{
    public function all($request);

    public function find($id);

    public function create($request);

    public function update($request, $id);

    public function logs($id);

    public function delete($id);

    public function findCmMemberLastTransaction($id);

    public function reportCmTransactions($request);

    public function reportSponsorPaidTransactions($request);


}
