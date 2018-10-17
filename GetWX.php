<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE");
 include "Cache.php";
 $APPID = "wxfc38b155c43b8d30";
 $SECRET = "c872f590f06d0e0724101085e21c7d6a";
 if($_GET['Type'] == "access_token"){
    echo getAccess_token();
 }
 else if($_GET['Type'] == "jsapi_ticket"){
    echo getJsapi_ticket();
 }
 else if($_GET['Type'] == "config"){
  // var_dump('assasa');die;
  $jsapi_ticket = getJsapi_ticket();
  $nonceStr = createNonceStr(); 
  $timestamp = time(); 
  $url = $_GET['url'];
  $signature = getSignature($jsapi_ticket,$nonceStr, $timestamp, $url);
  $access_token = getAccess_token();
  $APPID = "wxfc38b155c43b8d30";
  $result = array("jsapi_ticket"=>$jsapi_ticket, "nonceStr"=>$nonceStr,"timestamp"=>$timestamp,"url"=>$url,"signature"=>$signature,'access_token' =>$access_token,'APPID' => $APPID);
    echo json_encode($result);
  }else{

  }
  function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }
 function getSignature($jsapi_ticket,$noncestr, $timestamp, $url){
  $string1 = "jsapi_ticket=".$jsapi_ticket."&noncestr=".$noncestr."&timestamp=".$timestamp."&url=".$url;
  // var_dump($string1);
  $sha1 = sha1($string1);
  return $sha1;
 }
 
 function getJsapi_ticket(){
  $cache = new Cache();
  $cache = new Cache(7000, 'cache/'); 
  $jsapi_ticket = $cache -> get("jsapi_ticket");
  $access_token = getAccess_token();
  
  if ($jsapi_ticket == false) {
   $access_token = getAccess_token();
   $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket'; 
   $data = array('type'=>'jsapi','access_token'=>$access_token); 
   $header = array(); 
   $response = json_decode(curl_https($url, $data, $header, 5));
   // var_dump($response);die;
   $jsapi_ticket = $response->ticket;
  
   $cache -> put("jsapi_ticket", $jsapi_ticket);
  }
  return $jsapi_ticket;
 }
 
 function getAccess_token(){
  $cache = new Cache();
  $cache = new Cache(7000, 'cache/');
  
  $access_token = $cache -> get("access_token");
 
  
  if ($access_token == false) {
    global $APPID,$SECRET;
    $url = 'https://api.weixin.qq.com/cgi-bin/token'; 
    $data = array('grant_type'=>'client_credential','appid'=>$APPID,'secret'=>$SECRET); 
    $header = array();
    // var_dump($data);die;
    $response = json_decode(curl_https($url, $data, $header, 5));
    $access_token = $response->access_token;
    $cache -> put("access_token", $access_token);
  }
  return $access_token;
 }
 

 function curl_https($url, $data=array(), $header=array(), $timeout=30){ 
  $ch = curl_init(); 
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
  curl_setopt($ch, CURLOPT_URL, $url); 
  curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
  curl_setopt($ch, CURLOPT_POST, true); 
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
  curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
 
  $response = curl_exec($ch);
 
  if($error=curl_error($ch)){ 
  die($error); 
  }
 
  curl_close($ch);
 
  return $response;
 
 } 
?>