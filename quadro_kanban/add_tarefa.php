<?php
	include_once('../connect/connect_vars.php');

	if ( isset($_GET['pro_id']) ) 
	{
		$pro_id = $_GET['pro_id'];
		$permissao = $_GET['tip_id'];
		$action = &$_REQUEST;

		if (isset($_POST['send'])) 
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
			mysqli_select_db($dbc, "easykanban-bd")
				or die ('Erro ao selecionar o Banco de Dados');
			
			// recupera os dados digitados no formulário
			$tip_tarefa = trim($_POST['tip_tarefa']);
			$prioridade = trim($_POST['prioridade']);
			$tar_titulo = trim($_POST['titulo']);
			$tar_descricao = trim($_POST['descricao']);
			$tar_comentario = trim ($_POST['comentario']);	
			$tar_data_inicio = trim ($_POST['data_inicio']);
			$tar_data_conclusao = trim ($_POST['data_fim']);
			$tar_tempo_estimado = trim($_POST['tempo_estimado']);
			
			$usu_id_reponsavel = trim($_POST['tipo_situacao']);
			
			if ( !empty($tar_titulo) )
			{

				// criando query de inserção na tabela tarefa
				$query = "INSERT INTO `tarefa` ( `tip_t_id`, `pri_id`, `met_id`, `sit_id`, `pro_id`, `tar_titulo`, `tar_descricao`, `tar_comentario`, `tar_data_inicio`, `tar_data_conclusao`, `tar_tempo_estimado`, `tar_data_criacao`) VALUES ( '$tip_tarefa', '$prioridade', NULL, '1', '$pro_id', '$tar_titulo', '$tar_descricao', '$tar_comentario', '$tar_data_inicio', '$tar_data_conclusao', NULL, CURRENT_TIMESTAMP() );"
				or die ('Erro ao contruir a consulta' . mysqli_error($dbc) );
				
				echo $query;
				
				//execulta query de inserção na tabela tarefa
				$data = mysqli_query($dbc, $query)
					or die('Erro ao executar a inserção na tabela tarefa' );
					
				// recupera o id da terefa inserida e insere na tabela responsável
				$ultimo_tar_id = mysqli_insert_id($dbc);
				
				$query = "INSERT INTO `responsavel`(  `tar_id`, `usu_id`) VALUES ( '$ultimo_tar_id', '$usu_id_reponsavel' )"
				or die (  mysqli_error($dbc) );
				
				//execulta query de inserção na responsável
				$data = mysqli_query($dbc, $query)
					or die( mysqli_error($dbc) );
			}
			
			/* Fecha conexão com o banco */
			mysqli_close($dbc);
		}
	}
	
	// volta para o quadro kanban
	$voltar_url = 'quadro_kanban.php?pro_id=' . $pro_id . '&tip_id=' . $permissao . '&action=' . $action['action'] ;
	header('Location: ' . $voltar_url ) xor die;
		
?>
