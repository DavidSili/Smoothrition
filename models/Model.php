<?php
class Db {
    private static $instance = NULL;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance() {
        if (!isset(self::$instance)) {
            $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
            $pdo_options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES 'utf8'";
            self::$instance = new PDO('mysql:host=localhost;dbname=smoothrition', 'root', '', $pdo_options);
        }
        return self::$instance;
    }

}
class Model {

    protected $db;

    public function __construct() {
        $this->db = Db::getInstance();
    }

    public function login ($username) {
        $sql = "SELECT username, password, settings FROM users WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(":username" => $username));
        $return = $stmt->fetch(PDO::FETCH_ASSOC);
        return $return;
    }

	public function getFoodGroups() {
		return array(
			'0900' => 'Voća i voćni sokovi',
			'1200' => 'Koštunjavo voće i semenje',
			'1400' => 'Pića',
			'0200' => 'Začini i lekovito bilje',
			'0400' => 'Masti i ulja',
			'1100' => 'Povrće',
			'0100' => 'Proizvodi od mleka i jaja',
			'1900' => 'Slatkiši',
		);
	}

    public function loadTasks ($user = NULL) {
        if (NULL == $user) {
            die;
        }
        $sql = "SELECT id, title, done FROM tasks WHERE user = :user";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(":user" => $user));
        $return = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($return);
    }

    public function insertT ($user = null, $task = null) {
        if (empty($user) || null == $task) {
            return false;
        } else {
            $sql = 'INSERT INTO tasks (user, title) VALUES (:user, :task)';
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute(array(':user' => $user, ':task' => $task));
            return ($result == true) ? 'ok' : 'error';
        }
    }

    public function doneT ($user = null, $t_id = null, $done) {
        if (empty($user) || null == $t_id || null === $done ) {
            return false;
        } else {
            $sql = 'UPDATE tasks SET done = :done WHERE `user` = :user AND id = :id';
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute(array(':user' => $user, ':id' => $t_id, ':done' => $done));
            return ($result == true) ? '{"callback":"ok"}' : '{"callback":"error"}';
        }
    }

}