<?
header('Content-Type: text/html; charset=utf-8');
require_once './src/Base/AutoLoader/AutoLoader.php';
header('Content-Type: application/json');

set_exception_handler(function (Exception $error) {
    echo json_encode(['state' => false, 'msg' => $error->getMessage()]);
});

use Base\AutoLoader\AutoLoader;
use Services\Service;

AutoLoader::init();

//минироутинг
echo \Api\Router\Router::run(Service::request()->post('action'));
