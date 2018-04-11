<?php
header('content-type: image/png');

$url = $_SERVER['REQUEST_URI'];


$img_url = preg_replace('/qpic.php\?/','',$url);
$img_url = preg_replace('/qpic\//','',$url);
$img_url = 'http://mmbiz.qpic.cn'.$img_url;


$defaultUserAgent = 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/535.19 (KHTML, like Gecko) Ubuntu/10.10 Chromium/18.0.1025.151 Chrome/18.0.1025.151 Safari/535.19';
$_header = array(
    "User-Agent: {$defaultUserAgent}"
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $img_url);
curl_setopt($ch, CURLOPT_HTTPGET, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30 );
curl_setopt($ch, CURLOPT_HTTPHEADER, $_header);
curl_setopt($ch, CURLOPT_HEADER, 0);

//execute the curl to get the response
$r = curl_exec($ch);

echo $r;