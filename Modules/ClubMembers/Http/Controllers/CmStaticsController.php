<?php

namespace Modules\ClubMembers\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\ClubMembers\Entities\CmFinancialStatus;
use Modules\ClubMembers\Entities\CmMember;
use Modules\ClubMembers\Entities\CmMemberPermission;
use Modules\ClubMembers\Entities\CmMemberRequest;
use Modules\ClubMembers\Entities\CmMemberType;
use Modules\ClubMembers\Entities\CmSponser;
use Modules\ClubMembers\Entities\CmSponsorGroup;

class CmStaticsController extends Controller
{
    public function count()
    {
        $data = [];

        // // الأعضاء المشطوبين
        $data['DeletedMember'] = CmMember::where('member_status_id', 2)->count();

        // // الأعضاء المشطوبين شطب بناءا علي طلبه
        $data['count_6'] = CmMember::where('member_status_id', 2)->where('discharge_reson_id', 6)->count();
        $data['Percentage_6'] = $data['DeletedMember'] != 0 ? round(($data['count_6'] / $data['DeletedMember']) * 100, 2) : 0;

        // // الأعضاء المشطوبين شطب للوفاة
        $data['count_7'] = CmMember::where('member_status_id', 2)->where('discharge_reson_id', 7)->count();
        $data['Percentage_7'] = $data['DeletedMember'] != 0 ? round(($data['count_7'] / $data['DeletedMember']) * 100, 2) : 0;

        // // الأعضاء المشطوبين شطب بموجب القرار الوزاري
        $data['count_8'] = CmMember::where('member_status_id', 2)->where('discharge_reson_id', 8)->count();
        $data['Percentage_8'] = $data['DeletedMember'] != 0 ? round(($data['count_8'] / $data['DeletedMember']) * 100, 2) : 0;

        // // الأعضاء المشطوبين شطب بموجب كتاب الهيئة
        $data['count_9'] = CmMember::where('member_status_id', 2)->where('discharge_reson_id', 9)->count();
        $data['Percentage_9'] = $data['DeletedMember'] != 0 ? round(($data['count_9'] / $data['DeletedMember']) * 100, 2) : 0;

        // // الأعضاء المشطوبين شطب لعدم السداد
        $data['count_18'] = CmMember::where('member_status_id', 2)->where('discharge_reson_id', 18)->count();
        $data['Percentage_18'] = $data['DeletedMember'] != 0 ? round(($data['count_18'] / $data['DeletedMember']) * 100, 2) : 0;

        $CmMemberTypes = CmMemberType::all();
        foreach ($CmMemberTypes as $index => $CmMemberType) {
            $data['CountDeletedCmMemberType'][$index][$CmMemberType->name] = CmMember::where('member_status_id', 2)->where('member_kind_id', $CmMemberType->id)->count();
        }
        return $data;
    }

