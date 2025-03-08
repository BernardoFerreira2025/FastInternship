<?php	
include  '../database/mysqli.php';

$emptyUsernameOrPassword = '';



if($_POST){ // Se existir um post, entra!

	$username = $_POST['username'];  // Get do username
	$password = $_POST['password'];  // Get da password

	$password = md5($password);
	

	

	if ($username && $password){ // Validar se ambos os campos têm valor.	
		
		$query = "SELECT * FROM utilizadores WHERE username='{$username}' AND password='{$password}'";			
		$sql = mysqli_query($conn, $query);
		$result = mysqli_fetch_all($sql, MYSQLI_ASSOC);
		
		
		if(count($result) > 0) // Se encontrou password porque está registado
							//if($result) // Se encontrou password porque está registado
		{
			include "./assets/elements/header_login.php";
			session_start() ;
			echo "utilizador encontrado";
			$_SESSION['username'] = $username; // Cria um cookie saving the username
			$_SESSION['loggedIn'] = true; // Creates a cookie saying the user is logged in
			//header("Location: menu.php \n"); // redireciona para pagina protegida.

			echo  $_SESSION['username'];
		}
		else
		{ 
			include "./assets/elements/header.php";
			
			echo "Utilizador não encontrado";
			//header("Location: index.php \n"); // Não existe o utilizador, redirect  para a pagina de login.
		}
		}else{
				$emptyUsernameOrPassword = true; 
		}
	}
?>