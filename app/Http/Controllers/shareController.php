<?php

namespace App\Http\Controllers;

use App\Review;
use App\ReviewAttr;
use App\ReviewContent;
use Illuminate\Support\Facades\Input;

class ShareController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($path)
    {
        $path=base64_decode($path);
        header("Location:".$path);
        exit;
    }

}
