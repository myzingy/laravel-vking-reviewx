<?php
define('HOST','127.0.0.1');
define('PORT','3306');
define('USER','root');
define('PASS','');
define('DBNAME','amarley');
define('SQL','./var/cache/jqulia.review.sql');
##########################
$APPID='vkingxd635644d932612eb789d382c76';
$START_ID=20000;
$URI='https://www.vkingx.com/';
$IMGURI='https://res.vkingx.com/';
##########################
$offset=$_GET['offset']+0;
$limit=100;
/////////////////////////////////////////////////////

$db=mysqli_connect(HOST,USER,PASS,DBNAME,PORT);
if(!$db){
    echo "<div>Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    echo "</div>";
    exit;
}
mysqli_query($db,'SET NAMES utf8');
mysqli_query($db,"SET sql_mode='NO_UNSIGNED_SUBTRACTION'");
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
coalesce(RV.value,5) AS score,
IF(RD.`reviewimages`,1,0) AS is_attr,
R.`created_at`,
R.`created_at` AS 'update_at'  
FROM  `review` R
INNER JOIN `review_detail` RD ON R.`review_id`=RD.`review_id`
INNER JOIN `catalog_product_entity` CP ON CP.`entity_id`=R.`entity_pk_value`
LEFT JOIN `rating_option_vote` RV ON  R.`review_id`=RV.`review_id` 
ORDER BY R.`review_id` ASC
LIMIT $offset,$limit
END;
$query=mysqli_query($db,$sql);
$rows=mysqli_num_rows($query);
if($rows>0){
    $delete_ids=[];
    $insert_sql="INSERT INTO `reviews` (`id`,`page_id`,`appid`,`target_id`,`target_sku`,`entity_id`,`type`,`status`,`score`,`is_attr`,`created_at`,`updated_at`) VALUES \n"; ;
    while  ($r = mysqli_fetch_array($query, MYSQLI_ASSOC)){
        $sub_sql="('".implode("','",$r)."'),\n";
        $insert_sql.=$sub_sql;
        array_push($delete_ids,$r['id']);
    }
    mysqli_free_result($query);
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
RD.reviewimages as review_images,
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
    echo $sql;
    $query=mysqli_query($db,$sql);
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

    while  ($r = mysqli_fetch_array($query, MYSQLI_ASSOC)){
        array_walk($r,"sqlsafe");
        setReviewImages($r['review_images']);
        getInnerImages($r);

        $sub_sql="('".implode("','",$r)."'),\n";
        $insert_sql.=$sub_sql;
    }
    mysqli_free_result($query);
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
function getInnerImages(&$r){

    $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';

    preg_match_all($preg, $r['review'], $allImg);
    if(!empty($allImg[0])){
        $r['review'] = str_replace($allImg[0],'',$r['review']);
    }
    if(!empty($allImg[1])){
        $img = array();
        foreach ($allImg[1] as $key=>$val){
            $val = str_replace(array('http://','https://'),'',$val);
            $val = substr($val,0,2) == '//' ? 'https:'.$val : 'https://'.$val;

            $img[]=[
                'attr_id'=>$key,
                'thumb'=>$val,
                'src'=>$val
            ];
        }
        $r['review_images'] = json_encode($img);
    }
}