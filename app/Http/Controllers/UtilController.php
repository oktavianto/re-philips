<?php

namespace App\Http\Controllers;

use App\Distributor;
use App\NewsRead;
use App\ProductKnowledgeRead;
use App\StoreDistributor;
use Illuminate\Http\Request;
use App\User;
use App\Store;
use App\AreaApp;
use App\EmployeeStore;
use DB;
use Activity;
use Auth;

class UtilController extends Controller
{
    //
    public function existEmailUser(Request $request){
    	$user = User::where('email', $request->email);

    	if($user->count() > 0){
            if($request->form_method == 'PATCH'){

                $oldUser = User::find($request->userId);

                if($oldUser->email == $request->email){
                    return "true";
                }    
            }    		
    		return "false";
    	}
    	return "true";
    }

    public function existEmailEmployee(Request $request){               
        $employee = Employee::where('email', $request->email);

        if($employee->count() > 0){
            if($request->form_method == 'PATCH'){

                $oldEmployee = Employee::find($request->employeeId);

                if($oldEmployee->email == $request->email){
                    return "true";
                }    
            }  
            return "false";
        }

        return "true";
    }

    public function getStoreForEmployee($userId){        
        // $empStore = EmployeeStore::where('employee_id', $employeeId);
        $empStore = EmployeeStore::where('user_id', $userId);        
        $empStoreIds = $empStore->pluck('store_id');
        $store = Store::where('stores.deleted_at', null)
        			->join('sub_channels', 'stores.subchannel_id', '=', 'sub_channels.id')
                    ->join('channels', 'sub_channels.channel_id', '=', 'channels.id')
                    ->join('global_channels', 'channels.globalchannel_id', '=', 'global_channels.id')
                    ->join('districts', 'stores.district_id', '=', 'districts.id')
                    ->join('areas', 'districts.area_id', '=', 'areas.id')
                    ->join('regions', 'areas.region_id', '=', 'regions.id')
//                    ->join('users', 'stores.user_id', '=', 'users.id')
                    ->whereIn('stores.id', $empStoreIds)
                    ->select('stores.*', 'sub_channels.name as subchannel_name', 'channels.name as channel_name', 'global_channels.name as globalchannel_name', 'districts.name as district_name', 'areas.name as area_name', 'regions.name as region_name')->get();

        foreach ($store as $item){

            if($item->user_id == null){
                $item['spv_name'] = "";
            }else{
                $item['spv_name'] = $item->user->name;
            }

        }

        return response()->json($store);
    }

    public function getDistributorForStore($storeId){

        $storeDist = StoreDistributor::where('store_id', $storeId);
        $storeDistIds = $storeDist->pluck('distributor_id');
        $distributor = Distributor::where('distributors.deleted_at', null)
                    ->whereIn('distributors.id', $storeDistIds)->get();

        return response()->json($distributor);
    }

    public function getAreaApp($id){
        $data = AreaApp::find($id);
        return response()->json($data);
    }

    public function getStore($id){
        $data = Store::find($id);
        return response()->json($data);
    }

    public function getUser($id){
        $data = User::find($id);
        return response()->json($data);
    }

     public function getNewsRead($id){
        $data = NewsRead::where('news_id', $id)
                    ->join('users', 'news_reads.user_id', '=', 'users.id')
                    ->select('users.*', 'news_reads.created_at as read_at')
                    ->get();

        return response()->json($data);
    }

    public function getProductRead($id){
        $data = ProductKnowledgeRead::where('productknowledge_id', $id)
                    ->join('users', 'product_knowledge_reads.user_id', '=', 'users.id')
                    ->select('users.*', 'product_knowledge_reads.created_at as read_at')
                    ->get();

        return response()->json($data);
    }

    public function getUserOnline(){

        $users = Activity::users()->where('user_id', '<>', Auth::user()->id)->orderByUsers('name');

        return response()->json([
            'count' => $users->count(),
            'users' => $users->get(),
        ]);
    }
}
