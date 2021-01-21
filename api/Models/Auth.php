<?php


namespace models;

use PDO;

class Auth
{
    private $connect, $route;

    public function __construct($connect, $route)
    {
        $this->connect = $connect;
        $this->route = $route;
    }

    public function apply()
    {
        if (method_exists($this, $this->route)) {
            $method = $this->route;
            return $this->$method();
        } else {
            http_response_code(404);
            return json_encode(['status' => false, 'data' => 'Unknown Model']);
        }
    }

    public function register()
    {
        $res = $this->connect->prepare('SELECT * FROM users WHERE email = ?');
        $res->execute([$_POST['email']]);
        $query = $res->fetchAll(PDO::FETCH_ASSOC);

        if (count($query)) {
            http_response_code(400);
            return json_encode(['status' => false, 'data' => 'User Exists']);
        } else {
            $sql = "INSERT INTO users(name, email, password) VALUES(?, ?, ?)";
            $query = $this->connect->prepare($sql);
            $password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $query->execute([
                $_POST['name'],
                $_POST['email'],
                $password_hash
            ]);
            http_response_code(201);
            return json_encode(['status' => true, 'data' => 'User created']);
        }
    }

    public function login()
    {
        $res = $this->connect->prepare('SELECT * FROM users WHERE email = ?');
        $res->execute([$_POST['email']]);
        $query = $res->fetch();

        if ($query && password_verify($_POST['password'], $query['password'])) {
            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $api_token = substr(str_shuffle($permitted_chars), 0, 60);

            $sql = "UPDATE users SET api_token=? WHERE id=?";
            $stmt= $this->connect->prepare($sql);
            $stmt->execute([$api_token, $query['id']]);

            return json_encode(['status' => true, 'data' => $api_token]);
        } else {
            http_response_code(400);
            return json_encode(['status' => false, 'data' => 'Not Correct']);
        }
    }
}