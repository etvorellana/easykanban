<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	if ( isset($_SESSION['usu_id']) and isset($_GET['emp_id']) and isset($_GET['usu_id'] ) )
	{
		$usu_id_logado = $_SESSION['usu_id'];
		$emp_id = $_GET['emp_id'];
		$usu_id = $_GET['usu_id'];
		$action = &$_REQUEST;		
		
		if ( $action['haction']=='inserir')
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
		
			$query = "INSERT INTO usuario_empresa ( usu_id, emp_id ) VALUES ( '$usu_id', '$emp_id' )"
			or die ('Erro ao criar a consulta');
			
			// execulta query de inserção na tabela usuario_empresa
			$data = mysqli_query($dbc, $query)
				or die('Erro ao execultar a inserção na tabela usuario_empresa');
			
			/* Fecha conexão com o banco */
			mysqli_close($dbc);
		}
		
		header("Location: http://".move_header('config_company.php?emp_id=' . $emp_id));
	}
	
	function move_header($str){
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		return ($host.$uri.'/'.$str); 
	}

	
?>