<?php

/**
 * @param $http2ch          the curl connection
 * @param $http2_server     the Apple server url
 * @param $apple_cert       the path to the certificate
 * @param $app_bundle_id    the app bundle id
 * @param $message          the payload to send (JSON)
 * @param $token            the token of the device
 * @return mixed            the status code
 */


  $keyfile = 'AuthKey_GG9P5QL4L3.p8';               # <- Your AuthKey file
  $keyid = 'GG9P5QL4L3';                            # <- Your Key ID
  $teamid = 'Q8YCT49UJB';                           # <- Your Team ID (see Developer Portal)
  $bundleid = 'com.bizzy.deals';                # <- Your Bundle ID
  $url = 'https://api.push.apple.com';  # <- development url, or use http://api.development.push.apple.com for development environment
  //$token = 'ec8d2ca5caf50cada615ee1ca592244d2da80ac6135928fb8f6bb8783279e02d';              # <- Device Token

  $message = '{"aps":{"alert":"Hi there! Sijan.","sound":"default", "badge":1}}';

  $key = openssl_pkey_get_private('file://'.$keyfile);

  $header = ['alg'=>'ES256','kid'=>$keyid];
  $claims = ['iss'=>$teamid,'iat'=>time()];

  $header_encoded = base64($header);
  $claims_encoded = base64($claims);

  $signature = '';
  openssl_sign($header_encoded . '.' . $claims_encoded, $signature, $key, 'sha256');
  $jwt = $header_encoded . '.' . $claims_encoded . '.' . base64_encode($signature);

  // only needed for PHP prior to 5.5.24
  if (!defined('CURL_HTTP_VERSION_2_0')) {
      define('CURL_HTTP_VERSION_2_0', 3);
  }

function sendHTTP2Push($http2ch, $url, $http2_server, $jwt, $bundleid, $message, $token)
{

  //$token = 'd2092ad4e4da5256c3db5e0021fb797f9657d7b32aa471baef1d313ae55aefa2';
  $token = $_GET["token"];
  
  curl_setopt_array($http2ch, array(
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
    CURLOPT_URL => "$url/3/device/$token",
    CURLOPT_PORT => 443,
    CURLOPT_HTTPHEADER => array(
      "apns-topic: {$bundleid}",
      "authorization: bearer $jwt"
    ),
    CURLOPT_POST => TRUE,
    CURLOPT_POSTFIELDS => $message,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HEADER => 1
  ));

  $result = curl_exec($http2ch);
  
  if ($result === FALSE) {
    throw new Exception("Curl failed: ".curl_error($http2ch));
  }

  $status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
  return $status;
  
  }
  
  
  $http2ch = curl_init();
  $token = $_GET['devicetoken'];
  $response = json_decode(json_encode($responseJSON));

$status = sendHTTP2Push($http2ch, $url, $http2_server, $jwt, $bundleid, $message, $token);

if($status == 200) {
      echo '{"status": '.$status.',"message":"Push notification send successfully."}';
    } else {
      echo '{"status": '.$status.',"message":"Push notification not send."}';
    }
    
    


// close connection
curl_close($http2ch);


  function base64($data) {
    return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
  }
  
  ?>
