<?php

namespace App\Http\Controllers;

use App\Review;
use App\ReviewAttr;
use App\ReviewContent;
use Aws\DynamoDb\DynamoDbClient;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Cache;

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
    public function index($config)
    {
        $config=strtr($config,array('-'=>'+','_'=>'/'));
        $config_jsonstr=@base64_decode($config);
        $config=@json_decode($config_jsonstr,true);
        if(!$config){
            $config=$this->formatJson($config_jsonstr);
        }
        $app=$this->__getApp($config['appid']);//config('review.'.$config['appid']);
        $param=array_merge($app,$config);
        unset($param['appkey'],$param['api'],$param['apiPublicKey'],$param['apiSecretKey']);
        $data_json=json_encode($param);
        $data_json=strtr($data_json,array("'"=>"&#x27;"/*,"\\"=>"",'"'=>"&#x22;"*/));
        $data=['data'=>$param,'data_json'=>$data_json];
        return view('iframe',$data);
    }
    private function formatJson($config_jsonstr){
        $config=[
            'dom_id'=>'',
            'appid'=>'',
            'page_id'=>'',
            'page_url'=>'',
            'page_title'=>'',
            'user_id'=>'',
            'user_id_mask'=>'',
            'user_name'=>'',
            'user_email'=>'',
            'target_id'=>'',
            'target_sku'=>'',
            'target_ids'=>'',
            'view'=>'',
        ];
        foreach ($config as $key=>$val){
            $flag=preg_match("/\"$key\":\"([^\"]+)\"/",$config_jsonstr,$match);
            if($flag){
                $config[$key]=preg_replace("/[^\da-z #:\/\.\-_]/i","",$match[1]);
            }
        }
        return $config;
    }
    public function getTotal(){
        $data=Input::get();
        $page_ids=[];
        if(!empty($data['target_ids'])){
            if(is_string($data['target_ids'])){
                $data['target_ids']=explode(',',$data['target_ids']);
            }
            if($data['target_ids']){
                foreach ($data['target_ids'] as $target_id){
                    array_push($page_ids,$this->__getPageId(['target_id'=>$target_id,'appid'=>$data['appid']]));
                }
            }
        }else{
            $page_ids=[$this->__getPageId($data)];
        }
        if(count($page_ids)<1) return ['code'=>200,'data'=>[]];
        if(count($page_ids)==1){
            if(!$qdata=Cache::get($page_ids[0])) {
                $where = [
                    ['appid', '=', $data['appid']],
                    ['status', '=', Review::STATUS_SUCCESS],
                    ['type', '=', Review::TYPE_REVIEW]
                ];
                $query = Review::select('page_id', \DB::raw('max(`target_id`) as `target_id`,max(`target_sku`) as target_sku,count(*) as `count`, avg(`score`) as `score`'));
                $qdata = $query->where($where)
                    ->whereIn('page_id', $page_ids)
                    ->groupBy('page_id')
                    ->get();
                if (!empty($qdata[0])) {
                    $qdata[0]['qcount'] = Review::where(array(
                        ['appid', '=', $data['appid']],
                        ['status', '=', Review::STATUS_SUCCESS],
                        ['type', '=', Review::TYPE_QUESTION]
                    ))->whereIn('page_id', $page_ids)->count();
                    Cache::forever($page_ids[0], $qdata);
                    if(!empty($data['target_id'])){
                        $this->awsCache($data['target_id'],$qdata[0]['score'],$qdata[0]['count'],$data['appid']);
                    }
                }else{
                    $qdata=[
                        'count'=>0,
                        'page_id'=>$page_ids[0],
                        'qcount'=>0,
                        'score'=>0,
                        'target_id'=>empty($data['target_id'])?'':$data['target_id'],
                        'target_sku'=>empty($data['target_sku'])?'':$data['target_sku'],
                    ];
                    Cache::forever($page_ids[0],$qdata);
                    if(!empty($data['target_id'])){
                        $this->awsCache($data['target_id'],$qdata[0]['score'],$qdata[0]['count'],$data['appid']);
                    }
                }
            }
            $json=['code'=>200,'data'=>$qdata,'cache'=>\Cache::has($page_ids[0])];
        }else{
            $where=[
                ['appid','=',$data['appid']],
                ['status','=',Review::STATUS_SUCCESS],
                ['type','=',Review::TYPE_REVIEW]
            ];
            $query=Review::select('page_id',\DB::raw('max(`target_id`) as `target_id`,max(`target_sku`) as target_sku,count(*) as `count`, avg(`score`) as `score`'));
            $qdata=$query->where($where)
                ->whereIn('page_id', $page_ids)
                ->groupBy('page_id')
                ->get();
            $json=['code'=>200,'data'=>$qdata];
        }
        
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
        foreach($data as &$x){
            if(is_string($x)) $x=trim($x);
        }
        $data['page_id']=$this->__getPageId($data);
        $data['score']=empty($data['rate'])?0:($data['rate']+0);
        $data['is_attr']=Review::IS_ATTR_NULL;
        if(!empty($data['fileList']) && count($data['fileList'])>0 && $data['type']==Review::TYPE_REVIEW){
            $data['is_attr']=Review::IS_ATTR_HAVING;
        }
        $data['entity_id']=$this->__getEntityId($data);
        $review = Review::create($data);
        if($review->id){
            $data['ip']=\Request::getClientIp();
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
        $cache_key="";
        if($data['offset']==0 && $data['order']=='is_attr' && $data['type']==Review::TYPE_REVIEW){
            $cache_key=$page_id.'.first';
            $res=Cache::get($cache_key);
            if($res){
                $res['cache']=true;
                die(json_encode($res));
            }
        }
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
        foreach ($reviews as &$x){
            $x['score']+=0;
        }
        $res=array(
            'total'=>0,
            'data'=>$reviews,
            'code'=>200
        );
        if($data['offset']<$limit){
            $res['total']=Review::where($where)->count();
        }
        if($cache_key){
            Cache::forever($cache_key,$res);
        }
        die(json_encode($res));
    }
    public function getMyReviews(){
        $data=Input::get();
        $entity_id=$this->__getEntityId($data);
        if(!$entity_id) die('{code:200,data:[],total:0}');
        $sortByDesc='created_at';
        $where=[
            ['appid','=',$data['appid']],
            ['entity_id','=',$entity_id],
            //['type','=',$data['type']]
        ];
        $limit=$data['limit']?$data['limit']:10;
        $reviews = Review::with(['cont'])
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
        if(!empty($data['target_id'])) return md5($appid.$data['target_id']);
        if(!empty($data['target_sku'])) return md5($appid.$data['target_sku']);
        $page=$data['page_url'];
        return md5($page);
    }
    function __getEntityId($data){
        $app=$this->__getApp($data['appid']);
        if($data['user_id'] && $data['user_id_mask']){
            if(sha1($app['appkey'].$data['user_id'])==$data['user_id_mask']){
                return $data['user_id'];
            }
        }
        return "";
    }
    function __getApp($appid){
        if(!$appid) die('appid empty.');
        $app=config('review.'.$appid);
        if(!$app) die('appid error.');
        return $app;
    }
    function code(){
        header("Location: ".mix('js/iframe.js'));
        exit;
    }
    function awsCache($id,$rating,$total,$appid){
        if(!$id) return;
        $app=$this->__getApp($appid);
        if(empty($app['awsTableName'])) return;
        $client = new DynamoDbClient([
            'region'  => 'us-west-2',
            'version' => 'latest',
            'http'    => [
                'verify' => false
            ],
            'credentials' => [
                'key'    => 'AKIAIBSY2WW7ZAEZWM7Q',
                'secret' => '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'
            ]
        ]);

        $command = [
            'TableName'=>$app['awsTableName'],
            'Item'=>[
                'id'=>['S'=>$id],
                'rating'=>['N'=>($rating*20).""],
                'num'=>['N'=>$total.""]
            ]
        ];
        $client->putItem($command);
    }
}
