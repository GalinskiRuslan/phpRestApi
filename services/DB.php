<?php

namespace App\services;

use PDO;

class DB
{
    private $config = [
        'driver' => 'mysql',
        'host' => 'localhost',
        'db' => 'test_blog',
        'charset' => 'UTF8',
        'user' => 'root',
        'password' => '1234'
    ];
    protected function getConnect()

    {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_STRINGIFY_FETCHES => false,
            PDO::ATTR_EMULATE_PREPARES => false,

        ];
        $dsn = "{$this->config['driver']}:host={$this->config['host']};dbname={$this->config['db']}; charset={$this->config['charset']}";
        $connect = new PDO($dsn, $this->config['user'], $this->config['password'], $options);
        return $connect;
    }
    protected function query($sql, $params = [])
    {
        $PDOStatment = $this->getConnect()->prepare($sql);
        $PDOStatment->execute($params);
        return $PDOStatment;
    }
    public function getLastInsertId()
    {
        return $this->getConnect()->lastInsertId();
    }
    public function getAllPosts()
    {
        echo json_encode($this->query('SELECT * FROM posts')->fetchAll());
    }
    public function getPostById($id)
    {
        $post = $this->query('SELECT * FROM posts WHERE id = :id', [$id])->fetch();
        if ($post) {
            echo json_encode($post);
        } else {
            http_response_code(404);
            $res = [
                "status" => 404,
                "message" => "Post not found"
            ];
            echo json_encode($res);
        }
    }
    public function addPost($params)
    {
        if (isset($params['post_title'])) {
            $title = $params['post_title'];
        } else {
            $title = null;
        }
        if (isset($params['post_text'])) {
            $content = $params['post_text'];
        } else {
            $content  = '';
        }
        if (isset($params['post_img'])) {
            $srcImg = $params['post_img'];
        } else {
            $srcImg = null;
        }
        $this->query(
            "INSERT INTO posts (post_title, post_text, post_img)
         VALUES ( :post_title, :post_text, :post_img )",
            array(':post_title' => $title, ':post_text' => $content, ':post_img' => $srcImg)
        );
        http_response_code(201);
        $res = [
            "status" => 201,
            "message" => "Post added"
        ];
        echo json_encode($res);
    }
    public function deletePost($id)
    {
        $isPost = $this->query('SELECT * FROM posts WHERE id = :id', [$id])->fetch();
        if ($isPost) {
            $post = $this->query('DELETE FROM posts WHERE id = :id', [$id]);

            if (!$post) {
                http_response_code(400);
                $res = [
                    "status" => 400,
                    "message" => "can't delete post"
                ];
                echo json_encode($res);
            } else {

                http_response_code(200);
                $res = [
                    "status" => 200,
                    "message" => "Post deleted"
                ];
                echo json_encode($res);
            }
        } else {
            http_response_code(404);
            $res = [
                "status" => 404,
                "message" => "Post not found"
            ];
            echo json_encode($res);
        }
    }
    public function updatePost($id, $params)
    {
        $isPost = $this->query('SELECT * FROM posts WHERE id = :id', [$id])->fetch();
        if ($isPost) {
            $updatePost = $this->query(
                'UPDATE posts SET post_title = :post_title, post_text = :post_text, post_img = :post_img WHERE (`id` = :id)',
                array(':post_title' => $params['post_title'], ':post_text' => $params['post_text'], ':post_img' => $params['post_img'], ':id' => $id)
            );
            if ($updatePost) {
                http_response_code(200);
                $res = [
                    "status" => 200,
                    "message" => "Post updated"
                ];
                echo json_encode($res);
            } else {
                http_response_code(400);
                $res = [
                    "status" => 400,
                    "message" => "Can't update post1"
                ];
                echo json_encode($res);
            }
        }
    }
}
