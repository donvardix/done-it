<?php


namespace models;

use PDO;

class Users
{
    private $connect, $method, $id;

    public function __construct($connect, $method, $id)
    {
        $this->connect = $connect;
        $this->method = $method;
        $this->id = $id;
    }

    public function apply()
    {
        if (method_exists($this, $this->method)) {
            $method = $this->method;
            return $this->$method();
        } else {
            http_response_code(404);
            return json_encode(['status' => false, 'data' => 'Unknown Method']);
        }
    }

    public function get()
    {
        if ($this->is_authorized($_GET['token'], true)) {
            $where = ! empty($this->id) ? ' WHERE id = ?' : '';

            $res = $this->connect->prepare('SELECT `name` FROM users' . $where);
            if (empty($this->id)) {
                $res->execute();
            } else {
                $res->execute([$this->id]);
            }
            $query = $res->fetchAll(PDO::FETCH_ASSOC);

            if (count($query)) {
                return json_encode(['status' => true, 'data' => $query]);
            } else {
                http_response_code(404);
                return json_encode(['status' => false, 'data' => 'Not Found']);
            }
        } else {
            http_response_code(403);
            return json_encode(['status' => false, 'data' => 'Forbidden']);
        }
    }

    public function post()
    {
        if ($this->is_authorized($_GET['token'], true)) {
            $sql = "INSERT INTO users(name, email, password) VALUES(?, ?, ?)";
            $query = $this->connect->prepare($sql);
            $query->execute([
                $_POST['name'],
                $_POST['email'],
                $_POST['password']
            ]);
            return json_encode(['status' => true, 'data' => $this->connect->lastInsertId()]);
        } else {
            http_response_code(403);
            return json_encode(['status' => false, 'data' => 'Forbidden']);
        }

    }

    public function is_authorized($token, $check_admin = false) {
        $res = $this->connect->prepare('SELECT * FROM users WHERE api_token = ?');
        $res->execute([$token]);
        $query = $res->fetch();

        if ($query) {
            if ($check_admin && 0 == $query['is_admin']){
                return false;
            }
            return true;
        }
        return false;
    }
}