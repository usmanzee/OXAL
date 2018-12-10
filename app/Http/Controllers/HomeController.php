<?php

namespace App\Http\Controllers;

use App\User;
use App\Product;
use App\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usersCount = User::all()->count();
        $adsCount = Product::all()->count();
        $categoriesCount = Category::all()->count();
        return view('home', compact('usersCount', 'adsCount', 'categoriesCount'));
    }

    public function login() {
        return view('auth/login');
    }
}
