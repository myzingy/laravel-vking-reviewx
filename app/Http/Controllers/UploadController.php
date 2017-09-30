<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
use Matriphe\Imageupload\ImageuploadModel;

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
            $data= \Imageupload::output('db')->upload(Request::file('file'));
        }
        die($data);
    }
    public function image($path){
        list($attr_id,$token,$size)=explode('-',$path);

        $attr=ImageuploadModel::find($attr_id);
        header("content-type: ".$attr->original_mime);
        if('full.png'==$size){
            //header("content-length: ".$attr->original_filesize);
            readfile($attr->original_filepath);
            //echo file_get_contents($attr->original_filepath);
        }else{
            //header("content-length: ".$attr->size100_filesize);
            readfile($attr->size100_filepath);
            //echo file_get_contents($attr->size100_filepath);
        }
        exit;
    }
}
