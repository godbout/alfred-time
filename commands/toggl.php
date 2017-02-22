<?php

$url = 'https://www.toggl.com/api/v8/time_entries/start';

$apiToken = '7b9071f5580e3e565c1a98f709b8a3da';

$headers = [
    "Content-type: application/json",
    "Accept: application/json",
    'Authorization: Basic ' . base64_encode($apiToken . ':api_token'),
];

$item = [
    'time_entry' => [
        'description' => $query,
        'pid' => '26342720',
        'tags' => ['package'],
        'created_with' => 'Alfred Time Workflow',
    ],
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($item, true));
$response = curl_exec($ch);
curl_close($ch);
