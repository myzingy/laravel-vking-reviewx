<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Request;
use Matriphe\Imageupload\Imageupload;

class UploadController extends Controller
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
        $data='{}';
        if (Request::hasFile('file')) {
            $Imageupload=new Imageupload();
            $data= $Imageupload->upload(Request::file('file'));
        }
        die($data);
    }
}
