<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Purchase;
use App\Inventory;
use App\Category;
use DB;
use App\Store;
use App\Sale;
use App\Debtor;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('api');
    }

    public function getorder($id)
    {
        $query = Sale::where('sales.deleted_at', NULL)
            ->where('sale_id', $id)
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
            )->get();

        $sale = $query->transform(function($order, $key){
            $order->cart = unserialize($order->cart);
            return $order;
        });

        return $sale;
    }

    public function stat(Request $request)
    {
        $params = [];

        $query_user = User::where('deleted_at', NULL)->where('role', '!=', 0);
        if (auth('api')->user()->role != 3) {
            $query_user->where('users.store', auth('api')->user()->store);
        }

        $params['users'] = $query_user->count();


        $params['customers'] = User::where('deleted_at', NULL)->where('role', 0)->count();
        $params['stores'] = Store::where('deleted_at', NULL)->count();
        
        $query_debtor = Debtor::where('deleted_at', NULL)->where('status', 0);
        if (auth('api')->user()->role != 3) {
            $store = Store::where('deleted_at', NUll)->find(auth('api')->user()->store);
            $sale = Sale::where('deleted_at', NUll)->where('store_id', $store->id)->first();
            $query_debtor->where('trans_id', $sale->sale_id);
        }
        $params['debtors'] = $query_debtor->count();

        $params['inventories'] = Inventory::where('deleted_at', NULL)->count();
        $params['categories'] = Category::where('deleted_at', NULL)->count();
        $params['threshold_inventories'] = Inventory::where('deleted_at', NULL)->whereColumn('threshold', '>', 'quantity')->take(3)->get();
        $params['zero_inventories'] = Inventory::where('deleted_at', NULL)->where('quantity', 0)->take(3)->get();

        $params['store_threshold_inventories'] =  DB::table('inventory_store')
            ->where('inventory_store.deleted_at', NULL)
            ->where('inventory_store.store_id', auth('api')->user()->store)
            ->join('inventories', 'inventory_store.inventory_id', '=', 'inventories.id')
            ->where('inventories.deleted_at', NULL)
            ->whereColumn('inventory_store.threshold', '>', 'inventory_store.number')
            ->take(3)->get();

        $params['store_zero_inventories'] =  DB::table('inventory_store')
            ->where('inventory_store.deleted_at', NULL)
            ->where('inventory_store.store_id', auth('api')->user()->store)
            ->join('inventories', 'inventory_store.inventory_id', '=', 'inventories.id')
            ->where('inventories.deleted_at', NULL)
            ->where('inventory_store.number', 0)
            ->take(3)->get();
        return $params;
    }
}
