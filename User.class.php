<?php 

$user1 = new User("Romil");
$user2 = new User("Juku");

?>

<?php 
class User {
	
	//klassi loomisel (new User)
	function __construct($name) {
		echo $name." <br>";
	}
	
	
} ?>