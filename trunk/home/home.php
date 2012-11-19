<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	$usu_id =  $_SESSION['usu_id'];
	
	// se a sessão for válida
	if (isset($_SESSION['usu_id'])) 
	{
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
		
		// Quando o usuário submeter os dados de cadastro de novo usuario
		if (isset($_POST['send'])) 
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
			$nome = trim ($_POST['nome']);	
			$email = trim($_POST['email']);
			$senha = trim($_POST['senha']);
			$tipo = trim($_POST['tipo']);
			
			if ( !empty($nome) && !empty($email) && !empty($senha) && !empty($tipo) )
			{
				$query = "INSERT INTO usuario (tip_id, usu_nome, usu_email, usu_senha) VALUES ('$tipo', '$nome', '$email', SHA('$senha') )" or 
					die ('Erro ao contruir a consulta');
				
				$result = mysqli_query($dbc, $query)
					or die('Erro ao execultar a consulta');
			}
				
			/* Fecha conexão com o banco */
			mysqli_close($dbc);
		}
		
	}
	
?>

<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"><head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <title>easykanban</title>
    
    
    <link rel="stylesheet" type="text/css" media="all" href="../css/formulario.css">
    <link rel="stylesheet" type="text/css" media="all" href="../fancybox/jquery.fancybox.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.0.6"></script>
    
    
    <link rel="stylesheet" type="text/css" href="../css/main.css" />
    <link rel="stylesheet" type="text/css" href="home.css" />
  
  </head> 

  <body>
    
	<div id="container-cabecalho">
    <header>
    
		<div id="nome_usuario" class="menu_acesso_rapido">
        	<label> <?php echo ( $_SESSION['usu_nome']) ?> </label>
    	</div>
    	
        <div id="acessiobilidade" >
        	<label > <a class="menu_acesso_rapido" href="../logout.php"> logout </a> </label>
        </div>
    </header>
    </div>
    
	<div id="container-menu">
        <ul>
        <li><a href="#">Home</a></li>
        <li><a href="../empresa/company.php">Empresas</a></li>
        <li><a href="#">Relatórios</a></li>
        <li><a href="#">Configurações</a></li>
        </ul>
        
        <div id="nova-tarefa" >
                <a id="bug" class="modalbox" href="#inline"> 
                	<input class="orange_button" type="submit" value=" + Novo Usuário " > 
                </a>
        </div>
        
      <br style="clear:left"/>
    </div>
    
    <div id="main">
    
        <div id="menu_perfil">
            <table id="dados">
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
            
            <tr> <td> <a href="#" class="gray_button">Editar Perfil</a> </td> </tr>

            </table>
            
            
            
        </div>
    
        <div id="container_projetos" class="info" >
            <strong class="label_titulo" > Meus Projetos </strong> 
            
        </div>
        
        <div id="container_tarefas" class="info">   
            <strong class="label_titulo" > Minhas Tarefas </strong>	
        </div>
    </div>
    
    
	<!-- invisivel inline form -->
	<div id="inline">
	<h2> Adicionar Novo Usuário </h2><br />
    <div id="formulario">
	<form id="contact" name="contact" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
         <table class="border_space" >
         	
            <tr>
            	<td>
                	<table class="border_space" >
                    	<tr> <td>  <label for="nome" class="negrito">Nome completo:</label> </td> </tr>
                    	<tr> <td>  <input type="text" id="nome" name="nome" placeholder="Ex: Thalles Santos Silva" required>  </td> </tr>
                    </table>
                </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="border_space">
                        <tr>
                            <td>  <label class="negrito" >Endereço de e-mail:</label> </td>
                            <td>  <label </label> </td>
                            <td>  <label class="negrito" >Confirmar e-mail:</label> </td>
                        </tr>
                        <tr>
                            <td> <input type="email" id="email" name="email" placeholder="Ex: thalles@easykanban.com" required> </td>
                            <td>  <label </label> </td>
                            <td> <input type="email" id="confirmar_email" name="confirmar_email" required oninput="check_email(this)"> </td>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="border_space">
                        <tr>
                            <td> <label class="negrito" >Senha:</label> </td>
                            <td>  <label </label> </td>
                            <td> <label class="negrito" >Confirmar Senha:</label> </td>
                        </tr>
                        <tr>
                            <td> <input type="password" id="senha" name="senha" placeholder="" required> </td>
                            <td>  <label </label> </td>
                            <td> <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="" oninput="check_senha(this)" required> </td>
                        </tr>
                    </table>
               </td>
            </tr>
            
         	<tr> <td>  <label class="negrito" >Tipo:</label> </td> </tr>
            
            <tr>
            	<td>
                    <table class="border_space">
                        <tr>
                            <td> 
                           		Administrador: <input name="tipo" type="radio" value="1" required />
                            </td>
                           	
                            <td>
                            	Colaborador: <input name="tipo" type="radio" value="2" required />
                            </td>
                        </tr>
                    </table>
                    <br />	
               </td>
            </tr>
            
            <tr>
            <td> <input class="blue_button" type="submit" value="Cadastrar" name="send" id="send" /> </td>
            </tr>
            
         </table>
	</form>
    </div>
</div>

<!-- basic fancybox setup -->
<script type="text/javascript">

	function validateEmail(email) { 
		var reg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return reg.test(email);
	}
	
	function sleep(milliseconds) {
	  var start = new Date().getTime();
	  for (var i = 0; i < 1e7; i++) {
		if ((new Date().getTime() - start) > milliseconds){
		  break;
		}
	  }
	}

	$(document).ready(function() {
		$(".modalbox").fancybox(); //inicia a caixa de dialogo com o formulário
		
		// fazer validação javascript
		//
		//
		
		sleep( 1000 );					// espera 115 minutos para o usuário poder ler a mensagem na tela		
		$("#contact").submit(function() {  // quando os dados forem submetidos...
			$("#contact").fadeOut("slow", function(){
				$(this).before("<p><strong>Usuário cadastrado com Sucesso!</strong></p>"); // exibe mensagem de confirmação para o usuário
				setTimeout("$.fancybox.close()", 1000); // fecha caixa de dialogo
			});
		});

	});
</script>
    
  </body>
</html>




