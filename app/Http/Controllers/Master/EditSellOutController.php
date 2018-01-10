<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Filters\SellOutFilters;
use App\Traits\StringTrait;
use App\Traits\SalesTrait;
use DB;
use Auth;
use App\Store;
use App\SellOut;
use App\SellOutDetail;

class EditSellOutController extends Controller
{
    use StringTrait;
    use SalesTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.sellout');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;
        $data = SellOut::
                    where('sell_outs.deleted_at', null)
                    ->where('sell_out_details.deleted_at', null)
        			->join('sell_out_details', 'sell_outs.id', '=', 'sell_out_details.sellout_id')
                    ->join('stores', 'sell_outs.store_id', '=', 'stores.id')
                    ->join('users', 'sell_outs.user_id', '=', 'users.id')
                    ->join('products', 'sell_out_details.product_id', '=', 'products.id')
                    ->select('sell_outs.week as week', 'users.name as user_name', 'users.nik as user_nik', 'stores.store_id as store_id', 'stores.store_name_1 as store_name_1', 'stores.store_name_2 as store_name_2', 'stores.dedicate as dedicate', 'sell_out_details.id as id', 'sell_out_details.quantity as quantity', 'products.name as product')->get();

            if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
                $store = Store::where('user_id', $userId)
                            ->pluck('stores.store_id');
                $data = $data->whereIn('store_id', $store);
            }
        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(ChannelFilters $filters){
        $data = Channel::filter($filters)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->addColumn('action', function ($item) {

                   return
                    "<a href='#editsellout' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-sellout'><i class='fa fa-pencil'></i></a>
                    <button class='btn btn-danger btn-sm btn-delete deleteButton' data-toggle='confirmation' data-singleton='true' value='".$item->id."'><i class='fa fa-remove'></i></button>";

                })
                ->rawColumns(['action'])
                ->make(true);

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = SellOutDetail::where('id', $id)->first();

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'quantity' => 'required',
            ]);

        $this->updateSellOut($id, $request['quantity']);

        return response()->json(
            ['url' => url('/sellout'), 'method' => $request->_method]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $this->deleteSellOut($id);

        return response()->json($id);
    }
}
