<?php

use App\services\DB;

use function PHPSTORM_META\type;

require "./services/DB.php";
header('Content-Type: application/json');

$connect = new DB();

$method = $_SERVER['REQUEST_METHOD'];
$q = $_GET['q'];
$params = explode('/', $q);
$type = $params[0];
if (count($params) > 1) {
    $id = $params[1];
}

switch ($method) {
    case 'GET':
        if ($type === 'posts') {
            if (isset($id)) {
                $connect->getPostById($id);
            } else {
                $connect->getAllPosts();
            }
        }
        break;
    case 'POST':
        if ($type === 'posts') {
            if (count($_POST) > 0) {
                $connect->addPost($_POST);
            } else {
                http_response_code(400);
                $res = [
                    "status" => 400,
                    "message" => "Bad request"
                ];
                echo json_encode($res);
            }
        }
        break;
}
