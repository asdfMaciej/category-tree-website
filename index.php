<?php
session_start();

function getTemplatePath($page) {
	return __DIR__ . "/templates/$page.php";
}

class DB {
	protected static $dbname = "strona";

	public static function getConnection() {
		$dbname = static::$dbname;
		$connection = new PDO("mysql:host=localhost;dbname=$dbname", "root", "");
		$connection->exec("set names utf8");
		return $connection;
	}
	public static function select($query, $prepared=[]) {
		$db = DB::getConnection();
		$s = $db->prepare($query);
		$s->execute($prepared);
		return $s->fetchAll(PDO::FETCH_ASSOC);
	}

	public static function execute($query, $prepared=[]) {
		$db = DB::getConnection();
		$s = $db->prepare($query);
		return $s->execute($prepared);
	}
}

class Page {
	public static function getPage() {
		return $_GET["page"] ?? "";
	}

	public static function renderMain($popup="") {
		$category_id = $_GET["category"] ?? 0;
		$search = $_GET["search"] ?? "";

		$searching = [];
		if ($search) {
			$items = Item::printInSearch($search);
		} else {
			$items = Item::printInCategory($category_id);
		}
		
		$categories = Category::printAll();

		require getTemplatePath("content");
	}

	public static function handlePost() {
		if ($_SERVER['REQUEST_METHOD'] !== 'POST')
			return '';
		
		$action = $_POST['action'] ?? "";
		$abort = False;
		switch ($action) {
			case 'add_category':
			case 'add_item':
			case 'delete_category':
			case 'delete_item':
			case 'edit_category':
				$abort = !Auth::isAdmin();
			
			default:
				break;
		}

		if ($abort)
			return "Nie jesteś administratorem!";

		switch ($action) {
			case 'add_category':
				return Category::addCategory($_POST);

			case 'add_item':
				return Item::addItem($_POST);

			case 'delete_category':
				return Category::deleteCategory($_POST);

			case 'delete_item':
				return Item::deleteItem($_POST);

			case 'edit_category':
				return Category::editCategory($_POST);

			case 'register':
				return Auth::register(
						$_POST["login"] ?? "",
						$_POST["password"] ?? ""
					);

			case 'login':
				return Auth::login(
						$_POST["login"] ?? "",
						$_POST["password"] ?? ""
					);

			case 'logout':
				return Auth::logout();


			default:
				return 'co jest 5';
		}
	}

	public static function render() {
		$popup = static::handlePost();
		require getTemplatePath("header");
		switch (static::getPage()) {
			case 'main':
			default:
				static::renderMain($popup);
			break;
		}
		require getTemplatePath("footer");
	}
}

class Auth {
	public static function register($login, $password) {
		if (!$login || !$password)
			return "Uzupełnij wszystko.";

		$hash = password_hash($password, PASSWORD_DEFAULT);
		$success = DB::execute("insert into users (login, password) values (:login, :pass)",
								[":login" => $login, ":pass" => $hash]);
		if ($success)
			return "Zarejestrowano.";
		else
			return "Nie udało się zarejestrować.";
	}

	public static function login($login, $password) {
		if (!$login || !$password)
			return "Uzupełnij wszystko.";

		$user = User::getByLogin($login);
		if (!$user)
			return "Nie znaleziono takiego użytkownika";


		$user = $user[0];
		$hash = $user["password"];
		if (!password_verify($password, $hash)) {
			return "Hasło się nie zgadza.";
		}

		$_SESSION["user"] = $user;
		return "Zalogowano.";
	}

	public static function logout() {
		if (isset($_SESSION["user"]))
			unset($_SESSION["user"]);
		return "Wylogowano.";
	}

	public static function isAdmin() {
		if (!isset($_SESSION["user"]))
			return False;

		return $_SESSION["user"]["admin"] == 1;
	}
}

class User {
	public static function getByID($id) {
		return DB::select("select * from users where id = :id", [":id" => $id]);
	}

	public static function getByLogin($login) {
		return DB::select("select * from users where login = :login", [":login" => $login]);
	}
}

