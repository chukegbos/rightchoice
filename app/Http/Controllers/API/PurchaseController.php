<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Store;
use App\Purchase;
use App\Inventory;
use App\InventoryStore;
use Illuminate\Support\Facades\Hash;
use DB;

class PurchaseController extends Controller
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
        $query = Purchase::where('deleted_at', NULL);

        if ($request->start_date) {
            $query->where('purchase_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('purchase_date', '<=', $request->end_date . ' 23:59');
        }

        if ($request->name) {
             $query->where('purchase_id', 'like', '%' . $request->name . '%');
        }

        return $query->orderBy('created_at', 'Desc')->groupBy('purchases.purchase_id')->paginate(10);
    }

    public function store(Request $request)
    {
        $products = $request->productQty;

        foreach ($products as $product) {
            $purchases = Purchase::find($product['qtyId']);
            $purchases->outlet = $product['outlet'];
            $purchases->supplier = $product['supplier'];
            $purchases->quantity = $product['quantity'];
            $purchases->total_price = $product['total_price'];
            $purchases->purchase_date = $product['purchase_date'];
            $purchases->update();

            $setProduct = InventoryStore::where('inventory_id', $purchases->product)->where('store_id', $product['outlet'])->where('deleted_at', NULL)->first();

            $setProduct->number = $setProduct->number + $product['quantity'];
            $setProduct->update();
        }

        return $products;
    }

    public function show($purchaseId){
        $purchases =  Purchase::where('purchases.deleted_at', NULL)
            ->where('purchases.purchase_id', $purchaseId)
            ->where('inventories.deleted_at', NULL)
            ->join('inventories', 'purchases.product', '=', 'inventories.id')
            ->join('suppliers', 'purchases.supplier', '=', 'suppliers.id')
            ->where('suppliers.deleted_at', NULL)
            ->join('stores', 'purchases.outlet', '=', 'stores.id')
            ->where('stores.deleted_at', NULL)
            ->orderBy('purchases.created_at', 'Desc')
            ->select(
                'purchases.id as id',
                'purchases.purchase_id as purchase_id',
                'purchases.quantity as quantity',
                'suppliers.supplier_name as supplier_name',
                'stores.name as outlet',
                'inventories.product_name as product_name',
                'purchases.total_price as total_price',
                'purchases.amount_paid as amount_paid',
                'purchases.purchase_date as purchase_date'
            )
            ->get();

        return  response()->json($purchases);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'quantity' => 'required',
            'total_amount' => 'required',
            'total_pay' => 'required',
            'supplier' => 'required',
            'product' => 'required',
        ]);
        $purchase = Purchase::where('deleted_at')->find($id);
        $purchase_quantity = $purchase->quantity;
        $product = Product::findOrFail($request->product);
        $product->quantity = $product->quantity - $purchase_quantity;
        $product->quantity = $product->quantity + $request->quantity;
        $product->update();

        return $purchase->update($request->all());
        //return ['message' => "Success"];
    }

    public function destroy($id)
    {
        $purchase = Purchase::where('deleted_at', NULL)->find($id);
        $product = Inventory::where('deleted_at', NULL)->find($purchase->product);

        if ($product->quantity > $purchase->quantity) {
            $product->quantity = $product->quantity - $purchase->quantity;
            $product->update();
            Purchase::Destroy($id);
            return 'ok';
        }
        else
        {
            return 'You cannot delete this purchase detail because the product has decreased in the Inventory';
        }
        
    }
}
