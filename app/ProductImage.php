<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductImage extends Model
{
	use SoftDeletes;
	
    protected $table = "product_images";

    protected $fillable = ['product_id', 'name', 'name_without_ext', 'ext'];

    public function product() {
    	return $this->belongsTo('App\Product');
    }
}
