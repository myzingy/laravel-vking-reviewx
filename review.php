<?php
define('HOST','localhost');
define('PORT','3306');
define('USER','root');
define('PASS','');
define('DBNAME','amarley');
define('SQL','./var/cache/review.sql');
##########################
$APPID='49626cb84d647d4075d5ebe8da4b4b01';
$START_ID=1000;
$URI='https://sandbox.amarley.com/';
$IMGURI='https://sandbox.amarley.com/';
##########################
$offset=$_GET['offset']+0;
$limit=100;
/////////////////////////////////////////////////////

$db=mysql_connect(HOST.':'.PORT,USER,PASS);
if(!$db){
    die('db fi!');
}
mysql_select_db(DBNAME,$db);
mysql_query('SET NAMES utf8',$db);
mysql_query("SET sql_mode='NO_UNSIGNED_SUBTRACTION'",$db);
#reviews
$sql=<<<END
SELECT R.review_id+$START_ID AS id,
MD5(CONCAT('$APPID',R.entity_pk_value)) AS page_id,
'$APPID' AS appid,
R.entity_pk_value AS target_id,
CP.`sku` AS target_sku, 
RD.`customer_id` AS entity_id,
0 AS `type`,
IF(R.status_id=1,0,1) AS `status`,
RV.value AS score,
IF(RD.`reviewimage`,1,0) AS is_attr,
R.`created_at`,
R.`created_at` AS 'update_at'  
FROM  `review` R,`review_detail` RD,`rating_option_vote` RV,`catalog_product_entity` CP
WHERE R.`review_id`=RD.`review_id` AND R.`review_id`=RV.`review_id` AND CP.`entity_id`=R.`entity_pk_value`
ORDER BY R.`review_id` ASC
LIMIT $offset,$limit
END;
$query=mysql_query($sql,$db);
$rows=mysql_num_rows($query);
if($rows>0){
    $delete_ids=[];
    $insert_sql="INSERT INTO `reviews` (`id`,`page_id`,`appid`,`target_id`,`target_sku`,`entity_id`,`type`,`status`,`score`,`is_attr`,`created_at`,`updated_at`) VALUES \n"; ;
    while  ($r = mysql_fetch_array($query, MYSQL_ASSOC)){
        $sub_sql="('".implode("','",$r)."'),\n";
        $insert_sql.=$sub_sql;
        array_push($delete_ids,$r['id']);
    }
    $delete_sql= "DELETE FROM `reviews` WHERE `id` IN (".implode(',',$delete_ids).");\n"
    ."DELETE FROM `reviews_content` WHERE `review_id` IN (".implode(',',$delete_ids).");\n"
    ."DELETE FROM `reviews_attr` WHERE `review_id` IN (".implode(',',$delete_ids).");\n";
    $insert_sql=substr($insert_sql,0,-2).";\n";
    ////////////
    //reviews_cont
    $insert_cont_sql=getReviewContSql($delete_ids);
    //reviews_cont
    ////////////
    $all_sql=$delete_sql.$insert_sql.$insert_cont_sql;
    echo "<pre>".$all_sql;
    if($offset<$limit){
        $fb=fopen(SQL,'w+');
    }else{
        $fb=fopen(SQL,'a+');
    }
    fwrite($fb,$all_sql);
    fclose($fb);
}
if($rows>=$limit){
    echo '<meta http-equiv="refresh" content="1; url='.$_SERVER['PHP_SELF'].'?offset='.($offset+$limit).'" />';
    exit;
}
die('over!!!');

function getReviewContSql($ids){
    global $URI,$db,$START_ID;
    $ids=implode(',',$ids);
    $sql=<<<END
SELECT R.review_id+$START_ID AS review_id,
RD.nickname AS nickname,
RD.`title` AS summary, 
RD.`detail` AS review,
RD.reviewimage as review_images,
CONCAT('$URI',CPV1.value) AS `page_url`,
CPV2.`value` AS 'page_title',  
'127.0.0.1' AS ip,   
R.`created_at`,
R.`created_at` AS 'update_at'
FROM  `review` R
LEFT JOIN `review_detail` RD ON R.`review_id`=RD.`review_id`
LEFT JOIN `catalog_product_entity_varchar` CPV1 ON CPV1.`entity_id`=R.`entity_pk_value` AND CPV1.attribute_id=98
LEFT JOIN `catalog_product_entity_varchar` CPV2 ON CPV2.`entity_id`=R.`entity_pk_value` AND CPV2.attribute_id=71
WHERE R.review_id+$START_ID IN ($ids)
GROUP BY R.review_id;
END;
    $query=mysql_query($sql,$db);
    //$rows=mysql_num_rows($query);
    $insert_sql=<<<END
INSERT INTO `reviews_content` (
  `review_id`,
  `nickname`,
  `summary`,
  `review`,
  `review_images`,
  `page_url`,
  `page_title`,
  `ip`,
  `created_at`,
  `updated_at`
) 
VALUES
END;

    while  ($r = mysql_fetch_array($query, MYSQL_ASSOC)){
        array_walk($r,"sqlsafe");
        setReviewImages($r['review_images']);
        $sub_sql="('".implode("','",$r)."'),\n";
        $insert_sql.=$sub_sql;
    }
    $insert_sql=substr($insert_sql,0,-2).";\n";
    return $insert_sql;

}
function sqlsafe(&$v){
    $v=strtr($v,array("'"=>"\'","\\"=>"\\\\"));
}
function getReviewImagesUrl($path){
    global $IMGURI;
    if(strpos($path,'http')>-1) return $path;
    return $IMGURI.'media/creviewimages/'.$path;
}
function setReviewImages(&$review_images){
    if(!$review_images) return $review_images='[]';
    $review_images_flag=unserialize($review_images);
    if(is_array($review_images_flag)){
        $img=[];
        foreach ($review_images_flag as $i=>$r){
            $img[$i]=[
                'attr_id'=>$i,
                'thumb'=>getReviewImagesUrl($r['fileurl']),
                'src'=>getReviewImagesUrl($r['fileurl'])
            ];
        }
        $review_images=$img;
    }else{
        $review_images=[
            [
                'attr_id'=>0,
                'thumb'=>getReviewImagesUrl($review_images),
                'src'=>getReviewImagesUrl($review_images)
            ]
        ];
    }
    $review_images=json_encode($review_images);
}