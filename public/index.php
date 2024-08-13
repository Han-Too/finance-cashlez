<?php
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

try {
    $response = $kernel->handle(
        $request = Request::capture()
    )->send();
    $kernel->terminate($request, $response);
} catch (\Throwable $th) {
    echo 'gagal';
    echo '<pre>';
    print_r($th);
    echo '</pre>';
    // Log the error
    file_put_contents(__DIR__.'/../storage/logs/custom_error.log', $th, FILE_APPEND);
    die();
}

?>
