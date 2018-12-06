<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
	use SoftDeletes;
	
    protected $table = "products";

    protected $fillable = ['user_id', 'category_id', 'title', 'condition', 'description', 'price', 'province', 'city', 'area', 'longitude', 'laptitude', 'featured', 'sold'];

    public function user() {
    	return $this->belongsTo('App\User');
    }

    public function images() {
    	return $this->hasMany('App\ProductImage');
    }
}
