<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Store;
use App\StoreRequest;
use App\Sale;
use App\Debtor;
use App\User;
use App\Room;
use App\Target;
use App\Inventory;
use App\InventoryStore;
use App\StoreUser;
use Illuminate\Support\Facades\Hash;
use DB;
use Carbon\Carbon;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('api');
    }

    public function index(Request $request)
    {
        return Store::where('deleted_at', NULL)->get();
    }

    public function getInventory(Request $request)
    {
        //return $request->name;
        $query = DB::table('inventory_store')->where('inventory_store.deleted_at', NULL);

            if ($request->store_code) {
                $get_store = Store::where('deleted_at', NULL)->where('code', $request->store_code)->first();
                $myaccount = User::find(auth('api')->user()->id);
                $myaccount->store = $get_store->id;
                $myaccount->update();
                
                $query->where('inventory_store.store_id', $get_store->id);
            }
            else
            {
                $query->where('inventory_store.store_id', auth('api')->user()->store);
            }

            if ($request->name) {
                $query->where('inventories.product_name', 'like', '%' . $request->name . '%');
            }

            $query->join('inventories', 'inventory_store.inventory_id', '=', 'inventories.id')
            ->where('inventories.deleted_at', NULL);
            
        $inventory = $query->paginate(10);
        return response()->json($inventory);
    }


    public function allInventory(Request $request)
    {
        //return $request->name;
        $query = DB::table('inventory_store')->where('inventory_store.deleted_at', NULL);

            if ($request->store_code) {
                $get_store = Store::where('deleted_at', NULL)->where('code', $request->store_code)->first();
                $myaccount = User::find(auth('api')->user()->id);
                $myaccount->store = $get_store->id;
                $myaccount->update();
                
                $query->where('inventory_store.store_id', $get_store->id);
            }
            else
            {
                $query->where('inventory_store.store_id', auth('api')->user()->store);
            }

            $query->join('inventories', 'inventory_store.inventory_id', '=', 'inventories.id')
            ->where('inventories.deleted_at', NULL);
            
        $inventory = $query->get();
        return response()->json($inventory);
    }

    public function tradeinventory(Request $request)
    {
        $query = DB::table('inventory_store')->where('inventory_store.deleted_at', NULL);

            if ($request->store_code) {
                $get_store = Store::where('deleted_at', NULL)->where('code', $request->store_code)->first();
                $myaccount = User::find(auth('api')->user()->id);
                $myaccount->store = $get_store->id;
                $myaccount->update();
                
                $query->where('inventory_store.store_id', $get_store->id);
            }
            else
            {
                $query->where('inventory_store.store_id', auth('api')->user()->store);
            }

            $query->join('inventories', 'inventory_store.inventory_id', '=', 'inventories.id')
            ->where('inventories.deleted_at', NULL)
            ->select(
                'inventories.product_name as product_name',
                'inventories.product_id as product_id',
                'inventories.id as id',
                'inventory_store.number as number',
                'inventory_store.age as age',
                'inventory_store.updated_at  as updated_at'
            );

        $inventory = $query->get();
        return response()->json($inventory);
    }

    public function transferinventory($id)
    {
        $query = DB::table('inventory_store')
            ->where('inventory_store.deleted_at', NULL)
            ->where('inventory_store.inventory_id', $id)
            ->join('inventories', 'inventory_store.inventory_id', '=', 'inventories.id')
            ->where('inventories.deleted_at', NULL)
            ->join('stores', 'inventory_store.store_id', '=', 'stores.id')
            ->where('stores.deleted_at', NULL)
            ->select(
                'stores.name as name',
                'inventory_store.number as number',
                'inventory_store.id as id'
            );

        $inventory = $query->get();
        return response()->json($inventory);
    }

    public function roominventory(Request $request)
    {
        $query = Room::where('rooms.deleted_at', NULL);

            if ($request->store_code) {
                $get_store = Store::where('deleted_at', NULL)->where('code', $request->store_code)->first();
                $myaccount = User::find(auth('api')->user()->id);
                $myaccount->store = $get_store->id;
                $myaccount->update();
                
                $query->where('rooms.store_id', $get_store->id);
            }
            else
            {
                $query->where('rooms.store_id', auth('api')->user()->store);
            }

            $query->join('inventories', 'rooms.inventory_id', '=', 'inventories.id')
            ->where('inventories.deleted_at', NULL);
            
        $inventory = $query->get();
        return response()->json($inventory);
    }

    public function target(Request $request, $code)
    {
        $params = [];
        $params['store'] = Store::where('deleted_at', NULL)->where('code', $code)->first();
        $params['targets'] = Target::where('deleted_at', NULL)->where('store_id', $params['store']->id)->paginate(10);
        return $params;
    }

    public function storetarget(Request $request){
        //return $request;
        $target = Target::where('deleted_at', NULL)->where('store_id', $request->store_id)->where('active', 1)->first();
        if ($target) {
            $target->active = 0;
            $target->update();
        }
        return Target::create([
            'target_amount' => $request->target_amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'accomplished' => 0,
            'products' => 0,
            'active' => 1,
            'store_id' => $request->store_id,
        ]);
    }

    public function discharge()
    {
        $inventory = StoreRequest::where('store_request.deleted_at', NULL)
            ->join('inventories', 'store_request.product_id', '=', 'inventories.id')
            ->where('inventories.deleted_at', NULL)
            ->join('stores', 'store_request.store_id', '=', 'stores.id')
            ->where('stores.deleted_at', NULL)
            ->orderBy('store_request.created_at', 'desc')
            ->select(
                'store_request.req_id as req_id',
                'stores.name as store_name',
                'store_request.id as id',
                'inventories.product_name as product_name',
                'inventories.quantity as quantity',
                'store_request.qty as qty',
                'store_request.sender_id as sender_id',
                'store_request.created_at as created_at',
                'store_request.approved_by as approved_by',
                'store_request.reciever_id as reciever_id',
                'store_request.status as status'
            )
            ->groupBy('store_request.req_id')
        ->paginate(3);
        return response()->json($inventory);
    }  

    public function approve(Request $request)
    {
        $store_request = StoreRequest::where('deleted_at')->find($request->id);

        $product = InventoryStore::where('deleted_at')->find($request->store);
      
        if ($product->number < $store_request->qty) {
            return ['error' => "You cannot disburse more than you dont have in outlet"];
        }
        else{
            $store_request->update([
                'qty' => $request->quantity,
                'approved_by' => auth('api')->user()->id,
                'status' => 'approved',
                'store_ref' => $request->store,
            ]);
            return ['message' => "Success"];
        }
    }

    public function decline($id)
    {
        $store_request = StoreRequest::where('deleted_at')->find($id);
    
        $store_request->update([
            'status' => 'declined',
        ]);
        return ['message' => "Success"];
    }

    public function accept($id)
    {
        $req = StoreRequest::where('deleted_at')->find($id);

        $inventory = InventoryStore::where('deleted_at', NULL)->find($req->store_ref);
        if ($inventory) {
            $inventory->number = $inventory->number - $req->qty;
            $inventory->update();

            $store_inventory = DB::table('inventory_store')
                ->where('deleted_at', NULL)
                ->where('store_id', $req->store_id)
                ->where('inventory_id', $req->product_id)
                ->first();

            $store_inventory_update = DB::table('inventory_store')
                ->where('deleted_at', NULL)
                ->where('store_id', $req->store_id)
                ->where('inventory_id', $req->product_id)
                ->update(['number' => $store_inventory->number + $req->qty]);

            //Updating Status
            $req->reciever_id = auth('api')->user()->id;
            $req->status = 'concluded';
            $req->update();
            return $req;
        }
        else
        {
            return ['error' => "Product does not exist!!!"];
        }
        
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
        ]);

        $code = rand(3,99888888);

        Store::create([
            'name' => ucwords($request->name),
            'address' => $request->address,
            'code' => $code,
            'stock_limit' => ($request->stock_limit) ? $request->stock_limit :'Unlimited',
            /*'target' => ($request->target) ? $request->target :'None',*/
        ]);

        $store = Store::where('deleted_at', NULL)->where('code', $code)->first();
        if ($store) {
            Room::create([
                'store_id' => $store->id,
            ]); 
        }

        /*if ($request->target) {
            Target::create([
                'store_id' => $store->id,
                'target_amount' => $request->target,
                'accomplished' => 0,
                'products' => 0,
                'start_date' => carbon::now(),
            ]);
        }*/
        return ['message' => "Success"];
    }

    public function show($code)
    {
        $params = [];
        $params['store'] = Bar::where('deleted_at', NULL)->where('code', $code)->first();

        if ($params['store']) {
            return $params;
        }
        else
        {
            return response()->json(['error' => 'Store does not exist'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $store = Store::where('deleted_at')->find($id);
        $this->validate($request, [
            'name' => 'required|string|max:255',
        ]);

        if ($request->stock_limit) {
            $stock_limit = $request->stock_limit;
        }
        else{
            $stock_limit = $store->stock_limit;
        }

        /*if ($request->target) {
            $target = $request->target;
            $get_target = Target::where('deleted_at', NULL)->where('store_id', $id)->where('end_date', NULL)->first();

            if ($get_target) {
                $get_target->end_date = Carbon::now();
                $get_target->update();
            }

            Target::create([
                'store_id' => $id,
                'target_amount' => $request->target,
                'accomplished' => 0,
                'products' => 0,
                'start_date' => carbon::now(),
            ]);

        }
        else{
            $target = $store->target;
        }*/

        $store->update([
            'address' => $request->address,
            'name' => ucwords($request->name),
            'stock_limit' => $stock_limit,
        ]);
        return ['message' => "Success"];
    }

    public function destroy(Request $request)
    {
        foreach ($request['selectedItems'] as $store) {
            $getStore = json_decode($store);
            $the_store = Store::find($getStore->id);

            if ($getStore->id != 11) {
                $users = User::where('deleted_at', NULL)->where('store', $getStore->id)->get();
                foreach ($users as $user) {
                    $get_user = User::find($user->id);
                    $get_user->store = 11;
                    $get_user->update();
                }
                Store::Destroy($getStore->id);
            } 
        }

        return 'ok';  
    }

    public function reports(Request $request)
    {
        $params = [];

        $query = Sale::where('sales.deleted_at', NULL);

        if ($request->store_code) {
            $store = Store::where('deleted_at', NULL)->where('code', $request->store_code)->first();
            if ($store) {
                $query->where('sales.store_id', $store->id);
            }
            else{
                return 'Store not found';
            }       
        }

        if ($request->store) {
            $query->where('sales.store_id', $request->store);      
        }
        
        if (auth('api')->user()->role != 3) {
            $query->where('sales.store_id', auth('api')->user()->store);
        }

        //Optional where
        if ($request->start_date) {
            $query->where('sales.main_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('sales.main_date', '<=', $request->end_date . ' 23:59');
        }

        if ($request->mode_of_payment) {
            $query->where('sales.mop', $request->mode_of_payment);
        }

        $report_data = $query->orderBy('sales.main_date', 'Desc')
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->join('stores', 'sales.store_id', '=', 'stores.id')
            ->select(
                'stores.name as store_name',
                'sales.id as id',
                'sales.sale_id as sale_id',
                'users.name as user_name',
                'sales.mop as mop',
                'sales.sec_id as sec_id',
                'sales.cart as cart',
                'sales.initialPrice as initialPrice',
                'sales.totalPrice as totalPrice',
                'sales.discount as discount',
                'sales.percent_discount as percent_discount',
                'sales.amount_paid as amount_paid',
                'sales.main_date as created_at'  
            )->paginate(10);

        $report_data->transform(function($order, $key){
            $order->cart = unserialize($order->cart);
            return $order;
        });

        $params['report_data'] = $report_data;
        $params['stores'] = Store::where('deleted_at', NULL)->get();
        $query->select(DB::raw("Count(*) As total_orders, SUM(totalPrice) As total_amount"));
        $params['summary'] = $query->first();

        return $params;
    }

    public function debtors(Request $request)
    {
        $params = [];

        $query = Debtor::where('debtors.deleted_at', NULL)->where('debtors.status', 0);

        if (auth('api')->user()->role != 3) {
            $store = Store::where('deleted_at', NUll)->find(auth('api')->user()->store);
            $sale = Sale::where('deleted_at', NUll)->where('store_id', $store->id)->first();
                
            $query->where('debtors.trans_id', $sale->sale_id);
        }

        $report_data = $query->orderBy('debtors.created_at', 'Desc')
            ->join('sales', 'debtors.trans_id', '=', 'sales.sale_id')
            ->join('users', 'debtors.user_id', '=', 'users.id')
            ->select(
                'sales.sale_id as sale_id',
                'users.name as customer',
                'users.phone as phone',
                'users.address as address',
                'users.updated_at as updated_at',
                'debtors.amount as amount',
                'debtors.status as status'
            )->paginate(10);

        $report_data->transform(function($order, $key){
            $order->cart = unserialize($order->cart);
            return $order;
        });

        $params['report_data'] = $report_data;

        return $params;
    }

    public function debtorview($sale_id)
    {
        $params = [];
        $query = Debtor::where('debtors.deleted_at', NULL)->where('debtors.trans_id', $sale_id);

        $report_data = $query->orderBy('debtors.created_at', 'Desc')
            ->join('sales', 'debtors.trans_id', '=', 'sales.sale_id')
            ->join('users', 'debtors.user_id', '=', 'users.id')
            ->select(
                'sales.sale_id as sale_id',
                'users.name as customer',
                'sales.cart as cart',
                'sales.totalPrice as totalPrice',
                'sales.amount_paid as amount_paid',
                'debtors.amount as amount',
                'debtors.amount_paid as amount_paid',
                'debtors.status as status'
            )->paginate(10);

        $report_data->transform(function($order, $key){
            $order->cart = unserialize($order->cart);
            return $order;
        });

        $params['report_data'] = $report_data;
        return $params;
    }

    public function addDebt(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required',
        ]);

        $first_debt = Debtor::where('deleted_at', NULL)->where('trans_id', $request->sale_id)->where('status', 0)->first();
        $first_debt->amount_paid = $request->amount;
        $first_debt->update();


        $first_debt_id = $first_debt->id;
        $first_debt_amount = $first_debt->amount;

        $debts = Debtor::where('deleted_at', NULL)->where('trans_id', $request->sale_id)->get();
        foreach ($debts as $debt) {
            $debtor = Debtor::where('deleted_at', NULL)->find($debt->id);
            $debtor->status = 1;
            $debtor->update();
        }

        if ($first_debt_amount != $request->amount) {
            $set_debt = new Debtor();
            $set_debt->user_id = $first_debt->user_id;
            $set_debt->trans_id = $request->sale_id;
            $set_debt->amount = $first_debt_amount - $request->amount;
            $set_debt->status = 0;
            $set_debt->save();
        }
        return 'ok';
    }
}
