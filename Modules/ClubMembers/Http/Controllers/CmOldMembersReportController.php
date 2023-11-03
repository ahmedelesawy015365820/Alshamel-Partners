<?php

namespace Modules\ClubMembers\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class CmOldMembersReportController extends Controller
{
    public function getStatus()
    {
        return DB::table('status')->get();
    }
    public function getOldMembers(Request $request)
    {
        return DB::table('members')->where('ZSTATUS',$request->status_id)->paginate($request->per_page);
    }

}
