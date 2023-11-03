<?php

use App\Models\FinancialYear;
use App\Models\Serial;
use Carbon\Carbon;

// function generalSerial($model, $type)
// {
//     if (isset($model->serial)) {
//         if ($model->serial->branch) {
//             return $type . $model->document_id . $model->serial->branch->id . $model->id;
//         } else {
//             return $type . $model->document_id . $model->id;
//         }
//     } else {
//         if ($model->branch) {
//             $serial = Serial::where("branch_id", $model->branch->id)->first();
//             if ($serial) {
//                 return $type . $model->document_id . $serial->id . $model->id;
//             } else {
//                 return $type . $model->document_id . $model->id;
//             }

//         }
//     }

// }
function generalSerial($model, $type = "")
{
    $date = new Carbon($model->date);
    $data = [];
    if ($model->branch) {

        $serial = Serial::where(["branch_id" => $model->branch->id, "document_id" => $model->document_id])->first();

        $count = $model->where(['document_id' => $model->document_id, 'serial_id' => $serial->id])->get()->count();


        if ($count == 1) {

            $start_number = (int) $serial->start_no;
            $data['prefix'] = $date->format('y') . '-' . $model->branch->id . '-' . $model->document_id . '-' . $serial->perfix . '-' . $start_number;
            $data['serial_number'] = $start_number;

        } else {

//            $financial_year = FinancialYear::whereYear('start_date', now()->format('Y'))->first();
//
//            $end_date = $financial_year->end_date->format('Y');

//            if ($serial->restart_period_id == 5) {

                $last_serial_number = $model->where(['document_id' => $model->document_id, 'serial_id' => $serial->id])->latest('id')->skip(1)->first();
                $new_serial_number = (int) $last_serial_number->serial_number + 1;

                $data['prefix'] = $date->format('y') . '-' . $model->branch->id . '-' . $model->document_id . '-' . $serial->perfix . '-' . $new_serial_number;
                $data['serial_number'] = $new_serial_number;
//            } else {
//
//                $start_number = (int) $serial->start_no;
//                $data['prefix'] = $date->format('y') . '-' . $model->branch->id . '-' . $model->document_id . '-' . $serial->perfix . '-' . $start_number;
//                $data['serial_number'] = $start_number;
//            }

        }
        return $data;

    }
}

function generalSerialUpdate($model)
{
    $data = [];
    if ($model->branch) {
        $serial = Serial::where(["branch_id" => $model->branch->id, "document_id" => $model->document_id])->first();
        $start_number = $model->serial_number;
        $data['prefix'] = $serial->perfix . '-' . $model->document_id . '-' . $start_number;
        $data['serial_number'] = $start_number;
    }
    return $data;

}

function generalSerialWithIdCreate($model, $serial_id)
{
    $data = [];
    $serial = Serial::find($serial_id);
    $count = $model->where(['document_id' => $model->document_id, 'serial_id' => $serial->id])->get()->count();

    if ($count == 1) {

        $start_number = (int) $serial->start_no;
        $data['prefix'] = $serial->perfix . '-' . $model->document_id . '-' . $start_number;
        $data['serial_number'] = $start_number;

    } else {

        $last_serial_number = $model->where(['document_id' => $model->document_id, 'serial_id' => $serial_id])->latest('id')->skip(1)->first();

        $last_ser_num = $last_serial_number->serial_number;

        $new_serial_number = (int) $last_ser_num + 1;

        $data['prefix'] = $serial->perfix . '-' . $model->document_id . '-' . $new_serial_number;
        $data['serial_number'] = $new_serial_number;

    }
    return $data;
}

function generalCheckDateModelFinancialYear($date)
{
    $FinancialYear = FinancialYear::whereDate('start_date', '<=', $date)->whereDate('end_date', '>=', $date)->where('is_active', 1)->first();
    if ($FinancialYear):
        return 'true';
    endif;
    return "false";
}