class Item {
	public static function printInCategory($category_id) {
		$params = [":id" => $category_id];
		$items = DB::select("select * from items where category_id = :id", $params);
		$category = DB::select("select * from categories where id = :id", $params);
		if (empty($category)) {
			return "Wybrana kategoria nie istnieje.";

		}
		$category = $category[0];
		$template = getTemplatePath("item");
		ob_start();
		require $template;
		return ob_get_clean();
	}

	public static function printInSearch($name) {
		$items = DB::select("select * from items where name like :name",
						[":name" => "%".$name."%"]);

		$search = True;
		$template = getTemplatePath("item");
		ob_start();
		require $template;
		return ob_get_clean();
	}

	public static function addItem($params) {
		$name = $params["name"] ?? "";
		$category_id = $params["category_id"] ?? -1;
		if (!$name || $category_id == -1) {
			return "Nie podano wszystkiego.";
		}

		$success = DB::execute("INSERT INTO items (category_id, name)
								VALUES (:category_id, :name)",
								[":category_id" => $category_id, ":name" => $name]);

		if ($success)
			return "Dodano nowego zwierzaka..";
		else
			return "Nie udało się dodać nowego zwierzaka.";
	}

	public static function deleteItem($params) {
		$id = $params["id"] ?? "";
		if ($id === "")
			return "Nie podano ID.";
		
		$success = DB::execute("DELETE FROM items WHERE id = :id",
								[":id" => $id]);

		if ($success)
			return "Usunięto zwierzaka.";
		else
			return "Nie udało się usunąć zwierzaka.";
	}
}

class Category {
	public static function getAll() {
		$cats = DB::select(
			"select * from categories where id != 0");
		$items = DB::select("select * from items");
		return static::buildTree($cats, $items);
	}

	public static function addCategory($params) {
		$name = $params["name"] ?? "";
		$parent_id = $params["parent_id"] ?? -1;
		if (!$name || $parent_id == -1) {
			return "Nie podano wszystkiego.";
		}

		$success = DB::execute("INSERT INTO categories (parent_id, name)
								VALUES (:parent_id, :name)",
								[":parent_id" => $parent_id, ":name" => $name]);

		if ($success)
			return "Dodano nową kategorię.";
		else
			return "Nie udało się dodać nowej kategorii.";
	}

	public static function editCategory($params) {
		$name = $params["name"] ?? "";
		$id = $params["id"] ?? -1;
		if (!$name || $id == -1) {
			return "Nie podano wszystkiego.";
		}

		$success = DB::execute("UPDATE categories SET name = :name WHERE id = :id",
								[":id" => $id, ":name" => $name]);

		if ($success)
			return "Edytowano kategorię.";
		else
			return "Nie udało się zedytować kategorii.";
	}

	public static function deleteCategory($params) {
		$id = $params["id"] ?? "";
		if (!$id) // nie można usunąć kategorii #0
			return "Nie podano ID kategorii.";
		

		$success = DB::execute("DELETE FROM categories WHERE id = :id",
								[":id" => $id]);

		if ($success)
			return "Usunięto kategorię.";
		else
			return "Nie udało się usunąć kategorii.";
	}

	// https://stackoverflow.com/questions/8587341/recursive-function-to-generate-multidimensional-array-from-database-result/
	// modified
	protected static function buildTree(array $elements, $items, $parentId = 0) {
		$branch = array();

		foreach ($elements as $element) {
			if ($element['parent_id'] == $parentId) {
				$children = static::buildTree($elements, $items, $element['id']);
				$element['children'] = $children ?? [];

				// O(N of items * M of cats)
				// inefficient but quick to write
				$element["items"] = [];
				foreach ($items as $i)
					if ($i["category_id"] == $element["id"])
						$element["items"][] = $i;

				$branch[] = $element;
			}
		}

		return $branch;
	}

	public static function printAll() {
		$categoryTree = static::getAll();
		function printBranch($tree) {
			$txt = "";
			$template = getTemplatePath("category");
			foreach ($tree as $node) {
				$id = $node['id'];
				$name = $node['name'];
				$items = $node['items'];
				$categories = printBranch($node['children']);
				
				ob_start();
				require $template;
				$txt .= ob_get_clean();
			}
			return $txt;
		}

		return printBranch($categoryTree);
	}
}

Page::render();
?>