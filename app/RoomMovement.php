<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use App\User;

class RoomMovement extends Model
{
    use SoftDeletes;
    protected $table = 'rooom_movement';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_id', 'product_id', 'qty', 'user_id', 'ref_id', 'status', 'move_type'
    ];

    protected $dates = [
        'deleted_at', 
    ];
}
