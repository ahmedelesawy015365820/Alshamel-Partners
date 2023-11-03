<?php

namespace Modules\HR\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

/*
            $table->id();
            $table->unsignedTinyInteger('month')->min(1)->max(12)->comment('report month');
            $table->year('year')->comment('report year');
            $table->unsignedInteger('employee_id');
            
            $table->string('name')->comment('employee name');
            $table->date('day')->comment('yyyy-mm-dd in report month and year');
            $table->time('attend_1')->nullable()->comment('1st shift attend time');
            $table->time('depart_1')->nullable()->comment('1st shift depart time');
            $table->time('attend_2')->nullable()->comment('2nd shift attend time');
            $table->time('depart_2')->nullable()->comment('2nd shift depart time');

            $table->unsignedSmallInteger('late')->nullable()->comment('total late time in mins');
            $table->unsignedSmallInteger('overtime')->nullable()->comment('total over time in mins');
            $table->unsignedSmallInteger('exact_hours')->nullable()->comment('total exact working time in mins');
            $table->enum('absence', ['attended', 'absent 1', 'absent 2', 'absent'])->nullable()->comment('day status');
            $table->string('notes')->nullable();

            $table->timestamps();

*/



class AttendanceDepartureReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            
            'id' => $this->id,
            'month' => $this->month,
            'year' => $this->year,
            'employee_id' => $this->employee_id,
            'name' => $this->name,
            'day' => $this->day,
            'attend_1' => $this->attend_1,
            'depart_1' => $this->depart_1,
            'attend_2' => $this->attend_2,
            'depart_2' => $this->depart_2,
            'late' => $this->late,
            'overtime' => $this->overtime,
            'exact_hours' => $this->exact_hours,
            'absence' => $this->absence,
            'notes' => $this->notes

        ];
    }
}
