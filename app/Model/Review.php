<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    const TYPE_REVIEW=0;
    const TYPE_QUESTION=1;

    const STATUS_SUCCESS=0;     //通过
    const STATUS_WAIT=1;        //待审核
    const STATUS_FAIL=2;        //垃圾评论
    const STATUS_NOSUCCESS=3;   //不通过

    const IS_ATTR_NULL=0;
    const IS_ATTR_HAVING=1;
    //允许批量赋值的字段
    protected $fillable=['page_id','appid','target_id','target_sku','type','score','is_attr','entity_id'];
    
    public function attr()
    {
        return $this->hasMany('App\ReviewAttr');
    }
    public function cont()
    {
        return $this->hasOne('App\ReviewContent');
    }
}
class ReviewAttr extends Model
{
    //
    public $timestamps = false;
    protected $table = 'reviews_attr';
    //允许批量赋值的字段
    protected $fillable=['review_id','attr_id'];
}
class ReviewContent extends Model
{
    //
    protected $table = 'reviews_content';
    
    //允许批量赋值的字段
    protected $fillable=['review_id','nickname','email','summary','review','page_url','user','ip','page_title'];
    protected $casts = [
        'user' => 'array',
        'review_images'=>'array',
    ];
    protected $primaryKey='review_id';

}
