<?php

namespace App\Http\Controllers;

use App\Review;
use App\ReviewAttr;
use App\ReviewContent;
use Illuminate\Support\Facades\Input;
use App\lib;
use App\Model\Points;
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
    public function index()
    {
        $appid=Input::get('appid');
        $page_url=Input::get('page_url');
        $flag=preg_match("/shxxare=([a-z\-_\d]+(=?)(=?))/i",$page_url,$match);
        if(empty($match[1])) die('{code:501,message:"param error"}');
        $match[1]=strtr($match[1],array('-'=>'+','_'=>'/'));
        list($target_id,$user_id,$user_id_mask,$platform)=explode(",",base64_decode($match[1]));
        if(empty($target_id) || empty($user_id)  || empty($user_id_mask) || empty($platform)){
            die('{code:502,message:"param parse error"}');
        }
        $data=Input::get();
        $app=$this->__getApp($data['appid']);
        if(!$app['api']) die('{code:503,message:"api error"}');
        $entity_id=$this->__getEntityId($data,$app);
        if(!$entity_id) die('{code:503,message:"entity_id error"}');
        $page_id=$this->__getPageId($data);
        $isEmpty=Points::isEmpty(array(
            'page_id'=>$page_id,
            'customer_id'=>$user_id,
            'platform'=>$platform
        ));
        if(!$isEmpty) die('{code:504,message:"isEmpty error"}');
        $res=lib::points(array(
            'api'=>$app['api'],
            'apiPublicKey'=>$app['apiPublicKey'],
            'apiSecretKey'=>$app['apiSecretKey'],
            'customer_id'=>'37053',
            'event'=>'share',
            'platform'=>$platform,
            'model'=>'review'
        ));
        die($res);
    }
    function __getApp($appid){
        if(!$appid) die('appid empty.');
        $app=config('review.'.$appid);
        if(!$app) die('appid error.');
        return $app;
    }
    function __getEntityId($data,$app){
        if($data['user_id'] && $data['user_id_mask']){
            if(sha1($app['appkey'].$data['user_id'])==$data['user_id_mask']){
                return $data['user_id'];
            }
        }
        return "";
    }
    function __getPageId($data,$appid=""){
        if(!empty($data['page_id'])) return md5($data['page_id']);
        $appid=$appid?$appid:$data['appid'];
        if($data['target_id']) return md5($appid.$data['target_id']);
        if($data['target_sku']) return md5($appid.$data['target_sku']);
        $page=$data['page_url'];
        return md5($page);
    }

}
