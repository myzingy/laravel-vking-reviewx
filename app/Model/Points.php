<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Points extends Model
{
    const TYPE_REVIEW=0;
    const TYPE_SHARE_CALLBACK=1;

    public $timestamps = false;
    protected $table = 'points';
    //允许批量赋值的字段
    protected $fillable=['page_id','customer_id','type'];

    static function isEmpty($data){
        $count=self::where($data)->count();
        if($count>0) return false;
        self::create($data);
        return true;
    }

}