    public function getStatics()
    {
        $data = [];

        //     الأعضاء العادي
        $data['normalMemberCount'] = CmMember::WhereRelation('memberType', 'parent_id', 1)->count();
        // الأعضاء المؤسسين
        $data['foundingMemberCount'] = CmMember::WhereRelation('memberType', 'parent_id', 2)->count();
        // الأعضاء المفصولين
        $data['dismissedMemberCount'] = CmMember::whereIn('member_type_id', [6, 7, 8, 9, 18])->count();
        // عدد الرعاة
        $data['sponsorsCount'] = CmSponser::whereNotNull('parent_id')->count();
        // عدد أعضاء حضور الانتخابات
        $data['presenceMemberCount'] = CmMember::whereIn('auto_member_type_id', [1, 2, 3])->count();
        // عدد الأعضاء تصويت في الانتخابات
        $data['voteMemberCount'] = CmMember::where('auto_member_type_id', 2)->count();
        // عدد الأعضاء الترشح في الانتخابات
        $data['nominateMemberCount'] = CmMember::whereIn('auto_member_type_id', [2, 3])->count();
        ////////////////////////////////////// بيانات الاعضاء العاديين //////////////////////////////////////

        // // في لائحة الاعضاء
        $data['InTheListOfMembers'] = CmMember::where('member_type_id', 4)->count();
        $data['InTheListOfMembersPercentage'] = $data['normalMemberCount'] != 0 ? round(($data['InTheListOfMembers'] / $data['normalMemberCount']) * 100, 2) : 0;

        // // موقوف
        $data['stopped'] = CmMember::where('member_type_id', 5)->count();
        $data['stoppedPercentage'] = $data['normalMemberCount'] != 0 ? round(($data['stopped'] / $data['normalMemberCount']) * 100, 2) : 0;

        // // له حق الترشح
        $data['HeHasTheRightToRun'] = CmMember::where('member_type_id', 11)->count();
        $data['HeHasTheRightToRunPercentage'] = $data['normalMemberCount'] != 0 ? round(($data['HeHasTheRightToRun'] / $data['normalMemberCount']) * 100, 2) : 0;

        // // له حق التصويت
        $data['HeHasTheRightToVote'] = CmMember::where('member_type_id', 12)->count();
        $data['HeHasTheRightToVotePercentage'] = $data['normalMemberCount'] != 0 ? round(($data['HeHasTheRightToVote'] / $data['normalMemberCount']) * 100, 2) : 0;

        // // ليس له حق التصويت
        $data['HeHasNoRightToVote'] = CmMember::where('member_type_id', 17)->count();
        $data['HeHasNoRightToVotePercentage'] = $data['normalMemberCount'] != 0 ? round(($data['HeHasNoRightToVote'] / $data['normalMemberCount']) * 100, 2) : 0;

        // // مسدد ليس له حق الحضور
        $data['PaidPersonHasNoRightToAttend'] = CmMember::where('member_type_id', 19)->count();
        $data['PaidPersonHasNoRightToAttendPercentage'] = $data['normalMemberCount'] != 0 ? round(($data['PaidPersonHasNoRightToAttend'] / $data['normalMemberCount']) * 100, 2) : 0;

        // //////////////////////////////////////////    المشطوبين    //////////////////////////////////////////////////////////

        // // شطب بناءا علي طلبه
        $data['DeletedAtHisRequest'] = CmMember::where('member_type_id', 6)->count();
        $data['DeletedAtHisRequestPercentage'] = $data['dismissedMemberCount'] != 0 ? round(($data['DeletedAtHisRequest'] / $data['dismissedMemberCount']) * 100, 2) : 0;

        // // شطب للوفاة
        $data['CancellationDueToDeath'] = CmMember::where('member_type_id', 7)->count();
        $data['CancellationDueToDeathPercentage'] = $data['dismissedMemberCount'] != 0 ? round(($data['CancellationDueToDeath'] / $data['dismissedMemberCount']) * 100, 2) : 0;

        // // شطب بموجب القرار الوزاري
        $data['CancellationPursuantToMinisterialDecision'] = CmMember::where('member_type_id', 8)->count();
        $data['CancellationPursuantToMinisterialDecisionPercentage'] = $data['dismissedMemberCount'] != 0 ? round(($data['CancellationPursuantToMinisterialDecision'] / $data['dismissedMemberCount']) * 100, 2) : 0;

        // // شطب بموجب كتاب الهيئة
        $data['CancellationAccordingToTheAuthoritySLetter'] = CmMember::where('member_type_id', 9)->count();
        $data['CancellationAccordingToTheAuthoritySLetterPercentage'] = $data['dismissedMemberCount'] != 0 ? round(($data['CancellationAccordingToTheAuthoritySLetter'] / $data['dismissedMemberCount']) * 100, 2) : 0;

        // // شطب لعدم السداد
        $data['WriteOffForNonPayment'] = CmMember::where('member_type_id', 18)->count();
        $data['WriteOffForNonPaymentPercentage'] = $data['dismissedMemberCount'] != 0 ? round(($data['WriteOffForNonPayment'] / $data['dismissedMemberCount']) * 100, 2) : 0;

        // //////////////////////////////////////////    الأعضاء المؤسسين    //////////////////////////////////////////////////////////

        // // عضو مؤسس
        $data['FoundingMemberDetail'] = CmMember::where('member_type_id', 10)->count();
        $data['FoundingMemberDetailPercentage'] = $data['foundingMemberCount'] != 0 ? round(($data['FoundingMemberDetail'] / $data['foundingMemberCount']) * 100, 2) : 0;

        // // له حق الترشح و التصويت
        $data['HeHasTheRightToRunAndVoteDetail'] = CmMember::where('member_type_id', 13)->count();
        $data['HeHasTheRightToRunAndVoteDetailPercentage'] = $data['foundingMemberCount'] != 0 ? round(($data['HeHasTheRightToRunAndVoteDetail'] / $data['foundingMemberCount']) * 100, 2) : 0;

        ///////////////////////// قيد الانتظار ////////////////////////////////////////////////////////////////////////////////

        $pending_member_request = CmMemberRequest::where('member_type_id', 15)->count();
        $pending_member = CmMember::where('member_type_id', 15)->count();
        $data['RequestMembersPendingCount'] = $pending_member_request + $pending_member;

        $response = [
            'message' => 'success',
            'data' => $data,
//            'groups' => $groups
        ];

        return response()->json($response);
    }

