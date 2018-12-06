<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductImage;
use App\Product;
use App\Helper;
use Validator;
use App\User;
use Config;
use File;

class ProductsController extends Controller
{
    public function postAd(Request $request) {

    	$validator = Validator::make($request->all(), [
	        'title' => 'required',
	        'user_id' => 'required',
	        'category_id' => 'required',
	        'condition' => 'required',
	        'description' => 'required',
	        'price' => 'required',
	        'province' => 'required',
			'city' => 'required',
			'area' => 'required',
			'longitude' => 'required',
			'laptitude' => 'required'
	    ]);

	    if($validator->fails()) {
	        return response()->json([
	            'status' => false,
	            'errors' => $validator->errors(),
	            'message' => "Please provide valid information."
	        ]);
	    }

	    $input = $request->all();
        $input['verification_code'] = Helper::createRandomNumber(4);
	    $product = Product::create($input);

	    $allowedfileExtension = ['png', 'jpg', 'jpeg', 'gif', 'tif', 'bmp', 'ico', 'psd', 'webp'];
	    if($request->hasFile('images')) {
	    	$images = $request->file('images');
	    	foreach ($images as $key => $image) {

	    		//$imageName = $image->getClientOriginalName();
	    		$extension = $image->getClientOriginalExtension();
	    		$uploadNameWithoutExt = date('Ymd-His').'-'.$key;
	    		$uploadName = date('Ymd-His').'-'.$key.'.'.$extension;

	    		if(in_array($extension, $allowedfileExtension)) {

	    			$path = public_path('product_images');
					if(!File::exists($path)) {
						File::makeDirectory($path, $mode = 0777, true, true);
					}
	    			$image->move($path, $uploadName);
	    			$productImageParams = [
	    				'product_id' => $product->id,
	    				'name' => $uploadName,
	    				'name_without_ext' => $uploadNameWithoutExt,
	    				'ext' => $extension
	    			];
	    			ProductImage::create($productImageParams);
	    		}
	    	}
	    }
	    $output = [
            'status' => true,
            'data' => $product,
            'message' => 'Your ad posted successfully.'
        ];

        return response()->json($output);
    }

    public function searchProducts(Request $request) {
        $searchedWord = $request->searchedWord;
        $page = (isset($request->page) && $request->page) ? $request->page : 1;
        $limit = 15;
        $skip = ($page-1) * $limit;

        $selectRaw = "*";
        $selectRaw .= (!empty($laptitude) && !empty($longitude)) ? ', round(111.1111 * DEGREES(ACOS(COS(RADIANS(laptitude)) * COS(RADIANS('.$laptitude.')) * COS(RADIANS(longitude - '.$longitude.')) + SIN(RADIANS(laptitude)) * SIN(RADIANS('.$laptitude.')))), 1) AS distance_in_km' : '';

        $query = Product::selectRaw($selectRaw)->with('user');
        $query->where('title', 'LIKE', '%' .$searchedWord. '%')->orwhere('description', 'LIKE', '%' .$searchedWord. '%')->where('sold', 0);
        if(!empty($laptitude) && !empty($longitude)) {
            $query->orderBy('distance_in_km', 'ASC');
        }
        $products = $query->skip($skip)->take($limit)->get();
        if($products->count()) {
            $output = [
                'status' => true,
                'data' => $products
            ];
        } else {
            $output = [
                'status' => false,
                'message' => 'No ad found.'
            ];
        }

        return response()->json($output);
    }

    public function getUserProducts(Request $request) {
        $userId = $request->userId;
        $page = (isset($request->page) && $request->page) ? $request->page : 1;
        $limit = 15;
        $skip = ($page-1) * $limit;

        $path = Config::get('urls.site_url') . '/' . Config::get('urls.product_images_url');
        $userProducts = Product::where('user_id', $userId)
                        ->with(['images' => function($imagesQuery) use ($path) {
                                $imagesQuery->selectRaw('id, product_id, name, name_without_ext, ext, CASE WHEN name != "" AND name IS NOT NULL THEN CONCAT("'.$path.'", "/", name) ELSE NULL END AS imageUrl');
                        }])
                        ->get();

        if($userProducts->count()) {
            $output = [
                'status' => true,
                'data' => $userProducts
            ];
        } else {
            $output = [
                'status' => false,
                'message' => 'No feature ad found.'
            ];
        }

        return response()->json($output);

    }

