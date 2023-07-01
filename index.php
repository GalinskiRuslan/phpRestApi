<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');




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
            if (isset($id)) {
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
    case 'DELETE':
        if ($type === 'posts') {
            if (isset($id)) {
                $connect->deletePost($id);
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
    case 'PUT':
        if ($type === 'posts') {
            if (isset($id)) {
                $data = json_decode(file_get_contents('php://input'), true);
                $connect->updatePost($id, $data);
            } else {
                http_response_code(400);
                $res = [
                    "status" => 400,
                    "message" => "Bad request"
                ];
                echo json_encode($res);
            }
        } else {
            http_response_code(400);
            $res = [
                "status" => 400,
                "message" => "Bad request"
            ];
            echo json_encode($res);
        }
        break;
}
