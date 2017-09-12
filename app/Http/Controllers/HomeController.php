<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //var_dump(Auth::user());
        return view('home',['data'=>json_encode(Auth::user(),JSON_UNESCAPED_UNICODE)]);
    }
    public function my()
    {
        return view('home');
    }
}