    public function getFeaturedProducts(Request $request) {

    	$laptitude = (isset($request->laptitude)) ? $request->laptitude : '';
    	$longitude = (isset($request->longitude)) ? $request->longitude : '';
    	$page = (isset($request->page) && $request->page) ? $request->page : 1;
        $limit = 15;
        $skip = ($page-1) * $limit;

    	$selectRaw = "*";
    	$selectRaw .= (!empty($laptitude) && !empty($longitude)) ? ', round(111.1111 * DEGREES(ACOS(COS(RADIANS(laptitude)) * COS(RADIANS('.$laptitude.')) * COS(RADIANS(longitude - '.$longitude.')) + SIN(RADIANS(laptitude)) * SIN(RADIANS('.$laptitude.')))), 1) AS distance_in_km' : '';
        $path = Config::get('urls.site_url') . '/' . Config::get('urls.product_images_url');

        $query = Product::selectRaw($selectRaw)->with('user')
                        ->with(['images' => function($imagesQuery) use ($path) {
                                $imagesQuery->selectRaw('id, product_id, name, name_without_ext, ext, CASE WHEN name != "" AND name IS NOT NULL THEN CONCAT("'.$path.'", "/", name) ELSE NULL END AS imageUrl');
                        }]);
    	$query->where('featured', 1)->where('sold', 0);
    	if(!empty($laptitude) && !empty($longitude)) {
    		$query->orderBy('distance_in_km', 'ASC');
    	}
    	$products = $query->skip($skip)->take($limit)->get();
        if($products->count()) {
        	$output = [
        		'status' => true,
        		'data' => $products
        	];
        } else {
        	$output = [
        		'status' => false,
        		'message' => 'No feature ad found.'
        	];
        }

        return response()->json($output);
    }


    public function getProductsByCategory(Request $request) {

    	$categoryId = $request->categoryId;
    	$laptitude = (isset($request->laptitude)) ? $request->laptitude : '';
    	$longitude = (isset($request->longitude)) ? $request->longitude : '';
    	$page = (isset($request->page) && $request->page) ? $request->page : 1;
        $limit = 15;
        $skip = ($page-1) * $limit;

    	$selectRaw = "*";
    	$selectRaw .= (!empty($laptitude) && !empty($longitude)) ? ', round(111.1111 * DEGREES(ACOS(COS(RADIANS(laptitude)) * COS(RADIANS('.$laptitude.')) * COS(RADIANS(longitude - '.$longitude.')) + SIN(RADIANS(laptitude)) * SIN(RADIANS('.$laptitude.')))), 1) AS distance_in_km' : '';

        $path = Config::get('urls.site_url') . '/' . Config::get('urls.product_images_url');

        $query = Product::selectRaw($selectRaw)->with('user')
                        ->with(['images' => function($imagesQuery) use ($path) {
                                $imagesQuery->selectRaw('id, product_id, name, name_without_ext, ext, CASE WHEN name != "" AND name IS NOT NULL THEN CONCAT("'.$path.'", "/", name) ELSE NULL END AS imageUrl');
                        }]);
    	$query->where('category_id', $categoryId)->where('sold', 0);
    	if(!empty($laptitude) && !empty($longitude)) {
    		$query->orderBy('distance_in_km', 'ASC');
    	}
    	$products = $query->skip($skip)->take($limit)->get();
        if($products->count()) {
        	$output = [
        		'status' => true,
        		'data' => $products
        	];
        } else {
        	$output = [
        		'status' => false,
        		'message' => 'No feature ad found.'
        	];
        }

        return response()->json($output);
    }

    public function getProductDetail(Request $request) {

    	$productId = $request->productId;
    	$laptitude = (isset($request->laptitude)) ? $request->laptitude : '';
    	$longitude = (isset($request->longitude)) ? $request->longitude : '';

    	$selectRaw = "*";
    	$selectRaw .= (!empty($laptitude) && !empty($longitude)) ? ', round(111.1111 * DEGREES(ACOS(COS(RADIANS(laptitude)) * COS(RADIANS('.$laptitude.')) * COS(RADIANS(longitude - '.$longitude.')) + SIN(RADIANS(laptitude)) * SIN(RADIANS('.$laptitude.')))), 1) AS distance_in_km' : '';
        
        $path = Config::get('urls.site_url') . '/' . Config::get('urls.product_images_url');

        $query = Product::selectRaw($selectRaw)->with('user')
                        ->with(['images' => function($imagesQuery) use ($path) {
                                $imagesQuery->selectRaw('id, product_id, name, name_without_ext, ext, CASE WHEN name != "" AND name IS NOT NULL THEN CONCAT("'.$path.'", "/", name) ELSE NULL END AS imageUrl');
                        }]);
    	$query->where('id', $productId);
    	if(!empty($laptitude) && !empty($longitude)) {
    		$query->orderBy('distance_in_km', 'ASC');
    	}
    	$product = $query->first();
        if(!is_null($product)) {
        	$output = [
        		'status' => true,
        		'data' => $product
        	];
        } else {
        	$output = [
        		'status' => false,
        		'message' => 'Product not found.'
        	];
        }

        return response()->json($output);

    }
}
