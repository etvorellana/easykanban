<?php
	
    header( 'Content-Type: text/html; charset=ISO-8859-1' );
	
	function get_tarefas_from_project( $parametro_pro_id, $parametro_sit_id )
	{
		if (isset($_SESSION['usu_id']) and isset($_GET['pro_id']))  
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
		
			$query = 'SELECT t.tar_id, t.tar_titulo
					  FROM tarefa t
					  JOIN projeto p ON p.pro_id = t.pro_id
					  JOIN situacao s ON s.sit_id = t.sit_id
					  WHERE t.pro_id = %s AND s.sit_id=%s'
			or die ("Erro ao construir a consulta");
					
			// alimenta os parametros da conculta
			$query = sprintf($query, $parametro_pro_id, $parametro_sit_id); 			
					
			//executa query de consulta na tabela tarefa
			$result = mysqli_query($dbc, $query)
				or die('Erro ao executar a inserção na tabela tarefa');

			mysqli_close($dbc);
			
			return $result;
		}
	} //fim função get_tarefas


	function get_user_from_project( $parametro_pro_id )
	{
		// conectar ao banco de dados
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
		die('Erro ao conectar ao BD!');
		
		// selecioma todos os usuários logados ao projeto selecionado
		$query = 'SELECT u.usu_id, u.usu_nome
				  FROM usuario u 
				  JOIN usuario_projeto_tipo up on up.usu_id = u.usu_id 
				  JOIN projeto p on p.pro_id = up.pro_id
				  WHERE p.pro_id=%s'
		or die ("Erro ao construir a consulta");
		
		// alimenta os parametros da conculta
		$query = sprintf($query, $parametro_pro_id);	
				
		//executa query de inserção na tabela cep
		$data = mysqli_query($dbc, $query)
			or die('Erro ao execultar a inserção na tabela projeto');
		
		while ($row = mysqli_fetch_array($data)) {
			echo '<option value="' , $row['usu_id'] , '"> ' , $row['usu_nome'] , '</option>';
		}
		
		// fecha conexão com bd
		mysqli_close($dbc);	
	} // fim função get_user_from_project
	
	
	
	function get_limite_tarefas_por_coluna ( $project_id )
	{
		// conectar ao banco de dados
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
		die('Erro ao conectar ao BD!');
		
		// selecioma todos os usuários logados ao projeto selecionado
		$query = 'SELECT s.sit_id, l.lin_limite
				  FROM limite_tarefa l
				  JOIN projeto p on p.pro_id = l.pro_id
				  JOIN situacao s on s.sit_id = l.sit_id
				  WHERE p.pro_id =%s'
						or die ("Erro ao construir a consulta");
		
		// alimenta os parametros da conculta
		$query = sprintf($query, $project_id );	
				
		//executa query de inserção na tabela cep
		$data = mysqli_query($dbc, $query)
			or die('Erro ao execultar a inserção na tabela projeto');
		
		// fecha conexão com bd
		mysqli_close($dbc);		
		
		return $data;	
	}
?>