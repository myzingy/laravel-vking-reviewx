<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Points extends Model
{
    const PLARFORM_REVIEW='REVIEW';
    
    public $timestamps = false;
    protected $table = 'points';
    //允许批量赋值的字段
    protected $fillable=['page_id','customer_id','platform'];

    static function isEmpty($data){
        $count=self::where($data)->count();
        if($count>0) return false;
        self::create($data);
        return true;
    }

}
