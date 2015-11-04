<?php 
class User {
	
	//private - klassi sees
	private $connection;
	
	//klassi loomisel (new User)
	function __construct($mysqli) {
		
		// this t�hendab selle klassi muutujat
		$this->connection = $mysqli;
	}
	
	function createUser($create_email, $hash){
		
		// teen objekti 
		// seal on error, ->id ja ->message
		// v�i success ja sellel on ->message
		$response = new StdClass();
		
		//kas selline email on juba olemas
		$stmt = $this->connection->prepare("SELECT id FROM user_sample WHERE email=?");
		$stmt->bind_param("s", $create_email);
		$stmt->bind_result($id);
		$stmt->execute();
		
		// kas sain rea andmeid
		if($stmt->fetch()){
			
			// annan errori, et selline email olemas
			$error = new StdClass();
			$error->id = 0;
			$error->message = "Sellise e-postiga kasutaja on juba olemas!";
			
			$response->error = $error;
			
			// k�ik mis on p�rast returni enam ei k�ivitata
			return $response;
			
		}
		
		// panen eelmise p�ringu kinni
		$stmt->close();
		
		$stmt = $this->connection->prepare("INSERT INTO user_sample (email, password) VALUES (?,?)");
		$stmt->bind_param("ss", $create_email, $hash);
		
		// sai edukalt salvestatud
		if($stmt->execute()){
			
			$success = new StdClass();
			$success->message = "Kasutaja edukalt loodud!";
			
			$response->success = $success;
			
		}else{
			
			// midagi l�ks katki
			$error = new StdClass();
			$error->id = 1;
			$error->message = "Midagi l�ks katki!";
			
			$response->error = $error;
			
		}
		
		$stmt->close();
		
		return $response;
	}
	
	function loginUser($email, $hash){

		$stmt = $this->connection->prepare("SELECT id, email FROM user_sample WHERE email=? AND password=?");
		$stmt->bind_param("ss", $email, $hash);
		$stmt->bind_result($id_from_db, $email_from_db);
		$stmt->execute();
		if($stmt->fetch()){
			// ab'i oli midagi
			echo "Email ja parool �iged, kasutaja id=".$id_from_db;
			
			// tekitan sessiooni muutujad
			$_SESSION["logged_in_user_id"] = $id_from_db;
			$_SESSION["logged_in_user_email"] = $email_from_db;
			
			//suunan data.php lehele
			header("Location: data.php");
			
		}else{
			// ei leidnud
			echo "Wrong credentials!";
		}
		$stmt->close();
	}
	
} ?>