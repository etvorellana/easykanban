<?php
	include_once('../connect/connect_vars.php');

	$action = &$_REQUEST;
	$pro_id = $_GET['pro_id'];
	
	// se o status da tarefa foi modificado ...
	if ( $action['action']=='change_state' and $_GET['tar_id'] and $_GET['sit_id'])
	{
		$tar_id = $_GET['tar_id'];
		$sit_id = $_GET['sit_id'];
		
		// conectar ao banco de dados
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
		die('Erro ao conectar ao BD!');
		
		$query = 'UPDATE tarefa SET sit_id=' . $sit_id . ' WHERE tar_id =' . $tar_id
		or die ('Erro ao criar a consulta');
		
		// execulta query de inserção na tabela tarefa
		$data = mysqli_query($dbc, $query)
			or die('Erro ao executar a inserção na tabela tarefa');
		
		/* Fecha conexão com o banco */
		mysqli_close($dbc);
	}
	
	// volta para o quadro kanban
	$voltar_url = 'quadro.php?pro_id=' . $pro_id;
	header('Location: ' . $voltar_url );
?>
		