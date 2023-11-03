<?php

namespace Modules\ClubMembers\Repositories\CmMember;

use App\Models\FinancialYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Entities\CmHistoryTransform;
use Modules\ClubMembers\Entities\CmMember;
use Modules\ClubMembers\Entities\CmMemberRequest;
use Modules\ClubMembers\Entities\CmTransaction;

class CmMemberRepository implements CmMemberInterface
{

    public function __construct(private CmMember $model, private CmHistoryTransform $modelCmHistoryTransform, CmMemberRequest $modelRequest, CmTransaction $modelTransaction)
    {
        $this->model = $model;
        $this->modelRequest = $modelRequest;
        $this->modelCmHistoryTransform = $modelCmHistoryTransform;
        $this->modelTransaction = $modelTransaction;

    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC')->with('lastCmTransaction');

        if ($request->financial_status_id) {
            $models->where('financial_status_id', $request->financial_status_id);
        }
        if ($request->member_type_id) {
            $models->where('member_type_id', $request->member_type_id);
        }

        if ($request->member_id) {
            $models->where('id', $request->member_id);
        }
        if ($request->hasTransaction) {
            $models->whereHas('cmTransaction');
        }
        if ($request->sponsor_id) {
            $models->where('sponsor_id', $request->sponsor_id);
        }

        if ($request->member_status_id) {
            $models->where('member_status_id', $request->member_status_id);
        }

        if ($request->postal_report) {
            $models->where('member_kind_id', $request->postal_report)->where('member_status_id', 1);
        }
        if ($request->without) {
            $models->whereNotIn('id', explode(",", $request->without));
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } elseif ($request->limet) {
            return ['data' => $models->take($request->limet)->get(), 'paginate' => false];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function allAcceptancePending($request)
    {

        $models = $this->model->filter($request)
            ->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC')
            ->where('acceptance', '0');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }

    }

    public function create($request)
    {

        return DB::transaction(function () use ($request) {
            if ($request['first_name']) {
                $full_name['first_name'] = $request['first_name'];
            }
            if ($request['second_name']) {
                $full_name['second_name'] = $request['second_name'];
            }
            if ($request['third_name']) {
                $full_name['third_name'] = $request['third_name'];
            }
            if ($request['last_name']) {
                $full_name['last_name'] = $request['last_name'];
            }
            if ($request['family_name']) {
                $full_name['family_name'] = $request['family_name'];
            }

            $array = implode(' ', $full_name);

            return $this->model->create(array_merge($request, ['full_name' => $array, 'member_type_id' => 4]));
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            if ($request['first_name']) {
                $full_name['first_name'] = $request['first_name'];
            }
            if ($request['second_name']) {
                $full_name['second_name'] = $request['second_name'];
            }
            if ($request['third_name']) {
                $full_name['third_name'] = $request['third_name'];
            }
            if ($request['last_name']) {
                $full_name['last_name'] = $request['last_name'];
            }
            if ($request['family_name']) {
                $full_name['family_name'] = $request['family_name'];
            }

            $array = implode(' ', $full_name);
            $this->model->where("id", $id)->update(array_merge($request, ['full_name' => $array]));
        });
        $model = $this->model->find($id);
        return $model;
        //
    }

    public function updateAcceptance($request, $id)
    {

        DB::transaction(function () use ($id, $request) {
            $increment_member_number = $this->model->max('membership_number');
            $new_member_number = $increment_member_number + 1;

            $increment_applying_number = $this->model->max('applying_number');
            $new_applying_number = $increment_applying_number + 1;

            $this->model->where("id", $id)->update(array_merge($request->all(),
                [
                    'acceptance' => 1,
                    'membership_number' => $new_member_number,
                    'applying_number' => $new_applying_number,
                    'financial_status_id' => 3,
                    'member_type_id' => 4,
                    'status_id' => 2,
                ]));
        });

        $model = $this->model->find($id);
        return $model;
    }

    public function updateDecline($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $this->modelRequest->where("id", $id)->update(array_merge($request->all(), ['acceptance' => 2, 'member_type_id' => 2]));
        });
        $model = $this->modelRequest->find($id);
        return $model;
    }

    public function updateSponsor($request, $sponsor_id)
    {
        // return now()->format('d-m-Y H:i:s') ;
        DB::transaction(function () use ($sponsor_id, $request) {
            $models = $this->model->where("sponsor_id", $sponsor_id)->get();
            foreach ($models as $model) {
                $this->modelCmHistoryTransform->create([
                    'sponser_id_from' => $model->sponsor_id,
                    'sponser_id_to' => $request->sponsor_id,
                    'member_id' => $model->id,
                    'date' => now(),
                ]);
                $model->update($request->all());
            }
        });

        $updatedModels = $this->model->where("sponsor_id", $request->sponsor_id)->where('updated_at', now()->format('y-m-d H:i:s'))->get();
        return $updatedModels;
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

    public function delete($id)
    {
        $model = $this->find($id);
        $model->delete();
    }

    public function allAcceptance($request)
    {

        $models = $this->model->filter($request)
            ->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC')
            ->where('acceptance', '1');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }

    }

    public function acceptMembers($request)
    {
        return DB::transaction(function () use ($request) {

            foreach ($request['accept-members'] as $accept_member) {
                $max_membership_number = $this->model->max('membership_number');
                $max = $max_membership_number + 1;

                $memberRequest = $this->modelRequest->where('id', $accept_member['id'])->first();
                if ($memberRequest) {
                    $membercreate = collect($memberRequest)->except(['id', 'deleted_at', 'created_at', 'updated_at', 'financial_status_id', 'member_type_id', 'status_id']);
                    $model = $this->model->create($membercreate->all());
                    $accept = collect($accept_member)->except(['id', 'financial_status_id', 'member_type_id', 'status_id']);
                    $model->update(array_merge($accept->all(),
                        [
                            'acceptance' => 1,
                            'membership_number' => $max,
                            'financial_status_id' => 1,
                            'member_kind_id' => 1,
                            'member_status_id' => 1,
                        ]));
                    $transaction = $this->modelTransaction->where('member_request_id', $memberRequest->id)->first();
                    if ($transaction) {
                        $transaction->update([
                            'cm_member_id' => $model->id,
                            'member_request_id' => null,
                            "member_number" => $model['membership_number'],

                        ]);
                    }
                    $memberRequest->delete();
                }
            }
            return 200;

        });
    }

    public function reportCmMember($request)
    {
//        $this->publicUpdatePermissionCmMember($request->members_permissions_id);
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->cm_permissions_id == 1) {
            $models->whereIn('auto_member_type_id', [1, 2, 3]);
        }
        if ($request->cm_permissions_id == 2) {
            $models->where('auto_member_type_id', 2);
        }

        if ($request->cm_permissions_id == 3) {
            $models->whereIn('auto_member_type_id', [2, 3]);
        }

        if ($request->cm_permissions_id == "0") {
            $models->where('auto_member_type_id', null);
        }

        if ($request->members_permissions_id) {
            $year = Carbon::createFromFormat('d-m-Y',$request->dateOfYear)->format('Y') + 1;

            DB::statement('call p_statistics(?)',["$year"]);

            $models->whereIn('members_permissions_id', $request->members_permissions_id)->where('member_status_id', 1)->where('last_transaction_year', $year)->with('lastCmTransaction');

            $new_year = Carbon::now()->format('Y') + 1;
            DB::statement('call p_statistics(?)',["$year"]);
       }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }

    }

    public function updateLastTransactionDate()
    {
        $All_Members = $this->model->with('lastCmTransaction')->whereIn('member_type_id', [4, 5, 11, 12, 17, 19])->get();
        foreach ($All_Members as $member):

            $Last_date = \Carbon\Carbon::parse($member->lastCmTransaction['date'])->format('d-m-Y');
            $member->update(['last_transaction_date' => $Last_date]);
        endforeach;
        return 200;

    }