    public function validMembers()
    {
        $data = [];

        $data['validMembersCount'] = CmMember::where('member_status_id', 1)->count(); //6193

        $member_types = CmMemberType::get();

        $data['member_types_data'] = [];

        foreach ($member_types as $member_type) {

            $member_types_count = CmMember::where('member_status_id', 1)->where('member_kind_id', $member_type->id)->count();
            $percentage = $data['validMembersCount'] != 0 ? round(($member_types_count / $data['validMembersCount']) * 100, 2) : 0;

            $data['member_types_data'][] = [
                'name' => $member_type->name,
                'name_e' => $member_type->name_e,
                'count' => $member_types_count,
                'percentage' => $percentage,
            ];
        }

        $financial_statuses = CmFinancialStatus::get();

        $data['financial_statuses_data'] = [];

        foreach ($financial_statuses as $financial_status) {
            $financial_statuses_count = CmMember::where('member_status_id', 1)->where('financial_status_id', $financial_status->id)->count();
            $percentage = $data['validMembersCount'] != 0 ? round(($financial_statuses_count / $data['validMembersCount']) * 100, 2) : 0;

            $data['financial_statuses_data'][] = [
                'name' => $financial_status->name,
                'name_e' => $financial_status->name_e,
                'count' => $financial_statuses_count,
                'percentage' => $percentage,
            ];

        }

        $member_permissions = CmMemberPermission::get();
        $data['member_permissions_data'] = [];

        foreach ($member_permissions as $member_permission) {
            $member_permissions_count = CmMember::where('member_status_id', 1)->where('members_permissions_id', $member_permission->id)->where('last_transaction_year', 2024)->count();
            $percentage = $data['validMembersCount'] != 0 ? round(($member_permissions_count / $data['validMembersCount']) * 100, 2) : 0;

            $data['member_permissions_data'][] = [
                'name' => $member_permission->name,
                'name_e' => $member_permission->name_e,
                'count' => $member_permissions_count,
                'percentage' => $percentage,
            ];

        }

        $data['DeletedMember'] = CmMember::where('member_status_id', 2)->count();

        // // الأعضاء المشطوبين شطب بناءا علي طلبه
        $data['count_6'] = CmMember::where('member_status_id', 2)->where('discharge_reson_id', 6)->count();
        $data['Percentage_6'] = $data['DeletedMember'] != 0 ? round(($data['count_6'] / $data['DeletedMember']) * 100, 2) : 0;

        // // الأعضاء المشطوبين شطب للوفاة
        $data['count_7'] = CmMember::where('member_status_id', 2)->where('discharge_reson_id', 7)->count();
        $data['Percentage_7'] = $data['DeletedMember'] != 0 ? round(($data['count_7'] / $data['DeletedMember']) * 100, 2) : 0;

        // // الأعضاء المشطوبين شطب بموجب القرار الوزاري
        $data['count_8'] = CmMember::where('member_status_id', 2)->where('discharge_reson_id', 8)->count();
        $data['Percentage_8'] = $data['DeletedMember'] != 0 ? round(($data['count_8'] / $data['DeletedMember']) * 100, 2) : 0;

        // // الأعضاء المشطوبين شطب بموجب كتاب الهيئة
        $data['count_9'] = CmMember::where('member_status_id', 2)->where('discharge_reson_id', 9)->count();
        $data['Percentage_9'] = $data['DeletedMember'] != 0 ? round(($data['count_9'] / $data['DeletedMember']) * 100, 2) : 0;

        // // الأعضاء المشطوبين شطب لعدم السداد
        $data['count_18'] = CmMember::where('member_status_id', 2)->where('discharge_reson_id', 18)->count();
        $data['Percentage_18'] = $data['DeletedMember'] != 0 ? round(($data['count_18'] / $data['DeletedMember']) * 100, 2) : 0;

        $CmMemberTypes = CmMemberType::all();
        foreach ($CmMemberTypes as $index => $CmMemberType) {
            $memberTypeCount = CmMember::where('member_status_id', 2)->where('member_kind_id', $CmMemberType->id)->count();

            $percentage = $data['DeletedMember'] != 0 ? round(($memberTypeCount / $data['DeletedMember']) * 100, 2) : 0;

            $data['member_types'][] = [
                'name' => $CmMemberType->name,
                'name_e' => $CmMemberType->name_e,
                'count' => $memberTypeCount,
                'percentage' => $percentage,

            ];
        }

        //المجموعات
        $data['groups'] = CmSponsorGroup::count();
        //الرعاه
        $data['sponsors'] = CmSponser::count();
/////////////// count of sponsor for each group /////////////////////
        $sponsorsGroups = CmSponsorGroup::select('id', 'name', 'name_e')
            ->withCount('sponsors')
            ->get();
        $totalSponsors = $sponsorsGroups->sum('sponsors_count');

        $data['sponsors_group'] = $sponsorsGroups->map(function ($group) use ($totalSponsors) {
            // $percentage = ($group->sponsors_count / $totalSponsors) * 100;
            $percentage = ($totalSponsors > 0) ? ($group->sponsors_count / $totalSponsors) * 100 : 0;

            return [
                'name' => $group->name,
                'name_e' => $group->name_e,
                'count' => $group->sponsors_count,
                'percentage' => round($percentage, 2),
            ];
        });

        /////////////// count of members for each sponsor where (member_status_id) == 1 /////////////////////

        $memberCounts = CmSponser::select('id', 'name', 'name_e')
            ->withCount(['members' => function ($query) {
                $query->where('member_status_id', 1);
            }])
            ->get();

        $totalMembers = $memberCounts->sum('members_count');

        $data['sponsors_members'] = $memberCounts->map(function ($sponsor) use ($totalMembers) {
            $count = $sponsor->members_count;
            // $percentage = ($count / $totalMembers) * 100;
            $percentage = ($totalMembers > 0) ? ($count / $totalMembers) * 100 : 0;

            return [
                'id' => $sponsor->id,
                'name' => $sponsor->name,
                'name_e' => $sponsor->name_e,
                'count' => $count,
                'percentage' => round($percentage, 2),
            ];
        })->filter(function ($sponsor) {
            return $sponsor['count'] > 0;
        })->values();
////////// count of members for each sponsor where (member_status_id) == 1  and for every permission ///////////

        $totalMembersPermissions = CmMember::where('member_status_id', 1)->count();

        $memberCountsPermissions = CmSponser::select('id', 'name', 'name_e')
            ->withCount(['members' => function ($query) {
                $query->where('member_status_id', 1);
            }])
            ->get();

        $data['sponsors_members_permissions'] = $memberCountsPermissions->map(function ($sponsor) use ($totalMembersPermissions) {
            $permissions = CmMemberPermission::all();

            $counts = [];

            foreach ($permissions as $permission) {
                $count = $sponsor->members()
                    ->where('member_status_id', 1)
                    ->where('members_permissions_id', $permission->id)
                    ->count();

                // $percentage = ($count / $totalMembersPermissions) * 100;
                $percentage = ($totalMembersPermissions > 0) ? ($count / $totalMembersPermissions) * 100 : 0;

                $counts[] = [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'name_e' => $permission->name_e,
                    'count' => $count,
                    'percentage' => round($percentage, 2),
                ];
            }

            $result = [
                'id' => $sponsor->id,
                'name' => $sponsor->name,
                'name_e' => $sponsor->name_e,
                'count' => $sponsor->members_count,
                'percentage' => round(($sponsor->members_count / $totalMembersPermissions) * 100, 2),
                'permissions_counts' => $counts,
            ];

            return $result;
        })->values()->all();

        ////////////////////////// cm_member_requests ///////////////////////////////////////////////////////////////

        $data['member_requests_count'] = CmMemberRequest::where('deleted_at', null)->count();

        $data['member_requests_waiting_count'] = CmMemberRequest::where('deleted_at', null)->where('member_type_id', 1)->count();

        $data['member_requests_waiting_percentage'] = ($data['member_requests_count'] > 0) ? round(($data['member_requests_waiting_count'] / $data['member_requests_count']) * 100, 2) : 0;

        $data['member_requests_rejected_count'] = CmMemberRequest::where('deleted_at', null)->where('member_type_id', 2)->count();

        $data['member_requests_rejected_percentage'] = ($data['member_requests_count'] > 0) ? round(($data['member_requests_rejected_count'] / $data['member_requests_count']) * 100, 2) : 0;

        $response = [
            'message' => 'success',
            'data' => $data,
        ];

        return response()->json($response);

    }

}
