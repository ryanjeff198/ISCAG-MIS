<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/routes/web.php';

$test_route = '/admin/mis_admin/apartment_confirmation/image';
echo "Testing route: '$test_route'\n";

if (isset($routes[$test_route])) {
    echo "MATCH FOUND!\n";
    print_r($routes[$test_route]);
} else {
    echo "NO MATCH in routes array.\n";
    echo "Available admin routes:\n";
    foreach ($routes as $path => $handler) {
        if (str_contains($path, 'admin/mis_admin')) {
            echo " - '$path'\n";
        }
    }
}