//        Artisan::call('permission:db');
//        return "all";
    public function updateCmMember()
    {
        //return CmMember::with('lastCmTransaction')->find(13838);
        //return CmTransaction::where('cm_member_id', 13838)->first();
        //ini_set('max_execution_time', '60');

        $financialyear = FinancialYear::where('is_active', 1)->first();

        if ($financialyear) {

            //reset the financial status id in Cm Members for all active (saaary) members to 2 (غير مسدد)
            // reset the member permission id in Cm Members for all active (saary) members to 1 (ليس له حق الحضور)
            /// update to columns  financial_status_id =  غير مسدد and  members_permissions_id = ليس له حق الحضور  in table CmMembers
            CmMember::where(['member_status_id' => 1, 'member_kind_id' => 2])->update([
                'financial_status_id' => 1, // غير مطلوب السداد
                'members_permissions_id' => 4, // كل الحقوق
            ]);



            // عضو عادي
            CmMember::where(['member_status_id' => 1, 'member_kind_id' => 1])->update([
                'financial_status_id' => 2, // غير مسدد
                'members_permissions_id' => 1, // ليس له حق
            ]);



            // all settings
            $settings =
            DB::table('cm_type_permissions')->where('cm_permissions_id', '>', 1)->orderBy('cm_permissions_id', 'asc')->get();

            // get the active financial year

            // filter the "saaaaary" members which have at least one last_transaction_date
            // that its last_transaction_year as the financial year

            $running_member_all = $this->model
                ->where('member_status_id', 1) // ساري
                ->where(function ($q) use ($financialyear) {
                    $q->whereNotNull('last_transaction_date')
                        ->where('last_transaction_year', $financialyear->year); // int ? int
                })->orWhere('member_kind_id', 2)->count();

            return $running_member_all;

            //$time = strtotime('2023/02/28');
            //$date_allowed_vote_date = date('Y-m-d',$time);

            // From a date string
            $date_allowed_vote_date = Carbon::createFromFormat('Y-m-d', '2022-02-28');

            // From a date string
            $date_allowed_vote_date = Carbon::createFromFormat('Y-m-d', '2022-02-28');

            $update_3ady_members = $this->model
                ->where('member_status_id', 1) // ساري
                ->where('member_kind_id', 1) // عادي
                ->whereNotNull('last_transaction_date') // عنده transaction
                ->where('last_transaction_year', $financialyear->year) // سنة ال transaction هي سنة السنة المالية
                ->whereDate('last_transaction_date', '<=', $date_allowed_vote_date)
                ->update
                ([
                'financial_status_id' => 3, //مسدد في الموعد
            ]);

            $update_3ady_members_2 = $this->model
                ->where('member_status_id', 1) // ساري
                ->where('member_kind_id', 1) // عادي
                ->whereNotNull('last_transaction_date') // عنده transaction
                ->where('last_transaction_year', $financialyear->year) // سنة ال transaction هي سنة السنة المالية
                ->whereDate('last_transaction_date', '>', $date_allowed_vote_date)
                ->update
                ([
                'financial_status_id' => 4, //مسدد بعد الموعد
            ]);

            //return $update_3ady_members_2;

            /*
            ->update
            ([
            'financial_status_id'    => 4,  //مسدد بعد الموعد
            ]);
             */

            //$time2 = strtotime('1900/02/27');
            //$date2 = date('Y-m-d',$time);
            //$date2 = new Carbon('2023-02-28');
            //$date2 = Carbon::createFromFormat('Y-m-d', '2023-02-27');;
            //return $date_allowed_vote_date > $date2 ? "true" : "false" ;

            //return $update_3ady_members_2;

            /*
            ->update
            ([
            'financial_status_id'    => 4,  //مسدد بعد الموعد
            ]);
             */

            foreach ($running_member_all as $index => $member) {
                $member->refresh();

                $dbDate = \Carbon\Carbon::parse($member->membership_date)->format('Y-m-d');
                $diffYears = \Carbon\Carbon::now()->diffInYears($dbDate);

                foreach ($settings->reverse() as $setting) {

                    if (
                        $member->member_kind_id == $setting->cm_members_type_id &&
                        $diffYears >= $setting->membership_period &&
                        $member->financial_status_id == $setting->cm_financial_status_id
                    ) {

                        $member->update
                            ([
                            'members_permissions_id' => $setting->cm_permissions_id,
                        ]);
                        //   }

                        break; // exit the for each on the permissions => he/she can NOT achieve better
                        //}

                    } else {
                        $member->update
                            ([
                            'members_permissions_id' => 1,
                        ]);
                    }

                }

            }

        } else {

            return 'There must be an active financial year!';
        }

        return 200;
    }

    public function oldupdateCmMember()
    {
        Artisan::call('permission:db');
        return "all";
        $financialyear = FinancialYear::where('is_active', 1)->first();
        if ($financialyear) {

            //reset the financial status id in Cm Members for all active (saaary) members to 2 (غير مسدد)
            // reset the member permission id in Cm Members for all active (saary) members to 1 (ليس له حق الحضور)
            /// update to columns  financial_status_id =  غير مسدد and  members_permissions_id = ليس له حق الحضور  in table CmMembers
            CmMember::where(['member_status_id' => 1, 'member_kind_id' => 2])->update([
                'financial_status_id' => 1, // غير مطلوب السداد
                'members_permissions_id' => 4, // كل الحقوق
            ]);

            // عضو عادي
            CmMember::where(['member_status_id' => 1, 'member_kind_id' => 1])->update([
                'financial_status_id' => 2, // غير مسدد
                'members_permissions_id' => 1, // ليس له حق
            ]);

            // all settings
            $settings = DB::table('cm_type_permissions')->where('cm_permissions_id', '>', 1)->orderBy('cm_permissions_id', 'asc')->get();

            // get the active financial year

            // filter the "saaaaary" members which have at least one last_transaction_date
            // that its last_transaction_year as the financial year

            $running_member_all = DB::table('cm_members')
                ->where('member_status_id', 1) // ساري
                ->where(function ($q) use ($financialyear) {
                    $q->whereNotNull('last_transaction_date')
                        ->where('last_transaction_year', $financialyear->year); // int ? int
                })->orWhere('member_kind_id', 2)->get();
            $time = strtotime('2023/02/28');

            $date_allowed_vote_date = date('Y-m-d', $time);

            $update_3ady_members = $this->model
                ->where('member_status_id', 1) // ساري
                ->where('member_kind_id', 1) // عادي
                ->whereNotNull('last_transaction_date') // عنده transaction
                ->where('last_transaction_year', $financialyear->year) // سنة ال transaction هي سنة السنة المالية
                ->whereDate('last_transaction_date', '<=', $date_allowed_vote_date)
                ->update
                ([
                'financial_status_id' => 3, //مسدد في الموعد
            ]);

            $update_3ady_members_2 = $this->model
                ->where('member_status_id', 1) // ساري
                ->where('member_kind_id', 1) // عادي
                ->whereNotNull('last_transaction_date') // عنده transaction
                ->where('last_transaction_year', $financialyear->year) // سنة ال transaction هي سنة السنة المالية
                ->whereDate('last_transaction_date', '>', $date_allowed_vote_date)
                ->update
                ([
                'financial_status_id' => 4, //مسدد في الموعد
            ])
            ;

            foreach ($running_member_all as $member) {

                $dbDate = \Carbon\Carbon::parse($member->membership_date)->format('Y-m-d');
                $diffYears = \Carbon\Carbon::now()->diffInYears($dbDate);
                return $settings->reverse();
                foreach ($settings->reverse() as $setting) {
                    if ($member->member_kind_id == $setting->cm_members_type_id && $setting->cm_financial_status_id == $member->financial_status_id && $diffYears >= $setting->membership_period) {

                        $member->update
                            ([
                            'members_permissions_id' => $setting->cm_permissions_id,
                        ]);
                        //   }

                        break; // exit the for each on the permissions => he/she can NOT achieve better
                        //}

                    } else {
                        $member->update
                            ([
                            'members_permissions_id' => 1,
                        ]);

                    }

                }

            }

        } else {

            return 'There must be an active financial year!';
        }

        return 200;
    }

    public function publicUpdatePermissionCmMember($permission_id)
    {

        $financialyear = FinancialYear::where('is_active', 1)->first();
        if ($financialyear) {

            //reset the financial status id in Cm Members for all active (saaary) members to 2 (غير مسدد)
            // reset the member permission id in Cm Members for all active (saary) members to 1 (ليس له حق الحضور)
            /// update to columns  financial_status_id =  غير مسدد and  members_permissions_id = ليس له حق الحضور  in table CmMembers
            collect($this->model->where('member_status_id', 1)->get())->each(function ($item) {
                if ($item->member_kind_id == 2) {
                    $item->update([
                        'financial_status_id' => 1, // غير مطلوب السداد
                        'members_permissions_id' => 4, // كل الحقوق
                    ]);

                } else {
                    $item->update([
                        'financial_status_id' => 2, // غير مسدد
                        'members_permissions_id' => 1, // ليس له حقوق
                    ]);
                }
            });

            // all settings
            $settings = DB::table('cm_type_permissions')->where('cm_permissions_id', $permission_id)->where('cm_permissions_id', '>', 1)->orderBy('cm_permissions_id', 'asc')->get();

            // get the active financial year

            // filter the "saaaaary" members which have at least one last_transaction_date
            // that its last_transaction_year as the financial year
            $running_member_all = $this->model
                ->where('member_status_id', 1) // ساري
                ->where(function ($q) use ($financialyear) {
                    $q->whereNotNull('last_transaction_date')
                        ->where('last_transaction_year', $financialyear->year); // int ? int
                })->orWhere('member_kind_id', 2)->get();

            foreach ($running_member_all as $member) {

                $paidontime = false;

                foreach ($settings->reverse() as $setting) {

                    if ($member->member_kind_id == $setting->cm_members_type_id) {
                        $dbDate = \Carbon\Carbon::parse($member->membership_date)->format('Y-m-d');
                        $diffYears = \Carbon\Carbon::now()->diffInYears($dbDate);
                        $yearGlued_allowed_vote_date = strftime("%F", strtotime($financialyear->year . "-" . $setting->allowed_vote_date)); // format: (yyyy-mm-dd)
                        $yearGlued_allowed_subscription_date = strftime("%F", strtotime($financialyear->year . "-" . $setting->allowed_subscription_date)); // format: (yyyy-mm-dd)

                        if ($member->last_transaction_date) {

                            $Last_Member_transaction = \Carbon\Carbon::parse($member->last_transaction_date)->format('Y-m-d'); // format: (yyyy-mm-dd)

                            if ($Last_Member_transaction <= $yearGlued_allowed_vote_date) {
                                $member->update
                                    ([
                                    'financial_status_id' => 3, //مسدد في الموعد
                                ]);

                                $paidontime = true;

                            } else // ( $Last_Member_transaction > $yearGlued_allowed_vote_date )
                            {
                                $member->update
                                    ([
                                    'financial_status_id' => 4, // مسدد بعد الموعد
                                ]);

                            }

                        }
                        if ($setting->cm_financial_status_id == 1) {
                            $paidontime = true; // كانه داااافع
                        } // ليس مطلوب السداد

                        // اعطاء حق لعضو عادي او مؤسس بناءا علي مدة الاشتراك

                        if ($paidontime == true && $diffYears >= $setting->membership_period) {

                            //if ($Last_Member_transaction <= $yearGlued_allowed_vote_date)
                            //{
                            //   if ($member->member_kind_id == 1){
                            $member->update
                                ([
                                'members_permissions_id' => $setting->cm_permissions_id,
                            ]);
                            //   }

                            break; // exit the for each on the permissions => he/she can NOT achieve better
                            //}

                        } else {
                            $member->update
                                ([
                                'members_permissions_id' => 1,
                            ]);

                        }

                    }

                }

            }

        } else {

            return 'There must be an active financial year!';
        }

        return 200;
    }

    public function getSponsors($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->sponsor_id) {
            $models->where('sponsor_id', $request->sponsor_id);
        }

        if ($request->memberNumber) {
            $memberIds = $this->model->where('sponsor_id', $request->sponsor_id)->pluck('id');
        }
        // return $memberIds;

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true, 'memberIds' => $memberIds];
        } elseif ($request->limet) {
            return ['data' => $models->take($request->limet)->get(), 'paginate' => false];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function reportToMembers($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->financial_status_id) {
            $models->whereIn('financial_status_id', explode(",", $request->financial_status_id));
        }

        if ($request->member_type_id) {
            $models->whereIn('member_kind_id', explode(",", $request->member_type_id));
        }

        if ($request->status_id) {
            $models->whereIn('member_status_id', explode(",", $request->status_id));
        }

        if ($request->year_number) {
            $membership_year = $request->year - $request->year_number;
            $models->whereYear('membership_date', '<=', $membership_year);
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function getMemberForMultiSubscription($request)
    {
        $models = $this->model->orderBy('full_name', 'ASC')->with('lastCmTransaction');

        if ($request->sponsor_id) {
            $models->where('sponsor_id', $request->sponsor_id);
        }

        if ($request->hasTransaction) {
            $models->whereHas('cmTransaction');
        }

        if ($request->member_status_id) {
            $models->where('member_status_id', $request->member_status_id);
        }

        if ($request->financial_status_id) {
            $models->where('financial_status_id', $request->financial_status_id);
        }

        if ($request->membership_number) {
            $models->where('membership_number', $request->membership_number);
        }

        if ($request->full_name) {
            $models->where('full_name', 'like', $request->full_name . '%');
        }

        if ($request->national_id) {
            $models->where('national_id', $request->national_id);
        }

        if ($request->first_name) {
            $models->where('first_name', 'like', $request->first_name . '%');
        }

        if ($request->second_name) {
            $models->where('second_name', 'like', $request->second_name . '%');
        }

        if ($request->third_name) {
            $models->where('third_name', 'like', $request->third_name . '%');
        }

        if ($request->last_name) {
            $models->where('last_name', 'like', $request->last_name . '%');
        }

        if ($request->family_name) {
            $models->where('family_name', 'like', $request->family_name . '%');
        }

        if ($request->year)
        {
            $models->whereRelation('lastCmTransaction',function ($q) use ($request){
                $q->where('year',$request->year);
            });
        }
        if (isset($request->gender) &&($request->gender == 0 || $request->gender == 1)) {
            $models->where('gender', $request->gender);
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        }  else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

}
