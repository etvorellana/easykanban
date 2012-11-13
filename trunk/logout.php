<?php
	// Se o usuário estiver logado, delete as váriáveis de sessão para este ser deslogado
	session_start();
	
	if (isset($_SESSION['usu_id'])) {
			
		// Delete o conteúdo da super_global 
		$_SESSION = array();
		
		// deletando o cookie que é criado para armazenar o id da sessão
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time() - 3600);
		}
		
		// Destroi a sessão
		session_destroy();
	}
	
	// Deleta os cookies usu_id, usu_nome, usu_email configurando seu prazo de validade para uma hora atras
	setcookie('usu_id', '', time() - 3600);
	setcookie('usu_nome', '', time() - 3600);
	setcookie('usu_email', '', time() - 3600);
	
	// Redirecionando para a página de login
	$home_url = 'index.php';
	header('Location: ' . $home_url);
?>