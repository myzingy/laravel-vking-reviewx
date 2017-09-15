<?php

namespace App\Http\Controllers;

use App\Review;
use App\ReviewAttr;
use App\ReviewContent;
use Illuminate\Support\Facades\Input;

class ReviewController extends Controller
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
        $param=array_merge($app,$config);
        unset($param['appkey']);
        $data=['data'=>$param,'data_json'=>json_encode($param)];
        return view('iframe',$data);
    }
    public function getTotal(){
        return json_encode([1,2,3,4,5]);
    }
    public function show($fun){
        $json=$this->$fun();
        if($callback=Input::get('callback')){
            die("$callback($json);");
        }
        die($json);
    }
    public function submitReview(){
        $data=Input::get();
        $data['page_id']=md5($data['page_id']?$data['page_id']:$data['page_url']);
        $data['score']=$data['rate']+0;
        $data['is_attr']=Review::IS_ATTR_NULL;
        if(!empty($data['fileList']) && count($data['fileList'])>0 && $data['type']==Review::TYPE_REVIEW){
            $data['is_attr']=Review::IS_ATTR_HAVING;
        }
        $review = Review::create($data);
        if($review->id){
            $data['ip']=\Request::getClientIp();
            $data['user']=array(
                'user_id'=>$data['user_id'],
                'user_name'=>$data['user_id'],
            );
            $review->cont()->save(new ReviewContent($data));
            if($data['is_attr']==Review::IS_ATTR_HAVING){
                $attrs=[];
                foreach ($data['fileList'] as $attr_id){
                    array_push($attrs,new ReviewAttr(['attr_id'=>$attr_id]));
                }
                $review->attr()->saveMany($attrs);
            }
        }
        die(json_encode($review));
    }
    public function getReviews(){
        $data=Input::get();
        $page_id=md5($data['page_id']?$data['page_id']:$data['page_url']);
        $sortByDesc='created_at';
        $where=[
            ['appid','=',$data['appid']],
            ['page_id','=',$page_id],
            ['status','=',Review::STATUS_SUCCESS],
            ['type','=',$data['type']]
        ];
        $limit=$data['limit']?$data['limit']:10;
        $reviews = Review::with(['cont','attr'])
            ->where($where)
            ->when($order=$data['order'], function ($query) use ($order) {
                return $query->orderBy('is_attr','desc')->orderBy('created_at', 'desc');
            }, function ($query) {
                return $query->orderBy('created_at', 'desc');
            })
            ->offset($data['offset']+0)
            ->limit($limit)
            ->get();
        if($data['offset']<$limit){
            $total=Review::where($where)->count();
        }
        die(json_encode([
            'total'=>$total,
            'data'=>$reviews,
            'code'=>200,
        ]));
    }
}
