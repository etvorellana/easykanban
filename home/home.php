<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	$usu_id =  $_SESSION['usu_id'];
	
	// se a sessão for válida
	if (isset($_SESSION['usu_id'])) {
		// conectar ao banco de dados
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
		
		
		// consulta que retorna todos os dados do usuário logado no sistema
		$query = "SELECT usu_id, tip_id, usu_nome, usu_email, usu_senha, usu_foto " . 
				 "FROM usuario " .
				 "WHERE usu_id = " . $usu_id 
				 or die ('Erro ao construir a query');
		
		// executa consulta
		$data = mysqli_query($dbc, $query);
		$row = mysqli_num_rows($data);
		
		// verifica se foi retornado apenas um registro do banco
		if ( $row == 1) 
		{
			// captura os dadas deste registro
			$row = mysqli_fetch_array($data);
			
			if ( $row != NULL ) 
			{
				// recupera os dados
				$usu_nome = $row['usu_nome'];
				$usu_tipo = $row['tip_id'];
				$usu_email = $row['usu_email'];
			}
		}
	
		/* Fecha conexão com o banco */
		mysqli_close($dbc);
	}
?>

<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <title>easykanban</title>
    <link rel="stylesheet" type="text/css" href="../css/main.css" />
    <link rel="stylesheet" type="text/css" href="home.css" />
  </head> 

  <body>
	<div id="container-cabecalho">
    <header>
    
		<ul>
        <li> <a class="menu_acesso_rapido" href="#"> <?php echo ( $_SESSION['usu_nome']) ?> </a> </li>
    	</ul>
    	
        <div id="acessiobilidade">
        	<label > <a class="menu_acesso_rapido" href="../logout.php"> logout </a> </label>
        </div>
        
    </header>
    </div>
    
	<div id="container-menu">
        <ul>
        <li><a href="#">Home</a></li>
        <li><a href="../empresa/empresa.php">Empresas</a></li>
        <li><a href="#">Relatórios</a></li>
        <li><a href="#">Configurações</a></li>
        </ul>
        <br style="clear:left"/>
    </div>
    
    <div id="main">
        <div id="menu_perfil">
            <table>
            <tr>
            <td rowspan="5">
            
            <?php
            if (empty($row['usu_foto'])) {
                echo '<img src="../nopic.jpg" alt="Profile Picture" />';
            }
            ?>
            </td>
            </tr>
            
            <tr> <td> <strong class="nome_titulo"> <?php echo( $usu_nome ); ?> </strong> </td> </tr>
            <tr> <td> <strong> Tipo do Usuário: </strong> <?php echo( $usu_tipo ); ?> </td> </tr>
            <tr> <td> <strong> E-mail: </strong> <?php echo( $usu_email ); ?>  </td> </tr>
            
            <tr> <td> <button class="botao"> Editar Perfil </button> </td> </tr>

            </table>
            
            
            
        </div>
    
        <div id="container_projetos" class="info" >
            <strong class="label_titulo" > Meus Projetos </strong> 
        </div>
        
        <div id="container_tarefas" class="info">   
            <strong class="label_titulo" > Minhas Tarefas </strong>	
        </div>
    </div>

    <div id="footer">
      &copy; 2012, easykanban <br/>
      Contato: <strong> eakykanban@gmail.com </strong>
      <br />
      Todas as marcas e marcas registradas que aparecem neste site pertencem a seus respectivos proprietários.
    </div>
    
  </body>
</html>




