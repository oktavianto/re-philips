<?php

namespace App\Http\Controllers\Master;

use App\Distributor;
use App\DmArea;
use App\Filters\SellinFilters;
use App\Filters\SellOutFilters;
use App\Filters\RetConsumentFilters;
use App\Filters\RetDistributorFilters;
use App\Filters\FreeProductFilters;
use App\Filters\TbatFilters;
use App\Filters\DisplayShareFilters;
use App\Filters\SohFilters;
use App\Filters\SosFilters;
use App\ProductFocuses;
use App\Region;
use App\StoreDistributor;
use App\Reports\SummarySellIn;
use App\Reports\HistorySellIn;
use App\Reports\SummarySellOut;
use App\Reports\HistorySellOut;
use App\Reports\SummaryRetConsument;
use App\Reports\HistoryRetConsument;
use App\Reports\SummaryRetDistributor;
use App\Reports\HistoryRetDistributor;
use App\Reports\SummaryFreeProduct;
use App\Reports\HistoryFreeProduct;
use App\Reports\SummaryTbat;
use App\Reports\HistoryTbat;
use App\Reports\SummaryDisplayShare;
use App\Reports\HistoryDisplayShare;
use App\Reports\SummarySoh;
use App\Reports\HistorySoh;
use App\Reports\SummarySos;
use App\Reports\HistorySos;
use App\TrainerArea;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use League\Geotools\CLI\Command\Convert\DM;
use Yajra\Datatables\Facades\Datatables;
use DB;
use Auth;
use App\PosmActivity;
use App\PosmActivityDetail;
use App\SellIn;
use App\SellInDetail;
use App\SellOut;
use App\SellOutDetail;
use App\RetConsument;
use App\RetConsumentDetail;
use App\RetDistributor;
use App\RetDistributorDetail;
use App\Tbat;
use App\TbatDetail;
use App\DisplayShare;
use App\DisplayShareDetail;
use App\Soh;
use App\SohDetail;
use App\Sos;
use App\SosDetail;
use App\FreeProduct;
use App\FreeProductDetail;
use App\MaintenanceRequest;
use App\CompetitorActivity;
use App\PromoActivity;
use App\PromoActivityDetail;
use App\Attendance;
use App\AttendanceDetail;
use App\EmployeeStore;
use App\District;
use App\Store;
use App\Area;
use App\RsmRegion;
use App\Filters\ReportFilters;
use App\Filters\ReportPosmActivityFilters;
use App\Filters\ReportSellOutFilters;
use App\Filters\ReportSohFilters;
use App\Filters\ReportSosFilters;
use App\Filters\ReportRetConsumentFilters;
use App\Filters\ReportRetDistributorFilters;
use App\Filters\ReportTbatFilters;
use App\Filters\ReportDisplayShareFilters;
use App\Filters\MaintenanceRequestFilters;
use App\Filters\CompetitorActivityFilters;
use App\Traits\StringTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use File;

class ReportController extends Controller
{
    use StringTrait;

    public function sellInIndex(){
        return view('report.sellin-report');
    }

    public function sellOutIndex(){
        return view('report.sellout-report');
    }

    public function retConsumentIndex()
    {
        return view('report.retConsument-report');
    }

    public function retDistributorIndex()
    {
        return view('report.retDistributor-report');
    }

    public function tbatIndex()
    {
        return view('report.tbat-report');
    }

    public function freeproductIndex()
    {
        return view('report.freeproduct-report');
    }

    public function sohIndex()
    {
        return view('report.soh-report');
    }

    public function sosIndex()
    {
        return view('report.sos-report');
    }

    public function displayShareIndex(){
        return view('report.displayshare-report');
    }

    public function maintenanceRequestIndex(){
        return view('report.maintenancerequest-report');
    }

    public function competitorActivityIndex(){
        return view('report.competitoractivity-report');
    }

    public function promoActivityIndex(){
        return view('report.promoactivity-report');
    }
   
    public function posmActivityIndex(){
        return view('report.posmactivity-report');
    }

    public function attendanceIndex(){
        return view('report.attendance-report');
    }
    
    public function attendanceForm(){
        return view('report.form.attendance-form');
    }

