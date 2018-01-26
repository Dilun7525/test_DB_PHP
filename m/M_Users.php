<?php

//
// Менеджер пользователей
//
class M_Users
{
	private static $instance;   // экземпляр класса
	private $driverDB;          // драйвер БД

	private $authorization;     // состояние авторизации
	private $user;              // авторизованный пользователь
	private $idUser;            // id пользователя
	private $trueAdmin;         // пользователь - это администратор
	private $sortType;          //ASC или DESC
	private $switchSorting;     //true или false


	public static function getInstance()
	{
		if(self::$instance == null)
			self::$instance = new M_Users();

		return self::$instance;
	}

	protected function __construct()
	{
		$this->driverDB = M_DB::getInstance(PATH_CONFIGS . "db_install.txt");

		$this->authorization = !empty($_SESSION["authorization"])
			? $_SESSION["authorization"]
			: null;
		$this->user = !empty($_SESSION["user"])
			? $_SESSION["user"]
			: null;
		$this->idUser = !empty($_SESSION["idUser"])
			? $_SESSION["idUser"]
			: null;
		$this->trueAdmin = !empty($_SESSION["trueAdmin"])
			? $_SESSION["trueAdmin"]
			: null;
		$this->switchSorting = !empty($_SESSION["switchSorting"])
			? $_SESSION["switchSorting"]
			: null;
		$this->sortType = !empty($_SESSION["sortType"])
			? $_SESSION["sortType"]
			: null;

	}

	public function validateRegistrationForm()
	{
		$result = '';
		$trueError = false;
		$login = '';
		$pass = '';
		$email = '';
		foreach ($_POST as $k => $v) {
			$$k = $v;
		}
		// todo: после доработать механизм проверки на наличие уже такого
		// логина в базе
		if(strlen($login) <= 4) {
			$trueError = true;
			($login === '')
				? $result .= "Логин не может быть пустым" . "<br/>"
				: $result .= "Логин должен содержать 5 и более символов" . "<br/>";
		}
		if(strlen($pass) <= 5) {
			$trueError = true;
			($pass === '')
				? $result .= "Пароль не может быть пустым" . "<br/>"
				: $result .= "Пароль должен содержать 6 и более  символов" . "<br/>";
		}
		if(strlen($email) <= 6) {
			$trueError = true;
			($email === '')
				? $result .= "Email не может быть пустым" . "<br/>"
				: $result .= "Введите настоящий Email" . "<br/>";
		}

		if($trueError) {
			return [$trueError, $result];
		} else {
			return [$trueError, $_POST];
		}


	}

	public function validateInputForm()
	{
		$result = null;
		$trueError = false;
		$itemName = (!empty($_POST['login'])) ? $_POST['login'] : '';
		$pass = (!empty($_POST['pass'])) ? $_POST['pass'] : '';

		if(!empty($login = $this->getUser($itemName, "login"))) {
			$result = $login;
		} else if(!empty($email = $this->getUser($itemName, "email"))) {
			$result = $email;
		} else {
			$trueError = true;
			return [$trueError, "Неверно введено имя, email или пароль"];
		}

		if($pass !== $result["pass"]) {
			$trueError = true;
			return [$trueError, "Неверно введено имя, email или пароль"];
		}

		return [$trueError, $result];

	}

	public function registrationUser($formParameters)
	{
		$role = (!empty($formParameters["role"]))
			? $this->getIdRole($formParameters["role"])
			: $this->getIdRole("пользователь");
		$dataTable = [
			"login" => $formParameters["login"],
			"pass" => $formParameters["pass"],
			"email" => $formParameters["email"],
			"first_name" => $formParameters["nameUser"],
			"middle_name" => $formParameters["middleNameUser"],
			"surname" => $formParameters["surnameUser"],
			"role" => $role,];

		return $this->driverDB->insert("users", $dataTable);
	}

	public function activationUser($id)
	{
		$resultRequest = $this->getUser($id);
		$_SESSION["authorization"] = true;
		$_SESSION["idUser"] = $resultRequest["id"];
		$_SESSION["user"] = $resultRequest["login"];
		if($resultRequest["role"] === "администратор") {
			$_SESSION["trueAdmin"] = true;
		}

	}

	public function getUser($valueField, $searchField = "id")
	{
		$format = "SELECT users.id, login,  pass,  email, " .
			"surname,  first_name,  middle_name, role.role " .
			"FROM users INNER JOIN role ON users.role = role.id " .
			"WHERE users.%s = '%s'";

		$query = sprintf($format, $searchField, $valueField);
		$result = $this->driverDB->Select($query);
		return (!empty($result)) ? $result[0] : null;
	}

	public function getUsers($sortColumn = "id", $sortType = "ASC")
	{
		$result = [];
		$format = "SELECT users.id, login,  pass,  email, " .
			"surname,  first_name,  middle_name, role.role " .
			"FROM users INNER JOIN role ON users.role = role.id " .
			"ORDER BY %s %s";

		$query = sprintf($format, $sortColumn, $sortType);
		$result = $this->driverDB->Select($query);
		return (!empty($result)) ? $result : null;
	}

	public function switchSortType()
	{
		if($this->switchSorting) {
			$_SESSION["switchSorting"] = false;
			$this->switchSorting = false;
			$_SESSION["sortType"] = "ASC";
		} else {
			$_SESSION["switchSorting"] = true;
			$this->switchSorting = true;
			$_SESSION["sortType"] = "DESC";
		}
	}

	public function getRoleUser($id)
	{
		$format = "SELECT role.role " .
			"FROM users INNER JOIN role ON users.role = role.id " .
			"WHERE users.id = '%s'";

		$query = sprintf($format, $id);
		$result = $this->driverDB->Select($query);
		return (!empty($result)) ? $result[0] : null;
	}

	public function getIdRole($valueField)
	{
		$format = "SELECT id FROM role WHERE role = '%s'";
		$query = sprintf($format, $valueField);
		$result = $this->driverDB->Select($query);
		return (!empty($result[0])) ? $result[0]["id"] : null;
	}

	public function editProfile($formParameters)
	{
		$id = $formParameters["id"];
		$role = ($this->getIdRole($formParameters["role"]))
			? $this->getIdRole($formParameters["role"])
			: $this->getIdRole("пользователь");

		$object = [
			"login" => $formParameters["login"],
			"pass" => $formParameters["pass"],
			"email" => $formParameters["email"],
			"first_name" => $formParameters["nameUser"],
			"middle_name" => $formParameters["middleNameUser"],
			"surname" => $formParameters["surnameUser"],
			"role" => $role,];

		$where = "id = $id";
		$result = $this->driverDB->update("users", $object, $where);

		return (!empty($result)) ? $result : null;
	}

	public function deleteProfile($id)
	{
		$table = "users";
		$where = "id = $id";
		$result = $this->driverDB->delete($table, $where);

		return (!empty($result)) ? $result : null;
	}

	public function ClearSessions()
	{
		foreach ($_SESSION as $k => $v) {
			unset($_SESSION[$k]);
		}
	}
}