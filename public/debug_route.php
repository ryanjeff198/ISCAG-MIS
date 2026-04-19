<?php
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$baseUrl = str_replace('/public/index.php', '', $scriptPath);
echo "Detected BASE_URL: " . $baseUrl . "<br>";
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
echo "Parsed requestUri: " . $requestUri . "<br>";
$route = $requestUri;
if ($baseUrl !== '' && strpos($route, $baseUrl) === 0) {
    $route = substr($route, strlen($baseUrl));
}
echo "Final route: [" . $route . "]<br>";
$route = '/' . trim($route, '/');
echo "Cleaned route: [" . $route . "]<br>";
