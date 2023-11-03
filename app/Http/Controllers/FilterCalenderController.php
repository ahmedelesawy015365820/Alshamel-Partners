<?php

namespace App\Http\Controllers;

use App\Http\Resources\GetRoomsCalenderResource;
use App\Http\Resources\GetRoomsCalenderWithDetailsResource;
use App\Http\Resources\RoomsByFloorResource;
use App\Models\DocumentHeaderDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Booking\Entities\Unit;

class FilterCalenderController extends Controller
{

    public function __construct(private Unit $model, private DocumentHeaderDetail $modelDetail)
    {
        $this->model = $model;
        $this->modelDetail = $modelDetail;
    }

    public function getRoomsCalender(Request $request)
    {
        $currentMonth = Carbon::now();

        $models = $this->model->with(['Detail' => function ($q) {
            $q->select('id', 'date_from', 'date_to', 'unit_id', 'unit_status_id')->with('bookingUnitStatus:id,name,color');
        }])->where(function ($q) use ($currentMonth, $request) {
            $q->whereHas('documentHeaderDetails', function ($q) use ($currentMonth, $request) {
                $q->whereMonth('date_from', $request->months_number ?? $currentMonth->month)
                    ->whereYear('date_from', $request->year ?? $currentMonth->year)
                    ->whereNotNull('unit_status_id')->where('unit_status_id', '!=', 0);
            });
        })->get();

        return responseJson(200, 'success', GetRoomsCalenderResource::collection($models));

    }

    public function getRoomsCalenderFilter(Request $request)
    {

        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $unit_id = $request->unit_id;
        $unit_status_id = $request->unit_status_id;
        $DocumentHeaderDetail = DocumentHeaderDetail::select('document_header_id', 'date_from', 'date_to', 'unit_id', 'unit_status_id')
            ->whereIn('unit_id', $unit_id)

            ->where(function ($q) use ($request) {

                $q->where(function ($qu) use ($request) {
                    $qu->where('date_from', '>=', $request->date_from)
                        ->where('date_from', '<=', $request->date_to);
                })->orWhere(function ($que) use ($request) {
                    $que->where('date_to', '>=', $request->date_from)
                        ->where('date_to', '<=', $request->date_to);
                })->orWhere(function ($quer) use ($request) {
                    $quer->where('date_from', '<=', $request->date_from)
                        ->where('date_to', '>=', $request->date_to);
                });

            })->when($unit_status_id, function ($query) use ($unit_status_id) {
            $query->where('unit_status_id', $unit_status_id);
        })
            ->with(['unit:id,name,name_e', 'bookingUnitStatus:id,name,color'])
            ->orderBy('id', 'ASC')->orderBy('date_from', 'ASC')->get();

        return responseJson(200, 'success', GetRoomsCalenderWithDetailsResource::collection($DocumentHeaderDetail));

    }

    public function getRoomsByFloors(Request $request)
    {

        $currentMonth = Carbon::now();

        $models = $this->model
            ->select('id', 'name', 'name_e', 'booking_floor_id')
            ->when($request->booking_floor_id, function ($query) use ($request) {
                $query->where('booking_floor_id', $request->booking_floor_id);
            })->when($request->unit_id, function ($q) use ($request) {
            $q->where('unit_id', $request->unit_id);
        })
            ->with(['documentHeaderDetails' => function ($query) use ($request, $currentMonth) {
                $query->whereNotNull('unit_status_id')->where('unit_status_id', '!=', 0)

                ->where(function ($q) use ($request , $currentMonth) {

                    $q->where(function ($qu) use ($request , $currentMonth) {
                        $qu->where('date_from', '>=', $request->date_from ?? $currentMonth)
                            ->where('date_from', '<=', $request->date_to ?? $currentMonth);
                    })->orWhere(function ($que) use ($request, $currentMonth) {
                        $que->where('date_to', '>=', $request->date_from ?? $currentMonth)
                            ->where('date_to', '<=', $request->date_to ?? $currentMonth);
                    })->orWhere(function ($quer) use ($request, $currentMonth) {
                        $quer->where('date_from', '<=', $request->date_from ?? $currentMonth)
                            ->where('date_to', '>=', $request->date_to ?? $currentMonth);
                    });

                })



                // ->whereDate('date_from', '>=', $request->date_from ?? $currentMonth)
                // ->whereDate('date_to', '<=', $request->date_to ?? $currentMonth)

                    ->when($request->unit_status_id, function ($q) use ($request) {
                        $q->where('unit_status_id', $request->unit_status_id);
                    })->with('bookingUnitStatus', function ($q) {
                    $q->select('id', 'name', 'color');
                });
            }])->get();

        //    return $models;
        $data = collect(RoomsByFloorResource::collection($models));
        return responseJson(200, 'success', $data);

    }

}
