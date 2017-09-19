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
        $data=Input::get();
        $page_ids=[];
        if($data['target_ids']){
            if(is_string($data['target_ids'])){
                $data['target_ids']=explode(',',$data['target_ids']);
            }
            foreach ($data['target_ids'] as $target_id){
                array_push($page_ids,$this->__getPageId(['target_id'=>$target_id,'appid'=>$data['appid']]));
            }
        }else{
            $page_ids=[$this->__getPageId($data)];
        }
        if(count($page_ids)<1) return ['code'=>200,'data'=>[]];
        $where=[
            ['appid','=',$data['appid']],
            ['status','=',Review::STATUS_SUCCESS],
            ['type','=',Review::TYPE_REVIEW]
        ];
        $query=Review::select('page_id',\DB::raw('max(`target_id`) as `target_id`,max(`target_sku`) as target_sku,count(*) as `count`, avg(`score`) as `score`'));
        $data=$query->where($where)
            ->whereIn('page_id', $page_ids)
            ->groupBy('page_id')
            ->get();
        $json=['code'=>200,'data'=>$data];
        if($callback=Input::get('callback')){
            die("$callback(".json_encode($json).");");
        }
        return $json;
    }
    public function show($fun){
        $json=$this->$fun();
        if(!is_string($json)){
            $json=json_encode($json);
        }
        if($callback=Input::get('callback')){

            die("$callback($json);");
        }
        die($json);
    }
    public function submitReview(){
        $data=Input::get();
        $data['page_id']=$this->__getPageId($data);
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
        die(json_encode(['code'=>200]));
    }
    public function getReviews(){
        $data=Input::get();
        $page_id=$this->__getPageId($data);
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
        $total=0;
        if($data['offset']<$limit){
            $total=Review::where($where)->count();
        }
        die(json_encode([
            'total'=>$total,
            'data'=>$reviews,
            'code'=>200,
        ]));
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
