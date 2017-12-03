<?php

namespace App\Http\Controllers\Api\Master;

use App\DmArea;
use App\EmployeeStore;
use App\RsmRegion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Filters\StoreFilters;
use App\Traits\StringTrait;
use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use DB;
use App\Store;

class StoreController extends Controller
{
    public function all(){
    	$data = Store::join('districts', 'stores.district_id', '=', 'districts.id')
                ->select('stores.id', 'stores.store_id', 'stores.store_name_1', 'stores.store_name_2', 'stores.longitude',
                'stores.latitude', 'stores.address', 'districts.name as district_name')->get();
    	
    	return response()->json($data);
    }

    public function nearby(Request $request)
    {
        $content = json_decode($request->getContent(), true);
        $distance = 250;

        $user = JWTAuth::parseToken()->authenticate();
        $storeIds = EmployeeStore::where('user_id', $user->id)->pluck('store_id');

    	$data = Store::join('districts', 'stores.district_id', '=', 'districts.id')
                    ->where('latitude', '!=', null)
                    ->where('longitude', '!=', null)
                    ->whereNotIn('id', $storeIds)
                    ->select('stores.id', 'stores.store_id', 'stores.store_name_1', 'stores.store_name_2', 'stores.longitude',
                'stores.latitude', 'stores.address', 'districts.name as district_name');
//                    ->select('id', 'store_name_1 as nama', 'latitude', 'longitude');

        // This will calculate the distance in km
        // if you want in miles use 3959 instead of 6371
        $haversine = '( 6371 * acos( cos( radians('.$content['latitude'].') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$content['longitude'].') ) + sin( radians('.$content['latitude'].') ) * sin( radians( latitude ) ) ) ) * 1000';
        $data = $data->selectRaw("{$haversine} AS distance")->orderBy('distance', 'asc')->whereRaw("{$haversine} <= ?", [$distance]);

        return response()->json($data->get());
    }

    public function bySupervisor(){

        $user = JWTAuth::parseToken()->authenticate();

        $data = Store::where('user_id', $user->id)
                ->join('districts', 'stores.district_id', '=', 'districts.id')
                ->select('stores.id', 'stores.store_id', 'stores.store_name_1', 'stores.store_name_2', 'stores.longitude',
                'stores.latitude', 'stores.address', 'districts.name as district_name')->get();

    	return response()->json($data);

    }

    public function byPromoter(){

        $user = JWTAuth::parseToken()->authenticate();

        $storeIds = EmployeeStore::where('user_id', $user->id)->pluck('store_id');

        $data = Store::whereIn('stores.id', $storeIds)
                ->join('districts', 'stores.district_id', '=', 'districts.id')
                ->select('stores.id', 'stores.store_id', 'stores.store_name_1', 'stores.store_name_2', 'stores.longitude',
                'stores.latitude', 'stores.address', 'districts.name as district_name')->get();

    	return response()->json($data);

    }

    public function updateStore(Request $request){

        $store = Store::find($request->id);

        /* Case kalo ga bisa di update setelah first update */
//        if($store->longitude != null && $store->latitude != null){
//            return response()->json(['status' => false, 'message' => 'Longitude dan latitude untuk store ini telah diinput'], 500);
//        }

        $store->update(['longitude' => $request->longitude, 'latitude' => $request->latitude, 'address' => $request->address]);

        return response()->json(['status' => true, 'message' => 'Update longitude dan latitude store berhasil']);

    }

    public function byArea(Request $request){

        $data = Store::whereHas('district.area', function ($query) use ($request){
                    return $query->where('id', $request->area_id);
                })
                ->join('districts', 'stores.district_id', '=', 'districts.id')
                ->select('stores.id', 'stores.store_id', 'stores.store_name_1', 'stores.store_name_2', 'stores.longitude',
                'stores.latitude', 'stores.address', 'districts.name as district_name')->get();

    	return response()->json($data);

    }

    public function byDm(){

        $user = JWTAuth::parseToken()->authenticate();

        $areaIds = DmArea::where('user_id', $user->id)->pluck('area_id');

        $data = Store::whereHas('district.area', function ($query) use ($areaIds){
                    return $query->whereIn('id', $areaIds);
                })
                ->join('districts', 'stores.district_id', '=', 'districts.id')
                ->select('stores.id', 'stores.store_id', 'stores.store_name_1', 'stores.store_name_2', 'stores.longitude',
                'stores.latitude', 'stores.address', 'districts.name as district_name')->get();

    	return response()->json($data);

    }

    public function byRsm(){

        $user = JWTAuth::parseToken()->authenticate();

        $regionIds = RsmRegion::where('user_id', $user->id)->pluck('region_id');

        $data = Store::whereHas('district.area.region', function ($query) use ($regionIds){
                    return $query->whereIn('id', $regionIds);
                })
                ->join('districts', 'stores.district_id', '=', 'districts.id')
                ->select('stores.id', 'stores.store_id', 'stores.store_name_1', 'stores.store_name_2', 'stores.longitude',
                'stores.latitude', 'stores.address', 'districts.name as district_name')->get();

    	return response()->json($data);

    }

}
