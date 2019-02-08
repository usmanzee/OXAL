<?php

namespace App;

use Illuminate\Support\Facades\Config;
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

    public function category() {
    	return $this->belongsTo('App\Category');
    }
    
    public function images() {
    	return $this->hasMany('App\ProductImage');
    }

    public static function addEmptyImageInProducts($products) {
        $path = url(Config::get('urls.product_images_url'));

        foreach ($products as $key => $product) {
            $product->businessAd = false;
            if($product->images->isEmpty()) {
                $images['imageUrl'] = $path.'/'.'image-not-found.jpg';
                $product->images[] = $images;
            }
        }
    }

    public static function addBusinessAdToProducts($products, $businessAds, $defaultOffsetToInsert = 5) {
        $path = url(Config::get('urls.product_images_url'));
        $offsetToInsert = $defaultOffsetToInsert;
        if($businessAds->count()) {
            foreach ($businessAds as $key => $businessAd) {
                $businessAd->businessAd = true;
                if($businessAd->images->isEmpty()) {
                    $images['imageUrl'] = $path.'/'.'image-not-found.jpg';
                    $businessAd->images[] = $images;
                }
                $products->splice($offsetToInsert, 0, [$businessAd]);
                $offsetToInsert += $defaultOffsetToInsert;
                // if($offsetToInsert >= $products->count()) { 
                // } else { 
                //     break;
                // }
            }
        }
        
    }
}
