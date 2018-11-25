<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
	use SoftDeletes;
	
    protected $table = "products";

    protected $fillable = ['user_id', 'category_id', 'phone_number', 'title', 'condition', 'description', 'price', 'province', 'city', 'area', 'longitude', 'laptitude', 'active', 'sold', 'featured'];

    public function user() {
    	return $this->belongsTo('App\User');
    }
}
