<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name URL.class.php
 * @date 2014-09-01 17:24:22
 */
 



require_once(CLASS_PATH."ErrorCase.class.php");


class URL{
    private $error;

    public function __construct(){
        $this->error = new ErrorCase();
    }

    
    public function combineURL($baseURL,$keysArr){
        $combined = $baseURL."?";
        $valueArr = array();

        foreach($keysArr as $key => $val){
            $valueArr[] = "$key=$val";
        }

        $keyStr = implode("&",$valueArr);
        $combined .= ($keyStr);

        return $combined;
    }

    
    public function get_contents($url){
        if (ini_get("allow_url_fopen") == "1") {
            $response = file_get_contents($url);
        }else{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            $response =  curl_exec($ch);
            curl_close($ch);
        }

                if(empty($response)){
            $this->error->showError("50001");
        }

        return $response;
    }

    
    public function get($url, $keysArr){
        $combined = $this->combineURL($url, $keysArr);
        return $this->get_contents($combined);
    }

    
    public function post($url, $keysArr, $flag = 0){

        $ch = curl_init();
        if(! $flag) curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $keysArr);
        curl_setopt($ch, CURLOPT_URL, $url);
        $ret = curl_exec($ch);

        curl_close($ch);
        return $ret;
    }
}
