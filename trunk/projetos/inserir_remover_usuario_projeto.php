<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	if ( isset($_SESSION['usu_id']) and isset($_GET['pro_id']) and isset($_GET['insert_user'] ) )
	{
		$usu_id_logado = $_SESSION['usu_id'];
		$pro_id = $_GET['pro_id'];
		$insert_user = $_GET['insert_user'];
		$action = &$_REQUEST;		
		
		if ( $action['action']=='inserir')
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
		
			$query = "INSERT INTO usuario_projeto ( usu_id, pro_id ) VALUES ( '$insert_user', '$pro_id' )"
			or die ('Erro ao criar a consulta');
			
			// execulta query de inserção na tabela usuario_projeto
			$data = mysqli_query($dbc, $query)
				or die('Erro ao execultar a inserção na tabela usuario_projeto');
			
			/* Fecha conexão com o banco */
			mysqli_close($dbc);
		}
		
		header("Location: http://".move_header('config_projeto.php?pro_id=' . $pro_id));
	}
	
	function move_header($str){
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		return ($host.$uri.'/'.$str); 
	}

	
?>