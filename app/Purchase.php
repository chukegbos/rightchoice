<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    /*public function user(){
    	return $this->belongsTo('App\User');
    }
    */
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_id', 'date_of_purchase', 'product', 'amount_paid', 'total_price', 'balance', 'supplier', 'quantity', 'purchase_date', 'outlet'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
      'remember_token', 'deleted_at', 'date_of_purchase'
    ];

    protected $dates = ['date_of_purchase'];
    public function getBalanceAttribute()
    {
        $balance = $this->attributes['total_price'] - $this->attributes['amount_paid'];
        return $balance;
    }
}
