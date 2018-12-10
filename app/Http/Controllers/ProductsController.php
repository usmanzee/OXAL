<?php

namespace App\Http\Controllers;

use File;
use Session;
use App\User;
use Validator;
use App\Helper;
use App\Product;
use App\Category;
use App\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ProductsController extends Controller
{
    public function index() {
        $products = Product::with('user')->with('category')->get();

        return view('products/index', compact('products'));
    }

    public function add() {

        $users = User::all();
        $categories = Category::all();
        return view('products/add', compact('users', 'categories'));
    }

    public function store(Request $request) {

        $this->validate($request, [
            'title' => 'required',
            'condition' => 'required',
            'description' => 'required',
            'user_id' => 'required',
            'category_id' => 'required',
            'price' => 'required',
            'province' => 'required',
            'city' => 'required',
            'area' => 'required',
            'longitude' => 'required',
            'laptitude' => 'required'
        ]);

        $input = $request->all();
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

        Session::put('success', 'Product created successfully.');
        return redirect('admin/products');
    }

    public function edit($id) {

        $users = User::all();
        $categories = Category::all();
        $product = Product::with('images')->where('id', $id)->first();
        return view('products/edit', compact('users', 'categories', 'product'));
    }

    public function update($id, Request $request) {

        // dd($request->all());

        $this->validate($request, [
            'title' => 'required',
            'condition' => 'required',
            'description' => 'required',
            'user_id' => 'required',
            'category_id' => 'required',
            'price' => 'required',
            'province' => 'required',
            'city' => 'required',
            'area' => 'required',
            'longitude' => 'required',
            'laptitude' => 'required'
        ]);

        $params = [
            'title' => $request->title,
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'condition' => $request->condition,
            'description' => $request->description,
            'price' => $request->price,
            'province' => $request->province,
            'city' => $request->city,
            'area' => $request->area,
            'longitude' => $request->longitude,
            'laptitude' => $request->laptitude,
        ];
        $params['featured'] = (isset($request->featured) && $request->featured) ? $request->featured : 0;

        Product::where('id', $id)->update($params);

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
                        'product_id' => $id,
                        'name' => $uploadName,
                        'name_without_ext' => $uploadNameWithoutExt,
                        'ext' => $extension
                    ];
                    ProductImage::create($productImageParams);
                }
            }
        }

        if(isset($request->images_to_delete)) {
            foreach ($request->images_to_delete as $key => $image_to_delete) {
                ProductImage::where('id', $image_to_delete)->delete();
            }
        }

        Session::put('success', 'Product updated successfully.');
        return redirect('admin/products');
    }

    public function delete($id, Request $request) {
        Product::where('id', $id)->delete();
        Session::put('success', 'Product deleted successfully.');
        return redirect('admin/products');
    }

    

    //APIS
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
        
        $path = Config::get('urls.site_url') . '/' . Config::get('urls.product_images_url');

        $query = Product::selectRaw($selectRaw)->with('user')
                        ->with(['images' => function($imagesQuery) use ($path) {
                                $imagesQuery->selectRaw('id, product_id, name, name_without_ext, ext, CASE WHEN name != "" AND name IS NOT NULL THEN CONCAT("'.$path.'", "/", name) ELSE NULL END AS imageUrl');
                        }]);
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
