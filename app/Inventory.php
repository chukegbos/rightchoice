<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use App\User;
use App\Room;
use App\InventoryStore;

class Inventory extends Model
{
	use SoftDeletes;
	
    protected $fillable = [
    	'product_id', 'product_name', 'cost_price', 'price', 'quantity', 'category', 'unit', 'threshold'
    ];

    public function store()
    {
        return $this->belongsToMany('App\Store');
    }

    public function getQuantityAttribute()
    {
        $id = $this->attributes['id'];
        $store = InventoryStore::where('deleted_at', NULL)->where('inventory_id', $id)->sum('number');
        $room = Room::where('deleted_at', NULL)->where('inventory_id', $id)->sum('number');

        return $store + $room;
    }
}