    public function sellInData(Request $request, SellinFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');
        
        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {



            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $data = SummarySellIn::where('region_id', $value->region_id)->get();
                }
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $data = SummarySellIn::where('area_id', $value->area_id)->get();
                }
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $data = SummarySellIn::where('store_id', $value->store_id)->get();
                }
            }
            else{
                $data = SummarySellIn::all();
            }

            $filter = $data;

            /* If filter */
            if($request['byRegion']){
                $filter = $data->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $data->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $data->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $data->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $data->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $filter = $data->where('region_id', $value->region_id);
                }
            }

            return Datatables::of($filter->all())
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistorySellIn::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['dedicate'] = $detail->dedicate;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = $transaction->quantity;
                        $collection['unit_price'] = $transaction->unit_price;
                        $collection['value'] = $transaction->value;
                        $collection['value_pf_mr'] = $transaction->value_pf_mr;
                        $collection['value_pf_tr'] = $transaction->value_pf_tr;
                        $collection['value_pf_ppe'] = $transaction->value_pf_ppe;
                        $collection['role'] = $detail->role;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['byRegion']){
                $filter = $historyData->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $historyData->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $historyData->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $historyData->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $historyData->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $filter = $data->where('region_id', $value->region_id);
                }
            }

            if ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $filter = $data->where('area_id', $value->area_id);
                }
            }
            
            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $filter = $data->where('store_id', $value->store_id);
                }
            }



            return Datatables::of($filter->all())
            ->make(true);

        }

    }

    public function sellOutData(Request $request, SellOutFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        
        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {



            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $data = SummarySellOut::where('region_id', $value->region_id)->get();
                }
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $data = SummarySellOut::where('area_id', $value->area_id)->get();
                }
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $data = SummarySellOut::where('store_id', $value->store_id)->get();
                }
            }
            else{
                $data = SummarySellOut::all();
            }

            $filter = $data;

            /* If filter */
            if($request['byRegion']){
                $filter = $data->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $data->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $data->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $data->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $data->where('user_id', $request['byEmployee']);
            }

            return Datatables::of($filter->all())
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistorySellOut::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['dedicate'] = $detail->dedicate;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = $transaction->quantity;
                        $collection['unit_price'] = $transaction->unit_price;
                        $collection['value'] = $transaction->value;
                        $collection['value_pf_mr'] = $transaction->value_pf_mr;
                        $collection['value_pf_tr'] = $transaction->value_pf_tr;
                        $collection['value_pf_ppe'] = $transaction->value_pf_ppe;
                        $collection['role'] = $detail->role;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['byRegion']){
                $filter = $historyData->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $historyData->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $historyData->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $historyData->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $historyData->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $filter = $data->where('region_id', $value->region_id);
                }
            }

            if ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $filter = $data->where('area_id', $value->area_id);
                }
            }
            
            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $filter = $data->where('store_id', $value->store_id);
                }
            }
            return Datatables::of($filter->all())
            ->make(true);

        }

    }

    public function retConsumentData(Request $request, RetConsumentFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        
        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {



            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $data = SummaryRetConsument::where('region_id', $value->region_id)->get();
                }
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $data = SummaryRetConsument::where('area_id', $value->area_id)->get();
                }
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $data = SummaryRetConsument::where('store_id', $value->store_id)->get();
                }
            }
            else{
                $data = SummaryRetConsument::all();
            }


            $filter = $data;

            /* If filter */
            if($request['byRegion']){
                $filter = $data->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $data->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $data->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $data->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $data->where('user_id', $request['byEmployee']);
            }

            return Datatables::of($filter->all())
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistoryRetConsument::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['dedicate'] = $detail->dedicate;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = $transaction->quantity;
                        $collection['unit_price'] = $transaction->unit_price;
                        $collection['value'] = $transaction->value;
                        $collection['value_pf_mr'] = $transaction->value_pf_mr;
                        $collection['value_pf_tr'] = $transaction->value_pf_tr;
                        $collection['value_pf_ppe'] = $transaction->value_pf_ppe;
                        $collection['role'] = $detail->role;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['byRegion']){
                $filter = $historyData->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $historyData->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $historyData->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $historyData->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $historyData->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $filter = $data->where('region_id', $value->region_id);
                }
            }

            if ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $filter = $data->where('area_id', $value->area_id);
                }
            }
            
            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $filter = $data->where('store_id', $value->store_id);
                }
            } 
            return Datatables::of($filter->all())
            ->make(true);

        }

    }
    
    public function retDistributorData(Request $request, RetDistributorFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        
        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $data = SummaryRetDistributor::where('region_id', $value->region_id)->get();
                }
            }
            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $data = SummaryRetDistributor::where('area_id', $value->area_id)->get();
                }
            }
            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $data = SummaryRetDistributor::where('store_id', $value->store_id)->get();
                }
            }
            else{
                $data = SummaryRetDistributor::all();
            }

            $filter = $data;

            /* If filter */
            if($request['byRegion']){
                $filter = $data->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $data->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $data->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $data->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $data->where('user_id', $request['byEmployee']);
            }

            return Datatables::of($filter->all())
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistoryRetDistributor::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['dedicate'] = $detail->dedicate;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = $transaction->quantity;
                        $collection['unit_price'] = $transaction->unit_price;
                        $collection['value'] = $transaction->value;
                        $collection['value_pf_mr'] = $transaction->value_pf_mr;
                        $collection['value_pf_tr'] = $transaction->value_pf_tr;
                        $collection['value_pf_ppe'] = $transaction->value_pf_ppe;
                        $collection['role'] = $detail->role;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['byRegion']){
                $filter = $historyData->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $historyData->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $historyData->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $historyData->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $historyData->where('user_id', $request['byEmployee']);
            }
        
            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $filter = $data->where('region_id', $value->region_id);
                }
            }

            if ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $filter = $data->where('area_id', $value->area_id);
                }
            }
            
            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $filter = $data->where('store_id', $value->store_id);
                }
            }

            return Datatables::of($filter->all())
            ->make(true);

        }

    }

    public function tbatData(Request $request, TbatFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');
        
        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {



            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $data = SummaryTbat::where('region_id', $value->region_id)->get();
                }
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $data = SummaryTbat::where('area_id', $value->area_id)->get();
                }
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $data = SummaryTbat::where('store_id', $value->store_id)->get();
                }
            }
            else{
                $data = SummaryTbat::all();
            }
            

            $filter = $data;

            /* If filter */
            if($request['byRegion']){
                $filter = $data->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $data->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $data->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $data->where('storeId', $request['byStore']);
            }

            if($request['byStore2']){
                $filter = $data->where('storeDestinationId', $request['byStore2']);
            }

            if($request['byEmployee']){
                $filter = $data->where('user_id', $request['byEmployee']);
            }



            return Datatables::of($filter->all())
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistoryTbat::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['store_destinationId'] = $detail->store_destinationId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['dedicate'] = $detail->dedicate;
                        $collection['store_destination_name_1'] = $detail->store_destination_name_1;
                        $collection['store_destination_name_2'] = $detail->store_destination_name_2;
                        $collection['store_destination_id'] = $detail->store_destination_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = $transaction->quantity;
                        $collection['unit_price'] = $transaction->unit_price;
                        $collection['value'] = $transaction->value;
                        $collection['value_pf_mr'] = $transaction->value_pf_mr;
                        $collection['value_pf_tr'] = $transaction->value_pf_tr;
                        $collection['value_pf_ppe'] = $transaction->value_pf_ppe;
                        $collection['role'] = $detail->role;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['byRegion']){
                $filter = $historyData->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $historyData->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $historyData->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $historyData->where('storeId', $request['byStore']);
            }

            if($request['byStore2']){
                $filter = $historyData->where('storeDestinationId', $request['byStore2']);
            }

            if($request['byEmployee']){
                $filter = $historyData->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $filter = $data->where('region_id', $value->region_id);
                }
            }

            if ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $filter = $data->where('area_id', $value->area_id);
                }
            }
            
            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $filter = $data->where('store_id', $value->store_id);
                }
            }

            return Datatables::of($filter->all())
            ->make(true);

        }

    }

    public function freeproductData(Request $request, FreeProductFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $data = SummaryFreeProduct::where('region_id', $value->region_id)->get();
                }
            }
            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $data = SummaryFreeProduct::where('area_id', $value->area_id)->get();
                }
            }
            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $data = SummaryFreeProduct::where('store_id', $value->store_id)->get();
                }
            }
            else{
                $data = SummaryFreeProduct::all();
            }

            $filter = $data;

            /* If filter */
            if($request['byRegion']){
                $filter = $data->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $data->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $data->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $data->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $data->where('user_id', $request['byEmployee']);
            }

            return Datatables::of($filter->all())
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistoryFreeProduct::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['dedicate'] = $detail->dedicate;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = $transaction->quantity;
                        $collection['unit_price'] = $transaction->unit_price;
                        $collection['value'] = $transaction->value;
                        $collection['value_pf_mr'] = $transaction->value_pf_mr;
                        $collection['value_pf_tr'] = $transaction->value_pf_tr;
                        $collection['value_pf_ppe'] = $transaction->value_pf_ppe;
                        $collection['role'] = $detail->role;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['byRegion']){
                $filter = $historyData->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $historyData->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $historyData->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $historyData->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $historyData->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $filter = $data->where('region_id', $value->region_id);
                }
            }

            if ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $filter = $data->where('area_id', $value->area_id);
                }
            }
            
            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $filter = $data->where('store_id', $value->store_id);
                }
            }

            return Datatables::of($filter->all())
            ->make(true);

        }
        
    }

    public function sohData(Request $request, SohFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');


        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $data = SummarySoh::where('region_id', $value->region_id)->get();
                }
            }
            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $data = SummarySoh::where('area_id', $value->area_id)->get();
                }
            }
            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $data = SummarySoh::where('store_id', $value->store_id)->get();
                }
            }
            else{
                $data = SummarySoh::all();
            }


            $filter = $data;

            /* If filter */
            if($request['byRegion']){
                $filter = $data->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $data->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $data->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $data->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $data->where('user_id', $request['byEmployee']);
            }

            return Datatables::of($filter->all())
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistorySoh::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['dedicate'] = $detail->dedicate;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = $transaction->quantity;
                        $collection['unit_price'] = $transaction->unit_price;
                        $collection['value'] = $transaction->value;
                        $collection['value_pf_mr'] = $transaction->value_pf_mr;
                        $collection['value_pf_tr'] = $transaction->value_pf_tr;
                        $collection['value_pf_ppe'] = $transaction->value_pf_ppe;
                        $collection['role'] = $detail->role;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['byRegion']){
                $filter = $historyData->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $historyData->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $historyData->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $historyData->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $historyData->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $filter = $data->where('region_id', $value->region_id);
                }
            }

            if ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $filter = $data->where('area_id', $value->area_id);
                }
            }
            
            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $filter = $data->where('store_id', $value->store_id);
                }
            }
            return Datatables::of($filter->all())
            ->make(true);

        }

    }

    public function sosData(Request $request, SosFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $data = SummarySos::where('region_id', $value->region_id)->get();
                }
            }
            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $data = SummarySos::where('area_id', $value->area_id)->get();
                }
            }
            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $data = SummarySos::where('store_id', $value->store_id)->get();
                }
            }
            else{
                $data = SummarySos::all();
            }

            $filter = $data;

            /* If filter */
            if($request['byRegion']){
                $filter = $data->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $data->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $data->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $data->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $data->where('user_id', $request['byEmployee']);
            }

            return Datatables::of($filter->all())
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistorySos::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['dedicate'] = $detail->dedicate;
                        $collection['store_id'] = $detail->store_id;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['model'] = $transaction->model;
                        $collection['group'] = $transaction->group;
                        $collection['category'] = $transaction->category;
                        $collection['product_name'] = $transaction->product_name;
                        $collection['quantity'] = $transaction->quantity;
                        $collection['unit_price'] = $transaction->unit_price;
                        $collection['value'] = $transaction->value;
                        $collection['value_pf_mr'] = $transaction->value_pf_mr;
                        $collection['value_pf_tr'] = $transaction->value_pf_tr;
                        $collection['value_pf_ppe'] = $transaction->value_pf_ppe;
                        $collection['role'] = $detail->role;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['byRegion']){
                $filter = $historyData->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $historyData->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $historyData->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $historyData->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $historyData->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $filter = $data->where('region_id', $value->region_id);
                }
            }

            if ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $filter = $data->where('area_id', $value->area_id);
                }
            }
            
            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $filter = $data->where('store_id', $value->store_id);
                }
            }

            return Datatables::of($filter->all())
            ->make(true);

        }

    }

    public function displayShareData(Request $request, DisplayShareFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;
        if(($monthRequest == $monthNow) && ($yearRequest == $yearNow)) {



            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $data = SummaryDisplayShare::where('region_id', $value->region_id)->get();
                }
            }

            elseif ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $data = SummaryDisplayShare::where('area_id', $value->area_id)->get();
                }
            }

            elseif (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $data = SummaryDisplayShare::where('store_id', $value->store_id)->get();
                }
            }
            else{
                $data = SummaryDisplayShare::all();
            }          

            $filter = $data;

            /* If filter */
            if($request['byRegion']){
                $filter = $data->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $data->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $data->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $data->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $data->where('user_id', $request['byEmployee']);
            }

            return Datatables::of($filter)
            ->make(true);

        }else{ // Fetch data from history

            $historyData = new Collection();

            $history = HistoryDisplayShare::where('year', $yearRequest)
                        ->where('month', $monthRequest)->get();

            foreach ($history as $data) {

                $details = json_decode($data->details);

                foreach ($details as $detail) {

                    foreach ($detail->transaction as $transaction) {

                        $collection = new Collection();

                        /* Get Data and Push them to collection */
                        $collection['id'] = $data->id;
                        $collection['region_id'] = $detail->region_id;
                        $collection['area_id'] = $detail->area_id;
                        $collection['district_id'] = $detail->district_id;
                        $collection['storeId'] = $detail->storeId;
                        $collection['user_id'] = $detail->user_id;
                        $collection['week'] = $detail->week;
                        $collection['distributor_code'] = $detail->distributor_code;
                        $collection['distributor_name'] = $detail->distributor_name;
                        $collection['region'] = $detail->region;
                        $collection['channel'] = $detail->channel;
                        $collection['sub_channel'] = $detail->sub_channel;
                        $collection['area'] = $detail->area;
                        $collection['district'] = $detail->district;
                        $collection['store_name_1'] = $detail->store_name_1;
                        $collection['store_name_2'] = $detail->store_name_2;
                        $collection['store_id'] = $detail->store_id;
                        $collection['dedicate'] = $detail->dedicate;
                        $collection['nik'] = $detail->nik;
                        $collection['promoter_name'] = $detail->promoter_name;
                        $collection['date'] = $detail->date;
                        $collection['category'] = $transaction->category;
                        $collection['philips'] = $transaction->philips;
                        $collection['all'] = $transaction->all;
                        $collection['percentage'] = $transaction->percentage;
                        $collection['role'] = $detail->role;
                        $collection['spv_name'] = $detail->spv_name;
                        $collection['dm_name'] = $detail->dm_name;
                        $collection['trainer_name'] = $detail->trainer_name;

                        $historyData->push($collection);

                    }

                }

            }

            $filter = $historyData;

            /* If filter */
            if($request['byRegion']){
                $filter = $historyData->where('region_id', $request['byRegion']);
            }

            if($request['byArea']){
                $filter = $historyData->where('area_id', $request['byArea']);
            }

            if($request['byDistrict']){
                $filter = $historyData->where('district_id', $request['byDistrict']);
            }

            if($request['byStore']){
                $filter = $historyData->where('storeId', $request['byStore']);
            }

            if($request['byEmployee']){
                $filter = $historyData->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $filter = $data->where('region_id', $value->region_id);
                }
            }

            if ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $filter = $data->where('area_id', $value->area_id);
                }
            }
            
            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $filter = $data->where('store_id', $value->store_id);
                }
            }

            return Datatables::of($filter->all())
            ->make(true);

        }

    }

    public function maintenanceRequestData(Request $request, MaintenanceRequestFilters $filters){

        // Check data summary atau history
        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

        
            // $withFilter = MaintenanceRequest::filter($filters)->get();


            $data = MaintenanceRequest::filter($filters)
                    ->join('regions', 'maintenance_requests.region_id', '=', 'regions.id')
                    ->join('areas', 'maintenance_requests.area_id', '=', 'areas.id')
                    ->join('stores', 'maintenance_requests.store_id', '=', 'stores.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('users', 'maintenance_requests.user_id', '=', 'users.id')
                    ->select('maintenance_requests.*', 'maintenance_requests.photo as photo2', 'regions.name as region_name', 'areas.name as area_name', 'districts.name as district_name', 'stores.district_id', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.store_id as storeid', 'stores.dedicate', 'users.name as user_name')
                    ->get();

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){

                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $data->where('date','>=',$date1)->where('date','<=',$date2);
            }

            if($request['byStore']){
                $filter = $data->where('store_id', $request['byStore']);
            }

            if($request['byDistrict']){
                $filter = $data->where('area_id', $request['byDistrict']);
            }

            if($request['byArea']){
                $filter = $data->where('area_id', $request['byArea']);
            }

            if($request['byRegion']){
                $filter = $data->where('region_id', $request['byRegion']);
            }

            if($request['byEmployee']){
                $filter = $data->where('user_id', $request['byEmployee']);
            }

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $filter = $data->where('region_id', $value->region_id);
                }
            }

            if ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $filter = $data->where('area_id', $value->area_id);
                }
            }
            
            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $filter = $data->where('store_id', $value->store_id);
                }
            }

            return Datatables::of($filter->all())
            ->editColumn('photo', function ($item) {
                $folderPath = explode('/', $item->photo);
                $folder = $folderPath[5].'/'.$folderPath[6].'/'.$folderPath[7];
                $files = File::allFiles($folder);
                $images = '';
                foreach ($files as $file)
                {
                    $images .= "<img src='".asset((string)$file)."' height='100px'>\n";
                }
                    return $images;
                })
            ->editColumn('photo2', function ($item) {
                $folderPath = explode('/', $item->photo2);
                $folder = $folderPath[5].'/'.$folderPath[6].'/'.$folderPath[7];
                $files = File::allFiles($folder);
                $images = '';
                foreach ($files as $file)
                {
                    $images .= asset((string)$file)."\n";
                }
                    return $images;
                })
            ->rawColumns(['photo'])
            ->make(true);

    }

    public function competitorActivityData(Request $request){

        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

            $data = CompetitorActivity::
                      join('stores', 'competitor_activities.store_id', '=', 'stores.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
                    ->join('users', 'competitor_activities.user_id', '=', 'users.id')
                    ->join('group_competitors', 'competitor_activities.groupcompetitor_id', '=', 'group_competitors.id')
                    ->select('competitor_activities.*', 'competitor_activities.photo as photo2','regions.name as region_name', 'regions.id as region_id', 'areas.name as area_name', 'areas.id as area_id', 'districts.name as district_name', 'stores.district_id','stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.store_id as storeid', 'stores.dedicate', 'users.name as user_name', 'group_competitors.name as group_competitor')
                    ->get();

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $data->where('date','>=',$date1)->where('date','<=',$date2);
            }

            if($request['byStore']){
                $filter = $data->where('store_id', $request['byStore']);
            }

            if($request['byDistrict']){
                $filter = $data->where('district_id', $request['byDistrict']);
            }

            if($request['byArea']){
                $filter = $data->where('area_id', $request['byArea']);
            }

            if($request['byRegion']){
                $filter = $data->where('region_id', $request['byRegion']);
            }

            if($request['byEmployee']){
                $filter = $data->where('user_id', $request['byEmployee']);
            }

            if($request['byGroupCompetitor']){
                $filter = $data->where('groupcompetitor_id', $request['byGroupCompetitor']);
            }


            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $filter = $data->where('region_id', $value->region_id);
                }
            }

            if ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $filter = $data->where('area_id', $value->area_id);
                }
            }
            
            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $filter = $data->where('store_id', $value->store_id);
                }
            }

            return Datatables::of($filter->all())
            ->editColumn('photo', function ($item) {
                // $folderPath = explode('/', $item->photo);
                // $folder = $folderPath[5].'/'.$folderPath[6].'/'.$folderPath[7];
                // $files = File::allFiles($folder);
                $images = '';
                // foreach ($files as $file)
                // {
                    $images .= "<img src='".$item->photo."' height='100px'>\n";
                // }
                    return $images;
                })
            ->editColumn('photo2', function ($item) {
                $folderPath = explode('/', $item->photo2);
                $folder = $folderPath[5].'/'.$folderPath[6].'/'.$folderPath[7];
                $files = File::allFiles($folder);
                $images = '';
                foreach ($files as $file)
                {
                    $images .= asset((string)$file)."\n";
                }
                    return $images;
                })
            ->rawColumns(['photo'])
            ->make(true);

    }

    public function promoActivityData(Request $request){

        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

            $data = PromoActivity::
                    join('promo_activity_details', 'promo_activity_details.promoactivity_id', '=', 'promo_activities.id')
                    ->join('stores', 'promo_activities.store_id', '=', 'stores.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
                    ->join('users', 'promo_activities.user_id', '=', 'users.id')
                    ->join('products', 'promo_activity_details.product_id', '=', 'products.id')
                    ->select('promo_activities.*', 'promo_activity_details.product_id', 'promo_activities.photo as photo2', 'regions.id as region_id', 'areas.id as area_id', 'districts.id as district_id', 'regions.name as region_name', 'areas.name as area_name', 'districts.name as district_name', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.store_id as storeid', 'stores.dedicate', 'users.name as user_name', 'products.model as product_model', 'products.name as product_name', 'products.variants as product_variants')
                    ->get();

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $data->where('date','>=',$date1)->where('date','<=',$date2);
            }

            if($request['byStore']){
                $filter = $data->where('store_id', $request['byStore']);
            }

            if($request['byDistrict']){
                $filter = $data->where('district_id', $request['byDistrict']);
            }

            if($request['byArea']){
                $filter = $data->where('area_id', $request['byArea']);
            }

            if($request['byRegion']){
                $filter = $data->where('region_id', $request['byRegion']);
            }

            if($request['byEmployee']){
                $filter = $data->where('user_id', $request['byEmployee']);
            }

            if($request['byProduct']){
                $filter = $data->where('product_id', $request['byProduct']);
            }

            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $filter = $data->where('region_id', $value->region_id);
                }
            }

            if ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $filter = $data->where('area_id', $value->area_id);
                }
            }
            
            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $filter = $data->where('store_id', $value->store_id);
                }
            }

            return Datatables::of($filter->all())
            ->editColumn('photo', function ($item) {
                // $folderPath = explode('/', $item->photo);
                // $folder = $folderPath[5].'/'.$folderPath[6].'/'.$folderPath[7];
                // $files = File::allFiles($folder);
                $images = '';
                // foreach ($files as $file)
                // {
                    $images .= "<img src='".$item->photo."' height='100px'>\n";
                // }
                    return $images;
                })
            ->editColumn('photo2', function ($item) {
                $folderPath = explode('/', $item->photo2);
                $folder = $folderPath[5].'/'.$folderPath[6].'/'.$folderPath[7];
                $files = File::allFiles($folder);
                $images = '';
                foreach ($files as $file)
                {
                    $images .= asset((string)$file)."\n";
                }
                    return $images;
                })
            ->rawColumns(['photo'])
            ->make(true);

    }

    public function posmActivityData(Request $request){

        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

            $data = PosmActivity::
                    join('posm_activity_details', 'posm_activity_details.posmactivity_id', '=', 'posm_activities.id')
                    ->join('stores', 'posm_activities.store_id', '=', 'stores.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
                    ->join('users', 'posm_activities.user_id', '=', 'users.id')
                    ->join('posms', 'posm_activity_details.posm_id', '=', 'posms.id')
                    ->join('group_products', 'posms.groupproduct_id', '=', 'group_products.id')
                    ->select('posm_activities.*', 'posm_activity_details.photo as photo2', 'regions.id as region_id', 'areas.id as area_id', 'districts.id as district_id', 'regions.name as region_name', 'areas.name as area_name', 'districts.name as district_name', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.store_id as storeid', 'stores.dedicate', 'users.name as user_name', 'posms.name as posm_name', 'group_products.name as group_product', 'posm_activity_details.quantity', 'posm_activity_details.photo')
                    ->get();

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $data->where('date','>=',$date1)->where('date','<=',$date2);
            }

            if($request['byStore']){
                $filter = $data->where('store_id', $request['byStore']);
            }

            if($request['byDistrict']){
                $filter = $data->where('district_id', $request['byDistrict']);
            }

            if($request['byArea']){
                $filter = $data->where('area_id', $request['byArea']);
            }

            if($request['byRegion']){
                $filter = $data->where('region_id', $request['byRegion']);
            }

            if($request['byEmployee']){
                $filter = $data->where('user_id', $request['byEmployee']);
            }

            if($request['byProduct']){
                $filter = $data->where('product_id', $request['byProduct']);
            }


            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $filter = $data->where('region_id', $value->region_id);
                }
            }

            if ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $filter = $data->where('area_id', $value->area_id);
                }
            }
            
            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $filter = $data->where('store_id', $value->store_id);
                }
            }

            return Datatables::of($filter->all())
            ->editColumn('photo', function ($item) {
                // $folderPath = explode('/', $item->photo);
                // $folder = $folderPath[5].'/'.$folderPath[6].'/'.$folderPath[7];
                // $files = File::allFiles($folder);
                $images = '';
                // foreach ($files as $file)
                // {
                    $images .= "<img src='".$item->photo."' height='100px'>\n";
                // }
                    return $images;
                })
            ->editColumn('photo2', function ($item) {
                $folderPath = explode('/', $item->photo2);
                $folder = $folderPath[5].'/'.$folderPath[6].'/'.$folderPath[7];
                $files = File::allFiles($folder);
                $images = '';
                foreach ($files as $file)
                {
                    $images .= asset((string)$file)."\n";
                }
                    return $images;
                })
            ->rawColumns(['photo'])
            ->make(true);

    }

    public function attendanceData(Request $request){

        $monthRequest = Carbon::parse($request['searchMonth'])->format('m');
        $monthNow = Carbon::now()->format('m');
        $yearRequest = Carbon::parse($request['searchMonth'])->format('Y');
        $yearNow = Carbon::now()->format('Y');

            $data = Attendance::
                    join('employee_stores', 'employee_stores.user_id', '=', 'attendances.user_id')
                    ->join('stores', 'employee_stores.store_id', '=', 'stores.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
                    ->join('users', 'attendances.user_id', '=', 'users.id')
                    ->groupBy('attendances.user_id')
                    ->select('attendances.*', 'users.nik as user_nik', 'users.name as user_name', 'users.nik as user_nik', 'users.role as user_role')//,DB::raw('count(*) as total_hk'))
                    // ->where('attendances.status', '!=', 'Off')
                    ->get();

            $filter = $data;

            /* If filter */
            if($request['searchMonth']){
                $month = Carbon::parse($request['searchMonth'])->format('m');
                $year = Carbon::parse($request['searchMonth'])->format('Y');
                // $filter = $data->where('month', $month)->where('year', $year);
                $date1 = "$year-$month-01";
                $date2 = date('Y-m-d', strtotime('+1 month', strtotime($date1)));
                $date2 = date('Y-m-d', strtotime('-1 day', strtotime($date2)));

                $filter = $data->where('date','>=',$date1)->where('date','<=',$date2);
            }

            if($request['byStore']){
                $filter = $data->where('store_id', $request['byStore']);
            }

            if($request['byDistrict']){
                $filter = $data->where('district_id', $request['byDistrict']);
            }

            if($request['byArea']){
                $filter = $data->where('area_id', $request['byArea']);
            }

            if($request['byRegion']){
                $filter = $data->where('region_id', $request['byRegion']);
            }

            if($request['byEmployee']){
                $filter = $data->where('user_id', $request['byEmployee']);
            }
            
            if ($userRole == 'RSM') {
                $region = RsmRegion::where('user_id', $userId)->get();
                foreach ($region as $key => $value) {
                    $filter = $data->where('region_id', $value->region_id);
                }
            }

            if ($userRole == 'DM') {
                $area = DmArea::where('user_id', $userId)->get();
                foreach ($area as $key => $value) {
                    $filter = $data->where('area_id', $value->area_id);
                }
            }
            
            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = EmployeeStore::where('user_id', $userId)->get();
                foreach ($store as $key => $value) {
                    $filter = $data->where('store_id', $value->store_id);
                }
            }

            return Datatables::of($filter->all())
            ->addColumn('total_hk', function ($item) {
                $month = Carbon::parse($item->date)->format('m');
                $year = Carbon::parse($item->date)->format('Y');
                $minDate = "$year-$month-01";
                $maxDate = date('Y-m-d', strtotime('+1 month', strtotime($minDate)));
                $maxDate = date('Y-m-d', strtotime('-1 day', strtotime($maxDate)));
                $maxDate = date('Y-m-d');

                $dataD = Attendance::
                        select(DB::raw('count(*) as total_hk'))
                        ->where('attendances.status', '!=', 'Off')
                        ->where('attendances.status', '!=', 'Sakit')
                        ->where('attendances.status', '!=', 'Izin')
                        ->where('attendances.status', '!=', 'Pending Sakit')
                        ->where('attendances.status', '!=', 'Pending Izin')
                        ->where('attendances.status', '!=', 'Alpha')
                        ->where('attendances.date','>=',$minDate)
                        ->where('attendances.date','<=',$maxDate)
                        ->where('attendances.user_id',$item->user_id)
                        ->get()->all();
                $hk = 0;
                foreach ($dataD as $key => $value) {
                    $hk = $value->total_hk;
                }

                return "$hk";
                
            })
            ->addColumn('attendance_details', function ($item) {
                // return 'kampret';
                $month = Carbon::parse($item->date)->format('m');
                $year = Carbon::parse($item->date)->format('Y');
                $minDate = "$year-$month-01";
                $maxDate = date('Y-m-d', strtotime('+1 month', strtotime($minDate)));
                $maxDate = date('Y-m-d', strtotime('-1 day', strtotime($maxDate)));
                    $status = ['Alpha','Masuk',     'Sakit',    'Izin',     'Pending Sakit','Pending Izin', 'Off'];
                    $warna = ['#e74c3c','#2ecc71',  '#3498db',  '#e67e22',  '#f1c40f',      '#f1c40f',      '#95a5a6'];
                    $text = ['#ecf0f1','#ecf0f1',  '#ecf0f1',  '#ecf0f1',  '#ecf0f1',      '#ecf0f1',      '#ecf0f1'];
                    $tomorrowColor = "#ecf0f1";
                // return $minDate.' / '.$maxDate;

                    /* Get data from attendanceDetails then convert them into colored table */
                    // return $item->user_id;
                    $dataDetail = Attendance::
                        select('attendances.*')
                        ->where('attendances.date','>=',$minDate)
                        ->where('attendances.date','<=',$maxDate)
                        ->where('attendances.user_id',$item->user_id)
                        ->orderBy('id','asc')
                        ->get()->all();
                    foreach ($dataDetail as $key => $value) {
                        $statusAttendance[] = $value->status;
                    }
                    // return $statusAttendance;
                    $report = '<table><tr>';

                    /* Repeat as much as max day in month */
                    
                    $totalDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    for ($i=1; $i <= $totalDay ; $i++) { 

                        $index = 0;
                        $bgColor = $warna[0];
                        $textColor = $text[0];
                        foreach ($status as $key => $value) {
                            // $index = $key;
                            // if (isset($statusAttendance[$i-1])) {
                                if ($value == $statusAttendance[$i-1]) {
                                    $bgColor = $warna[$key];
                                    $textColor = $text[$key];
                                    $index = $key;
                                    break;
                                }
                            // }
                        }

                        $dateNow = Carbon::now()->format('Y-m-d');
                        $dateNow = explode('-', $dateNow);
                        $dateI = date("$year-$month-$i");
                        $dateI = explode('-', $dateI);


                        if ($dateI[2] > $dateNow[2]) {
                            $bgColor = $tomorrowColor; 
                            $textColor = 'black';
                        }

                        if (!isset($bgColor)) {
                            $bgColor="#34495e";
                        }

                        if ($index == 1) {
                            $report .= "<td 
                            class='col-md-12 text-center open-attendance-detail-modal btn btn-primary' data-target='#attendance-detail-modal' data-toggle='modal' data-url='util/attendancedetail' data-title='Attendance Detail' data-employee-name='".$item->user_name."' data-employee-nik='".$item->user_nik."' data-id='".$item->id."'
                            style='background-color: $bgColor;color:$textColor;'
                            >";
                        }else{
                            $report .= "<td 
                            class='col-md-12 text-center'
                            style='background-color: $bgColor;color:$textColor;'
                            >";
                        }
                        
                        $report .= "<b>$i</b><br>".$status[$index]."<td>";
                    }

                    $report .= '</tr></table>';
                    return $report;
                })
            ->addColumn('attendance_detail_excell', function ($item) {
                $month = Carbon::parse($item->date)->format('m');
                $year = Carbon::parse($item->date)->format('Y');
                $minDate = "$year-$month-01";
                $maxDate = date('Y-m-d');

                    $status = ['Alpha','Masuk',     'Sakit',    'Izin',     'Pending Sakit','Pending Izin', 'Off'];

                    /* Get data from attendanceDetails then convert them into colored table */
                    $dataDetail = Attendance::
                        select('attendances.*')
                        ->where('attendances.date','>=',$minDate)
                        ->where('attendances.date','<=',$maxDate)
                        ->where('attendances.user_id',$item->user_id)
                        ->orderBy('id','asc')
                        ->get()->all();
                        $statusAttendance = '';
                    foreach ($dataDetail as $key => $value) {
                        if ($key==0) {
                            $statusAttendance .= $value->status;
                        }else{
                            $statusAttendance .= ','.$value->status;
                        }
                    }

                    return $statusAttendance;
                })
            ->rawColumns(['attendance_details'])
            ->make(true);

    }

}
