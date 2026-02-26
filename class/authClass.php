<?php
class authClass
{
	public static function is_auth($current_session)
	{
		if (isset($current_session['user']) && !empty($current_session['user']) && isset($current_session['auth_token']))
			return true;
		return false;
	}

	public static function authenticate($username, $password)
	{
		try {
			$db = require dirname(__FILE__) . '/../lib/pdo.php';
			$fields = array(
				'nom_util',
				'admin'
			);
			$sql = "SELECT ".implode(', ', $fields)." 
					FROM utilisateurs 
					WHERE nom_util = :username AND mdp = :passhash";
			$statement = $db->prepare($sql);
			$statement->bindValue(':username', $username, PDO::PARAM_STR);
			$statement->bindValue(':passhash', md5($password), PDO::PARAM_STR);
			$statement->execute();
			$result = $statement->fetch(PDO::FETCH_ASSOC);
			return $result;
		
		} catch (Error|Exception $e) {
			echo $e->getMessage() . ' -> file: ' . $e->getFile() . ' - ligne: ' . $e->getLine();
		}
	}

	public static function checkPriviledAdmin($username)
	{
		try {
			$db = require dirname(__FILE__) . '/../lib/pdo.php';

			$fields = array(
				"admin",
			);
			$sql = "SELECT distinct ".implode(", ", $fields)."
					FROM utilisateurs
					WHERE nom_util = :nom_util ";
			$statement = $db->prepare($sql);
			$statement->bindValue(':nom_util', $username, PDO::PARAM_STR);
			$statement->execute();
			$result = $statement->fetch(PDO::FETCH_ASSOC)['admin'];
			return $result;

		} catch (Error|Exception $e) {
			echo $e->getMessage() . ' -> file: ' . $e->getFile() . ' - ligne: ' . $e->getLine();
		}
	}
}
