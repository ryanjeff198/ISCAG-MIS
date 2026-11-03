<?php
$g = stream_context_create([
    "ssl" => [
        "capture_peer_cert" => true,
        "verify_peer" => false,
        "verify_peer_name" => false
    ]
]);
$r = @stream_socket_client("ssl://iscagph.com:443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $g);
if ($r) {
    $params = stream_context_get_params($r);
    $cert = $params["options"]["ssl"]["peer_certificate"];
    $info = openssl_x509_parse($cert);
    echo "ISSUER: " . json_encode($info["issuer"], JSON_PRETTY_PRINT) . PHP_EOL;
    echo "SUBJECT: " . json_encode($info["subject"], JSON_PRETTY_PRINT) . PHP_EOL;
    echo "VALID_TO: " . date("Y-m-d H:i:s", $info["validTo_time_t"]) . PHP_EOL;
} else {
    echo "Error: $errstr ($errno)\n";
}

