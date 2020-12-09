<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use App\Inventory;
use Illuminate\Support\Str;
use App\Purchase;

class InventoryController extends Controller
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
        $params = [];
        $params['categories'] = Category::where('deleted_at', NULL)->get();
        $query =  Inventory::where('inventories.deleted_at', NULL)
            ->join('categories', 'inventories.category', '=', 'categories.id');
            if ($request->name) {
                $query->where('inventories.product_name', 'like', '%' . $request->name . '%')->orWhere('inventories.product_id', 'like', '%' . $request->name . '%');
            }

            if($request->category)
            {
                $query->where('inventories.category', $request->category);
            }

            //return $request;
            $params['inventories'] = $query->select(
                'inventories.id as id',
                'inventories.category as category',
                'categories.name as name',
                'inventories.product_id as product_id',
                'inventories.product_name as product_name',
                'inventories.quantity as quantity',
                'inventories.cost_price as cost_price',
                'inventories.price as price',
                'inventories.unit as unit',
                'inventories.threshold as threshold'
            )
            ->get();
        return $params;
    }

    public function getInventory(Request $request)
    {
        $inventory = Inventory::where('deleted_at', NULL)->get();
        return response()->json($inventory);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'product_name' => 'required|string|max:255',
            'price' => 'required|integer',
            'cost_price' => 'required|integer',
            'threshold' => 'required|integer',
        ]);

        $productId = 'stc'. rand();

        return Inventory::create([
            'product_id' => $productId,
            'product_name' => ucwords($request->product_name),
            'price' => $request->price,
            'category' => $request->category,
            'cost_price' => $request->cost_price,
            'unit' => $request->unit,
            'threshold' => $request->threshold,
        ]);
    }

    public function increase(Request $request)
    {
        $this->validate($request, [
            'number' => 'required|string|max:255'
        ]);

        $inventories = Inventory::where('deleted_at', NULL)->where('category', $request->category)->get();

        foreach ($inventories as $inventory) {
            $product = Inventory::where('deleted_at', NULL)->find($inventory->id);
            $newPrice = (($request->number/100) * $product->price) + $product->price;
            $product->price = $newPrice;
            $product->update();
        }
        return $inventories;
    }


    public function show($id)
    {
        $inventory =  Inventory::where('deleted_at', NULL)->find($id);
        return response()->json($inventory);
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
            'product_name' => 'required|string|max:255',
            'price' => 'required|integer|min:3',
            'cost_price' => 'required|integer',
            'threshold' => 'required|integer',
        ]);
        $inventory = Inventory::where('deleted_at', NULL)->find($id);
        $inventory->product_name = $request->product_name;
        $inventory->price = $request->price;
        $inventory->category = $request->category;
        $inventory->quantity = $request->quantity;
        $inventory->cost_price = $request->cost_price;
        $inventory->unit = $request->unit;
        $inventory->threshold = $request->threshold;
        if($inventory->save()){
            return ['success'=> 'Data updated successfully'];
        }else{
            return ['error' => 'Opps something went wrong'];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  
    public function destroy(Request $request)
    {
        foreach ($request->payload as $product) {
            $getProduct = json_decode($product);
            //$the_product = Inventory::find($getProduct->id);
            Inventory::Destroy($getProduct->id);
        }
        return 'ok';
        
    }

    /** Support functions  */

    public function loadQuantity($id){
        $inventory = Inventory::where('id', $id)->first();

        return response()->json($inventory->quantity);
    }


    public function addQuantity(Request $request, $id){
        $inventory = Inventory::where('id', $id)->first();
        $this->validate($request, [
            'quantity' => 'required|integer'
        ]);

         $inventory->update([
            'quantity' => $request->quantity
        ]);
        return ['new_quantity' => $inventory->quantity];
    }

    public function subtractQuantity(Request $request, $id){
        $inventory = Inventory::where('id', $id)->first();
        $this->validate($request, [
            'quantity' => 'required|integer'
        ]);

         $inventory->update([
            'quantity' => $request->quantity
        ]);
        return ['new_quantity' => $inventory->quantity];
    }

    public function purchase(Request $request)
    {
        //return json_encode($request['selectedItems']);
        //$a = array();
        $purchase_id = rand(1, 9999999);

        foreach ($request['selectedItems'] as $product) {
            $getProduct = json_decode($product);
            $the_product = Inventory::find($getProduct->id);
            
            Purchase::create([
                'purchase_id' => $purchase_id,
                'product' => $the_product->id,
            ]);
            //array_push($a, json_decode($product));
        } 

        return $purchase_id;
    }

    public function showpurchase($purchase_id )
    {
        return Purchase::where('purchases.purchase_id', $purchase_id)
            ->join('inventories', 'purchases.product', '=', 'inventories.id')
            ->select(
                'purchases.id as id',
                'purchases.purchase_id as purchase_id',
                'purchases.product as product_id',
                'inventories.product_name as product_name',
                'purchases.purchase_date as purchas_date',
                'purchases.quantity as quantity',
                'purchases.total_price as total_price',
                'purchases.supplier as supplier'
            )
            ->get();
    }
}
