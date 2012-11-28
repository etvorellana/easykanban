<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	if ( isset($_SESSION['usu_id']) and isset($_GET['pro_id']) )
	{
		$usu_id_logado = $_SESSION['usu_id'];
		$pro_id = $_GET['pro_id'];
		$action = &$_REQUEST;		
		
		if ( $action['action']=='inserir')
		{
			$insert_user = $_GET['insert_user'];
			
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
		
			$query = "INSERT INTO usuario_projeto_tipo ( pro_id, usu_id, tip_id ) VALUES (  '$pro_id', '$insert_user', 2 )"
			or die ('Erro ao criar a consulta');
			
			// execulta query de inserção na tabela usuario_projeto
			$data = mysqli_query($dbc, $query)
				or die('Erro ao execultar a inserção na tabela usuario_projeto_tipo');
			
			/* Fecha conexão com o banco */
			mysqli_close($dbc);
		}
		
		if ( $action['action'] == 'remove')
		{
			$remove_user = $_GET['remove_user'];
			
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
			$query = "DELETE FROM usuario_projeto_tipo WHERE usu_id =%s AND pro_id = %s"
			or die ('Erro ao criar a consulta');
			
			// alimenta os parametros da conculta
			$query = sprintf($query, $remove_user, $pro_id );	
			
			// execulta query de remoção na tabela usuario_projeto
			$data = mysqli_query($dbc, $query)
				or die('Erro ao execultar a inserção na tabela usuario_projeto');
			
			/* Fecha conexão com o banco */
			mysqli_close($dbc);	
		}
		
		if ( $action['action'] == 'changetype')
		{
			$old_tip_id = $_GET['tip_id'];
			$edit_usu_id =  $_GET['edit_usu_id'];
			
			if ( $old_tip_id == 1 )
				$new_tip_id = 2;
			else
				$new_tip_id = 1;
			
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
			$query = "SET FOREIGN_KEY_CHECKS = 0";
			$data = mysqli_query($dbc, $query) or die('aqui');
			
			$query = "UPDATE usuario_projeto_tipo SET tip_id = %s WHERE pro_id = %s AND usu_id =%s AND tip_id = %s LIMIT 1"
			or die ('Erro ao criar a consulta');
			
			// alimenta os parametros da conculta
			$query = sprintf($query, $new_tip_id, $pro_id, $edit_usu_id, $old_tip_id );	
			
			// execulta query de remoção na tabela usuario_projeto
			$data = mysqli_query($dbc, $query)
				or die('Erro ao execultar a atualização na tabela usuario_projeto_tipo');
			
			$query = "SET FOREIGN_KEY_CHECKS = 1";
			$data = mysqli_query($dbc, $query) or die('aqui');;
			
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