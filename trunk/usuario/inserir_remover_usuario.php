<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	if ( isset($_SESSION['usu_id']) )
	{
		$action = &$_REQUEST;		
		
		if ( $action['action']=='inserir' and isset($_POST['inserir_usuario']) )
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
			$nome = trim ($_POST['nome']);	
			$nickname = trim($_POST['nickname']);
			$email = trim($_POST['insert_email']);
			$senha = trim($_POST['insert_senha']);
			
			if ( !empty($nome) && !empty($email) && !empty($senha) && !empty($nickname) )
			{
				$query = "INSERT INTO `usuario` ( `usu_nickname`, `usu_nome`, `usu_email`, `usu_senha`, `usu_dt_cadastro` ) 
				VALUES ( '$nickname', '$nome', '$email', SHA('$senha'), CURRENT_TIMESTAMP() )" or 
					die ('Erro ao contruir a consulta');
				
				$result = mysqli_query($dbc, $query)
					or die('Erro ao execultar a consulta');
			}
				
			/* Fecha conexão com o banco */
			mysqli_close($dbc);
			
			$index_url = '../home/home.php';
			header('Location: ' . $index_url);
		}
		
		
		if ( $action['action']=='atualizar' and isset($_POST['editar_usuario']) )
		{
			$usu_id = $_SESSION['usu_id'];
	
			$usu_nome = $_POST['nome'];
			$usu_email = $_POST['email'];
			$usu_senha = sha1($_POST['senha']);
	
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
				die('Erro ao conectar ao BD!');
				
			mysqli_select_db($dbc, "easykanban-bd")
				or die ('Erro ao selecionar o Banco de Dados');
				
			$query = "UPDATE  `easykanban-bd`.`usuario` SET  
						`usu_nome` =  '$usu_nome',
						`usu_email` = '$usu_email',
						`usu_senha` = '$usu_senha'
						 WHERE  `usu_id` = '$usu_id'"
					 or die ('Erro ao contruir a consulta');                
	
			// executa consulta
			$data = mysqli_query($dbc, $query);
			
			$index_url = '../home/home.php';
			header('Location: ' . $index_url);
		}
		
		if ( $action['action']=='deletar_usuario' )
		{
			$remover_usu_id = $_GET['usu_id'];
			
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');

			$query = "DELETE from `usuario` WHERE usu_id = '$remover_usu_id'" or 
				die ('Erro ao contruir a consulta');
			
			$result = mysqli_query($dbc, $query)
				or die('Erro ao executar a consulta');
				
			/* Fecha conexão com o banco */
			mysqli_close($dbc);
			
			$index_url = '../usuario/config_usuario.php';
			header('Location: ' . $index_url);
		}
	}
?>

<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Editar Usuário</title>
</head>

<body>
</body>
</html>