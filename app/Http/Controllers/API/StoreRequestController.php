<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Store;
use App\Room;
use App\StoreRequest;
use App\RoomMovement;
use App\InventoryStore;
use Illuminate\Support\Facades\Hash;
use DB;


class StoreRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('api');
    }

    public function index()
    {
        $inventory = StoreRequest::where('store_request.deleted_at', NULL)
            ->join('inventories', 'store_request.product_id', '=', 'inventories.id')
            ->where('inventories.deleted_at', NULL)
            ->where('store_request.store_id', auth('api')->user()->store)
            ->groupBy('store_request.req_id')
            ->orderBy('store_request.created_at', 'desc')
            ->select(
                'store_request.req_id as req_id',
                'store_request.id as id',
                'inventories.product_name as product_name',
                'store_request.qty as qty',
                'store_request.sender_id as sender_id',
                'store_request.created_at as created_at',
                'store_request.approved_by as approved_by',
                'store_request.reciever_id as reciever_id',
                'store_request.status as status'
            )
            ->paginate(10);
        return response()->json($inventory);
    }

    public function makerequest(Request $request)
    {
        $random_number = rand(2,988888888);
   
        foreach ($request->payload as $trade) {
            $gettrade = json_decode($trade);
            $productRequest = new StoreRequest;
            $productRequest->req_id = $random_number;
            $productRequest->product_id = $gettrade->id;
            $productRequest->store_id = auth('api')->user()->store;
            $productRequest->qty = NULL;
            $productRequest->status = 'No Quantity';
            $productRequest->sender_id = auth('api')->user()->id;
            $productRequest->save();
        }
        return StoreRequest::where('req_id', $random_number)->first();        
    }


    public function moverequest(Request $request)
    {
        $random_number = rand(2,988888888);
   
        foreach ($request->payload as $trade) {
            $gettrade = json_decode($trade);
            $productRequest = new RoomMovement;
            $productRequest->ref_id = $random_number;
            $productRequest->product_id = $gettrade->id;
            $productRequest->store_id = auth('api')->user()->store;
            $productRequest->qty = NULL;
            $productRequest->status = NULL;
            $productRequest->user_id = auth('api')->user()->id;
            $productRequest->save();
        }
        return RoomMovement::where('ref_id', $random_number)->first();        
    }

    public function moveback(Request $request)
    {
        $random_number = rand(2,988888888);
       
        foreach ($request->payload as $trade) {
            $gettrade = json_decode($trade);
            $productRequest = new RoomMovement;
            $productRequest->ref_id = $random_number;
            $productRequest->product_id = $gettrade->id;
            $productRequest->store_id = auth('api')->user()->store;
            $productRequest->qty = NULL;
            $productRequest->move_type = 1;
            $productRequest->status = NULL;
            $productRequest->user_id = auth('api')->user()->id;
            $productRequest->save();
        }
        return RoomMovement::where('ref_id', $random_number)->first();        
    }
    public function getrequest(Request $request, $req_id)
    {
        return StoreRequest::where('req_id', $req_id)
            ->join('inventories', 'store_request.product_id', '=', 'inventories.id')
            ->where('inventories.deleted_at', NULL)
            ->select(
                'store_request.req_id as req_id',
                'store_request.id as id',
                'inventories.product_name as product_name',
                'store_request.qty as qty',
            )
        ->get();
    }

    public function getMove(Request $request, $ref_id)
    {
        return RoomMovement::where('ref_id', $ref_id)
            ->join('inventories', 'rooom_movement.product_id', '=', 'inventories.id')
            ->where('inventories.deleted_at', NULL)
            ->select(
                'rooom_movement.ref_id as ref_id',
                'rooom_movement.id as id',
                'rooom_movement.move_type as move_type',
                'inventories.product_name as product_name',
                'rooom_movement.qty as qty',
            )
        ->get();
    }

    public function viewrequest(Request $request, $req_id)
    {
        return StoreRequest::where('req_id', $req_id)
            ->join('inventories', 'store_request.product_id', '=', 'inventories.id')
            ->where('inventories.deleted_at', NULL)
            ->select(
                'store_request.req_id as req_id',
                'store_request.id as id',
                'store_request.product_id as product_id',
                'inventories.product_name as product_name',
                'store_request.qty as qty',
                'store_request.sender_id as sender_id',
                'store_request.created_at as created_at',
                'store_request.approved_by as approved_by',
                'store_request.reciever_id as reciever_id',
                'store_request.status as status'
            )
        ->get();
    }

    public function store(Request $request){
        foreach ($request->productQty as $product) {
            $id = $product['qtyId'];
            $prod = StoreRequest::Find($id);
            $prod->qty = $product['quantity'];
            $prod->status = 'pending';
            $prod->update(); 
        }
        return 'ok';
    }

    public function storemove(Request $request){
        foreach ($request->productQty as $product) {
            $id = $product['qtyId'];
            $req = RoomMovement::where('deleted_at')->find($id);
            if ($req->move_type==0) {

                $inventory = InventoryStore::where('deleted_at', NULL)->where('store_id', $req->store_id)->where('inventory_id', $req->product_id)->first();

                if ($inventory->number > $product['quantity']) {
                    $inventory->number = $inventory->number - $product['quantity'];
                    $inventory->update();

                    $room = Room::where('deleted_at', NULL)
                        ->where('store_id', $req->store_id)
                        ->where('inventory_id', $req->product_id)
                        ->first();
                    $room->number = $room->number + $product['quantity'];
                    $room->update();
                }
            }
            else
            {
                $room = Room::where('deleted_at', NULL)
                        ->where('store_id', $req->store_id)
                        ->where('inventory_id', $req->product_id)
                        ->first();


                if ($room->number > $product['quantity']) {
                    $room->number = $room->number - $product['quantity'];
                    $room->update();

                    $inventory = InventoryStore::where('deleted_at', NULL)->where('store_id', $req->store_id)->where('inventory_id', $req->product_id)->first();
                    $inventory->number = $inventory->number + $product['quantity'];
                    $inventory->update();
                }
            }

            //Updating Status
            $req->qty = $product['quantity'];
            $req->user_id = auth('api')->user()->id;
            $req->status = 'concluded';
            $req->update();           
        }
        return Store::where('deleted_at', NULL)->find(auth('api')->user()->store)->code;
    }

    public function destroy($id)
    {
        StoreRequest::Destroy($id);
        return 'ok';  
    }

    public function edit(Request $request, $id)
    {
        $store_request = StoreRequest::where('deleted_at')->find($id);
        $this->validate($request, [
            'qty' => 'required|string|max:255',
        ]);

        $store_request->update([
            'qty' => $request->qty,
            'status' => 'pending',
        ]);
        return ['message' => "Success"];
    }
}
