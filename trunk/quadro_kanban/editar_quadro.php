<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	if ( isset($_GET['pro_id']) and isset($_GET['tip_id']) )
	{	
		$pro_id = $_GET['pro_id'];
		$permissao = $_GET['tip_id'];
		
		$array = array( '1' => $_POST['coluna1'], '2' => $_POST['coluna2'], '3' => $_POST['coluna3'], '4' => $_POST['coluna4'], '5' => $_POST['coluna5'] );

		// conectar ao banco de dados
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
		die('Erro ao conectar ao BD!');
		
		//seleciona o banco adequado
		mysqli_select_db($dbc, "easykanban-bd")
			or die ('Erro ao selecionar o Banco de Dados');


		// criando query de inserção na tabela projeto
		for ( $colunaID = 1; $colunaID <= 5; $colunaID++ ) {
			$nome = 'coluna' . $colunaID; 
			echo $nome;
			$query = "UPDATE  `limite_tarefa` SET  `lin_limite` =" . $array[$colunaID] . " WHERE  `limite_tarefa`.`lin_id` =" . $colunaID
			or die ('Erro ao contruir a consulta');

			//execulta query de atualização na tabela tarefa
			$data = mysqli_query($dbc, $query)
				or die('Erro ao executar a inserção na tabela projeto');
		}
		
		/* Fecha conexão com o banco */
		mysqli_close($dbc);
		
		header("Location: config_tarefas.php?pro_id=" . $pro_id . "&tip_id=" . $permissao );
	}
	else
		echo 'ERRORRR';
	
	
?>