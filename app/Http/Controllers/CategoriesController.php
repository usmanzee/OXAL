<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;

class CategoriesController extends Controller
{
    public function getAllCategories() {

    	$categories = Category::all();
    	if($categories->count()) {
    		$output = [
    			'status' => true,
    			'data' => $categories
    		];
    	} else {
    		$output = [
    			'status' => false,
    			'message' => 'No record found.'
    		];
    	}

    	return response()->json($output);
    }
}
