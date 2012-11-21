<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	if (isset($_SESSION['usu_id']) and isset($_GET['pro_id'])) 
	{
		$action = &$_REQUEST;
		$pro_id = $_GET['pro_id'];
		
		// se o status da tarefa foi modificado ...
		if ( $action['action']=='change_state' and $_GET['tar_id'])
		{
			$tar_id = $_GET['tar_id'];
			
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
			$query = 'UPDATE  tarefa SET  sit_id = ' . 2 . ' WHERE  tar_id =' . $tar_id
			or die ('Erro ao criar a consulta');
			
			// execulta query de inserção na tabela usuario_empresa
			$data = mysqli_query($dbc, $query)
				or die('Erro ao execultar a inserção na tabela usuario_empresa');
			
			/* Fecha conexão com o banco */
			mysqli_close($dbc);
		}
	}
?>
		