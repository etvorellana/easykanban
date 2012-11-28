<?php
	// Quando o usuário submeter os dados de cadastro de nova empresa
	if (isset($_POST['edit']) and $_GET['pro_id'] ) 
	{
		$pro_id = $_GET['pro_id'];
		
		// conectar ao banco de dados
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
		die('Erro ao conectar ao BD!');
		
		// recupera os dados digitados no formulário
		$pro_nome = trim ($_POST['nome']);	
		$pro_descrição = trim($_POST['descricao']);
		$data_fim = trim($_POST['data_fim']);
		$tip_situacao = trim ($_POST['tipo_situacao']);	

		if ( !empty($nome) && !empty($pro_descrição) && !empty($data_fim) && !empty($tip_situacao) )
		{
			$por_usu_criador = $usu_id;
			
			// criando query de inserção na tabela projeto
			$query = 'UPDATE projeto SET
					  tip_id = %s
					  pro_nome = %s
					  pro_descricao= %s 
					  pro_dt_fim = %s
					  WHERE pro_id = %s'
			or die ('Erro ao contruir a consulta');
			
			// alimenta os parametros da conculta
			$query = sprintf($query, $tip_situacao, $pro_nome, $pro_descrição, $data_fim, $pro_id );	
		
			echo $query;
			
			//execulta query de inserção na tabela cep
			$data = mysqli_query($dbc, $query)
				or die('Erro ao execultar a inserção na tabela projeto');
			
		}
			
		/* Fecha conexão com o banco */
		mysqli_close($dbc);
	}

?>