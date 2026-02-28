<?php
function normalize($address) {
    $url = "https://tonapi.io/v2/accounts/" . urlencode($address);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    return $data['address'] ?? null;
}

$user = "UQDolrO5cILq-RSkftro3HF3ZsvCI9qBHeiWgcgSOoLeCHB5";
$dest = "UQBTW0bj2lR0NQ2WdlRVyuLNNO55uokEo9hauUMWOArxAKVx";

echo "User: " . $user . " -> " . normalize($user) . "\n";
echo "Dest: " . $dest . " -> " . normalize($dest) . "\n";
