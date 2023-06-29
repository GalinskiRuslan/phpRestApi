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
            $content  = null;
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
}
