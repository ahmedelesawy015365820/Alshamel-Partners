<?php

namespace Modules\ClubMembers\Repositories\CmMember;

interface CmMemberInterface
{

    public function all($request);

    public function find($id);

    public function create($request);

    public function update($request, $id);

    public function logs($id);

    public function delete($id);

    public function allAcceptancePending($request);

    public function updateAcceptance($request, $id);

    public function updateDecline($request, $id);

    public function updateSponsor($request, $sponsor_id);

    public function allAcceptance($request);

    public function acceptMembers($request);

    public function reportCmMember($request);


    public function getSponsors($request);

    public function reportToMembers($request);


    public function updateLastTransactionDate();

    public function publicUpdatePermissionCmMember($id);

    public function updateCmMember();

    public function getMemberForMultiSubscription($request);
}
