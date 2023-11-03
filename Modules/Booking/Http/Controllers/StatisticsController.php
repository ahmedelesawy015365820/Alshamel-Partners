<?php

namespace Modules\Booking\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Booking\Entities\Unit;
use Modules\Booking\Entities\UnitStatus;

class StatisticsController extends Controller
{

    public function all(Request $request)
    {
        $data = [];
        //الحالات
        $data['statusCount'] = UnitStatus::where('module_type', 'booking')->count();

        $data['UnitsCount'] = Unit::count();

        // الوحدات لكل حاله
        $status = UnitStatus::where('module_type', 'booking')->get();

        $data['unitsWithStatusWithDetailsCount'] = [];

        foreach ($status as $stat) {

            if ($stat['id'] != 12) {

                $units_with_Status_count = Unit::whereHas('documentHeaderDetails', function ($q) use ($stat) {
                    $q->where('unit_status_id', $stat['id'])
                        ->whereDate('date_from', '<=', now())
                        ->whereDate('date_to', '>=', now());
                })->count();

                $percentage = $data['UnitsCount'] != 0 ? round(($units_with_Status_count / $data['UnitsCount']) * 100, 2) : 0;

                $data['unitsWithStatusWithDetailsCount'][] = [
                    'id' => $stat->id,
                    'name' => $stat->name,
                    'name_e' => $stat->name_e,
                    'count' => $units_with_Status_count,
                    'percentage' => $percentage,
                ];
            }
        }

        ///////////////////////////////////////////

        $units_with_not_Status_count = Unit::whereDoesntHave('documentHeaderDetails', function ($q) use ($stat) {
            $q->whereDate('date_from', '<=', now())
                ->whereDate('date_to', '>=', now());
        })->count();

        $percentage_with_not_details = $data['UnitsCount'] != 0 ? round(($units_with_not_Status_count / $data['UnitsCount']) * 100, 2) : 0;

        $unitsWithStatusWithNotDetails = [
            'id' => 12,
            'name' => 'فارغة',
            'name_e' => 'فارغة',
            'count' => $units_with_not_Status_count,
            'percentage' => $percentage_with_not_details,
        ];

        array_push($data['unitsWithStatusWithDetailsCount'], $unitsWithStatusWithNotDetails);

        $response = [
            'message' => 'success',
            'data' => $data,
        ];

        return response()->json($response);
    }

}
