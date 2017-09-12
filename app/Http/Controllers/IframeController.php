<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
class IframeController extends Controller
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
    public function index()
    {
        $param=Input::get();
        foreach ($param as $key=>$v){
            $config=@json_decode($key,true);
        }
        $app=config('review.'.$config['appid']);
        $data=['data'=>array_merge($app,$config)];
        return view('iframe',$data);
    }
}
