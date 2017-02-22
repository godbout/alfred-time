<?php

$url = 'https://slmd.harvestapp.com/daily/add';

$base64Token = 'Z3VpbGwuYm91dEBnbWFpbC5jb206NmhhN3FUdj9AR0tRNl1SPSwyPm91YVRtcSwkOU5bMzY=';

$headers = [
    "Content-type: application/json",
    "Accept: application/json",
    'Authorization: Basic ' . $base64Token,
];

$item = [
    'notes' => $query,
    'project_id' => '12405684',
    'task_id' => 6960835,
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($item, true));
$response = curl_exec($ch);
curl_close($ch);
