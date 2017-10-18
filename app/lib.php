<?php
namespace App;
class lib{
    static function http_post($url, $param = "", $header = array(), $isGET = false) {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (is_string($param)) {
            $strPOST = $param;
        } elseif ($param) {
            $aPOST = array();
            foreach ($param as $key => $val) {
                $aPOST[] = $key . "=" . urlencode($val);
            }
            $strPOST = join("&", $aPOST);
        }

        if ($isGET) {
            curl_setopt($oCurl, CURLOPT_URL, $url . (strpos('?', $url) === false ? '?' : '&') . $strPOST);
        } else {
            curl_setopt($oCurl, CURLOPT_URL, $url);
            curl_setopt($oCurl, CURLOPT_POST, true);
            curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        }
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        if ($header)
            curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 20);
        curl_setopt($oCurl, CURLOPT_FOLLOWLOCATION, 1);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        return $sContent;
    }
    /**
     * @param $param
     *  -- api
     *  -- apiPublicKey
     *  -- apiSecretKey
     *  -- customer_id
     *  -- event share|review|review_with_photos
     *  -- platform facebook|other
     *  -- model review|product
     */
    static function points($param){
        $data = array(
            'timestamp'   => time(),
            'customer_id' => $param['customer_id'],
            'event'		  => $param['event'],
        );
        isset($param['model'])?$data['model']=$param['model']:"";
        isset($param['platform'])?$data['platform']=$param['platform']:"";

        $content = json_encode($data);
        $signature = $param['apiPublicKey'];//$publicKey;
        $hash = hash_hmac('md5', $content, $param['apiSecretKey']);

        $request = array(
            'content' => $content,
            'signature' => $signature,
            'hash' => $hash,
        );
        return self::http_post($param['api'],$request);
    }
}