<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	if ( isset($_SESSION['usu_id']) and isset($_GET['pro_id']) and isset($_GET['tar_id']) and isset($_GET['res_id']) )
	{
		$usu_id = $_SESSION['usu_id'];
		$usu_nome = $_SESSION['usu_nome'];
		$pro_id = $_GET['pro_id'];
		$tar_id = $_GET['tar_id'];
		$permissao = $_GET['tip_id'];
		$res_id = $_GET['res_id'];
		$action = &$_REQUEST;		
		
		// Quando o usuário submeter os dados de atualização da tarefa
		if ( $action['action']=='atualizar' ) 
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
			$tar_data_conclusao = trim ($_POST['data_fim']);
			$usu_id_reponsavel = trim($_POST['responsavel']);
	
			// criando query de inserção na tabela projeto
			$query = "UPDATE `tarefa` SET `tip_t_id`='$tip_tarefa',`pri_id`='$prioridade',`pro_id`='$pro_id',`tar_titulo`='$tar_titulo',
					`tar_descricao`='$tar_descricao',`tar_comentario`='$tar_comentario',`tar_data_conclusao`='$tar_data_conclusao' 
					WHERE `tar_id` = '$tar_id'"
			or die ('Erro ao contruir a consulta');
			
			//execulta query de atualização na tabela tarefa
			$data = mysqli_query($dbc, $query)
				or die('Erro ao execultar a inserção na tabela projeto');
			
			$query = "UPDATE `responsavel` SET `tar_id`='$tar_id',`usu_id`= '$usu_id_reponsavel' WHERE `res_id` = '$res_id'" 
				or die('Erro ao contruir a consulta');

			echo $query;

			//execulta query de atualização na tabela tarefa
			$data = mysqli_query($dbc, $query)
				or die('Erro ao execultar a inserção na tabela responsavel');
			
			/* Fecha conexão com o banco */
			mysqli_close($dbc);
		}
		
		// Quando o usuário submeter os dados de atualização da tarefa
		if ( $action['action']=='delete' ) 
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
			mysqli_select_db($dbc, "easykanban-bd")
				or die ('Erro ao selecionar o Banco de Dados');

			
			/* Fecha conexão com o banco */
			mysqli_close($dbc);
			
			header("Location: config_projeto.php?pro_id=" . $pro_id );
			
		}
		
		header("Location: editar_tarefas.php?pro_id=" . $pro_id . "&tar_id=" . $tar_id . "&tip_id=" . $permissao );
	}
?>

<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Documento sem t&iacute;tulo</title>
</head>

<body>
</body>
</html>