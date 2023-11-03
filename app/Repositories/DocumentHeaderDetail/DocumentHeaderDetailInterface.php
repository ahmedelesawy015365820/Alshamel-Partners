<?php

namespace App\Repositories\DocumentHeaderDetail;



interface DocumentHeaderDetailInterface
{
    public function all($request);
    public function find($id);
    public function create($request);
    public function update($request,$id);
    public function logs($id);
    public function delete($id);
    public function getReportDocument($request);
    public function getAnnualFinancialReport($request);
    public function getAnnualFinancialReportDetail($request);
    public function getPanelUsageRateReport($request);
    public function getRooms($request);
    public function allBookingReport($request);


}
