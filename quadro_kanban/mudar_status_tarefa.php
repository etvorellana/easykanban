<?php
	include_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	$return = &$_REQUEST;
	$action = &$_REQUEST;
	$pro_id = $_GET['pro_id'];
	$permissao = $_GET['tip_id'];
	
	// se o status da tarefa foi modificado ...
	if ( $action['action']=='change_state' and $_GET['tar_id'] and $_GET['sit_id'])
	{
		$tar_id = $_GET['tar_id'];
		$sit_id = $_GET['sit_id'];
		$usu_id = $_SESSION['usu_id'];
		$sit_descricao = '';
		
		switch($sit_id){
			case 1:
				$sit_descricao = 'Backlog';
				break;
			case 2:
				$sit_descricao = 'Requisitado';
				break;
			case 3:
				$sit_descricao = 'Em processo';
				break;
			case 4:
				$sit_descricao = 'Concluído';
				break;
			case 5:
				$sit_descricao = 'Arquivado';
				break;
		}
				
		// conectar ao banco de dados
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
		die('Erro ao conectar ao BD!');
		
		mysqli_select_db($dbc, "easykanban-bd")
			or die ('Erro ao selecionar o Banco de Dados');
		
		$query = 'UPDATE `tarefa` SET `sit_id`=' . $sit_id . ' WHERE `tar_id` =' . $tar_id
		or die ('Erro ao criar a consulta');
		
		// execulta query de inserção na tabela tarefa
		$data = mysqli_query($dbc, $query)
			or die('Erro ao executar a inserção na tabela tarefa');
		
		$trasicao = 'Transição';
		
		// insere na tabela acessos, a fim de guardar log das modificações
		$query = "INSERT INTO `acesso` (
				`ace_id` ,
				`usu_id` ,
				`ace_tabela` ,
				`ace_tipo` ,
				`ace_acao`,
				`tar_id`,
				`ace_tar_destino` ,
				`ace_data_hora` ,
				`pro_id` )
				VALUES ( NULL ,  '$usu_id',  'Tarefa',  '$trasicao',  'Update',  '$tar_id',  '$sit_descricao',  NOW(), NULL )"
		or die ('Erro ao criar a consulta');
		
		// execulta query de inserção na tabela acesso
		$data = mysqli_query($dbc, $query)
			or die('Erro ao executar a inserção na tabela acesso');
		
		/* Fecha conexão com o banco */
		mysqli_close($dbc);
	}
	
	if (  isset( $return['return'] ) && isset($_GET['selected_id']) ){
		$voltar_url = 'quadro_kanban.php?pro_id=' . $pro_id . '&tip_id=' . $permissao . '&action=' . $return['return'] . '&selected_id=' . $_GET['selected_id'] ; 
	}else
	if ( isset( $return['return'] ) ){
		// volta para o quadro kanban
		$voltar_url = 'quadro_kanban.php?pro_id=' . $pro_id . '&tip_id=' . $permissao . '&action=' . $return['return']; 
	}
	//else{
	// volta para o quadro kanban
	//	$voltar_url = 'quadro_kanban.php?pro_id=' . $pro_id . '&tip_id=' . $permissao; 
	//}
	
	//if(isset($_GET['usu_id_selecionado'])) 
	//	echo $voltar_url . '&usu_id_selecionado=' . $_GET['usu_id_selecionado'];
	
	echo 'fdsafadsfsdafsadfsdafsdfsdf';
	
	header('Location: ' . $voltar_url );
?>
		