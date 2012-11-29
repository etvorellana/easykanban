<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');

	// se a sessão for válida
	if (isset($_SESSION['usu_id'])) 
	{
		$usu_id =  $_SESSION['usu_id'];
		
		// conectar ao banco de dados
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
		
		
		// consulta que retorna todos os dados do usuário logado no sistema
		$query = 'SELECT u.usu_id, u.usu_nome, u.usu_email, u.usu_senha, u.usu_dt_cadastro, u.usu_dt_cadastro, u.usu_foto
				  FROM usuario u
				  WHERE usu_id =%s'
				     or die ('Erro ao construir a query');
		
		// alimenta os parametros da conculta
		$query = sprintf($query, $usu_id ); 	
		
		// executa consulta
		$data = mysqli_query($dbc, $query);
		
		// captura os dadas deste registro
		$dados_usuario = mysqli_fetch_array($data);
		
		// recupera os dados
		$usu_nome = $dados_usuario['usu_nome'];
		$usu_email = $dados_usuario['usu_email'];
	
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
			
			if ( !empty($nome) && !empty($email) && !empty($senha))
			{
				$query = "INSERT INTO usuario ( usu_nome, usu_email, usu_senha, usu_dt_cadastro ) VALUES ( '$nome', '$email', SHA('$senha'), CURRENT_TIMESTAMP() )" or 
					die ('Erro ao contruir a consulta');
				
				$result = mysqli_query($dbc, $query)
					or die('Erro ao execultar a consulta');
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
    
    
    <link rel="stylesheet" type="text/css" media="all" href="../css/formulario.css">
    <link rel="stylesheet" type="text/css" media="all" href="../fancybox/jquery.fancybox.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.0.6"></script>
    
    
    <link rel="stylesheet" type="text/css" href="../css/main.css" />
    <link rel="stylesheet" type="text/css" href="home.css" />

  
  </head> 

  <body>
  	<div id="corpo">
    
	<div id="container-cabecalho">
    <header>
    
		<div id="nome_usuario" class="menu_acesso_rapido">
        	<label> <?php echo ( $_SESSION['usu_nome']) ?> </label>
    	</div>
    	
        <div id="logout" class="config_logout">
        	<label > <a class="menu_acesso_rapido" href="../logout.php"> logout </a> </label>
        </div>
    </header>
    </div>
    
	<div id="container-menu">
        <ul>
            <li class="atual" ><a href="home.php">Home</a></li>
            <li><a href="../projetos/projeto.php">Projetos</a></li>
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
            <tr> <td> <br> </td> </tr>
            
            <tr> <td> <strong class="nome_titulo"> <?php echo( $usu_nome ); ?> </strong> </td> </tr>
            <tr> <td> <strong> E-mail: </strong> <?php echo( $usu_email ); ?>  </td> </tr>
            <tr> <td> <br> </td> </tr>
            </table>
            
            <div id="edit_user">
				<a  id="botao_editar" class="modalbox" href="#edit_inline" class="gray_button">Editar Perfil</a>
            </div>
            
        </div>
    
        <div id="container_projetos" class="info" >
            <strong class="label_titulo" > Meus Projetos </strong>
            
            <div class="barra_de_rolagem">
            <?php
                // conecta ao banco de dados
                $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
                    die('Erro ao conectar ao BD!');

                $query = 'SELECT p.`pro_id`, ts.`tip_situacao`, p.`pro_nome`, p.`pro_descricao`, p.`pro_dt_inicio`, p.`pro_dt_fim`, p.`pro_usu_criador`, up.`tip_id`
					      FROM `projeto` p
						  JOIN `usuario_projeto_tipo` up on up.`pro_id` = p.`pro_id`
						  JOIN `usuario` u on u.`usu_id` = up.`usu_id` 
						  JOIN `tipo_situacao` ts on ts.`tip_id` = p.`tip_id`
						  WHERE u.`usu_id` =%s AND ts.`tip_id` = 1 
						  ORDER BY (p.pro_dt_fim)'
                          or die ('Erro ao contruir a consulta');
                
				// alimenta os parametros da consulta
				$query = sprintf($query, $usu_id ); 	
				
                // executa consulta
                $data = mysqli_query($dbc, $query) or die ('Erro ao execultar consulta');
                //*$row = mysqli_num_rows($data); */
            
                while ( $row = mysqli_fetch_array($data) ) 
                {
                    echo '<div class="projeto_info" id="sumario_projetos">';
                    echo '<table width="100%" class="border_space">';
						echo '<tr> <td> <strong> Nome: </strong> <strong >'; echo( $row['pro_nome'] ); echo '</strong> </td> </tr>';
						echo '<tr> <td> <strong> Descrição: </strong>';     echo( $row['pro_descricao'] ); echo '</td> </tr>';
						echo '<tr> <td> <strong> Situação: </strong>';     echo( $row['tip_situacao'] ); echo '</td> </tr>';
						echo '<tr>';
                 		echo '<tr> <td> <a href="../quadro_kanban/quadro.php?pro_id=' , $row['pro_id'] , ' &tip_id=' , $row['tip_id'] , '" class="gray_button">Entrar</a> <tr> </td>';
						echo '</tr>';
                    echo '</table>';
                    echo '</div>';
                }
				
				// fecha conexão com o bd
                mysqli_close($dbc);
        ?>
        </div>
            
        </div>

        <div id="container_tarefas" class="info">   
            <strong class="label_titulo" > Minhas Tarefas </strong>	
            
       		<div class="barra_de_rolagem">
			<?php

                // conecta ao banco de dados
                $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
                    die('Erro ao conectar ao BD!');
					
                // seleciona a quantidade de projetos ligados ao usuario que está logado   
				$query = 'SELECT p.pro_id, p.pro_nome 
						  FROM projeto p
						  JOIN usuario_projeto_tipo up on up.pro_id = p.pro_id
						  JOIN usuario u on u.usu_id = up.usu_id
						  WHERE u.usu_id=%s'
						  or die ('Erro ao construir a consulta');
					
				// alimenta os parametros da conculta
				$query = sprintf($query, $usu_id ); 	
						 
                // executa consulta
                $dados_consulta = mysqli_query($dbc, $query) or die ('Erro ao executar consulta');			
				
				while ( $linha = mysqli_fetch_array($dados_consulta) ) 
				{
					$query = 'SELECT p.pro_id, t.tar_id, m.met_descricao, s.sit_descricao, p.pro_nome, t.tar_titulo, t.tar_descricao, t.tar_comentario, t.tar_data_inicio, t.tar_data_conclusao 
							 FROM tarefa t 
							 JOIN situacao s on s.sit_id = t.sit_id 
							 JOIN projeto p on p.pro_id = t.pro_id 
							 JOIN usuario_projeto_tipo up on up.pro_id = p.pro_id 
							 JOIN usuario u on u.usu_id = up.usu_id 
							 LEFT JOIN meta m on m.met_id = t.met_id 
							 WHERE u.usu_id =%s and p.pro_id=%s 
							 ORDER BY (tar_data_conclusao)'
									 or die ('Erro ao construir a consulta');
									 
					// alimenta os parametros da conculta
					$query = sprintf($query, $usu_id, $linha['pro_id'] ); 	
				
					// executa consulta
					$data = mysqli_query($dbc, $query) or die ('Erro ao execultar consulta');
					
					if (  $row = mysqli_num_rows($data) ) 
					{
						echo '<div id="sumario_tarefas">';
						
						echo '<fieldset class="tarefas_agrupadas">';
						echo '<legend>' . $linha['pro_nome'] . ' </legend>';
						echo '<table width="100%" class="border_space">';
						
						while ( $row = mysqli_fetch_array($data) ) 
						{
							echo '<tr> <td> <strong> Tarefa: </strong> <strong >'; echo( $row['tar_titulo'] ); echo '</strong> </td> </tr>';
							echo '<tr> <td> <strong> Situação: </strong>';     echo( $row['sit_descricao'] ); echo '</td> </tr>';
							echo '<tr> <td> <strong> Conclusão: </strong>';     echo( $row['tar_data_conclusao'] ); echo '</td> </tr>';
							echo '<tr> <td> <br> <tr> </td>';
						}
						
						echo '</table>';
						echo '</fieldset>';
						echo '</div>';
					
					}
				}
                mysqli_close($dbc);
       		?>
        	</div>   
        </div>
        
    </div>
    
    </div>
    
	<!-- Invisivel inline form -->
    <!-- Editar Usuário -->
	<div id="edit_inline">
    
    <h2> Editar Usuário </h2><br />
    <form id="editar_usuario_formulario" name="editar_usuario_formulario" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
    <table class="border_space" >
    <tr>
        <td>
            <table class="border_space" >
                <tr> <td>  <label for="nome" class="negrito">Nome completo:</label> </td> </tr>
                <tr> <td>  <input type="text" id="nome" name="nome" value="<?php echo $dados_usuario['usu_nome'] ?>" required>  </td> </tr>
            </table>
        </td>
    </tr>
    
    <tr>
        <td>
            <table class="border_space">
                <tr>
                    <td>  <label class="negrito" >Endereço de e-mail:</label> </td>
                    <td>  <label class="negrito" >Confirmar e-mail:</label> </td>
                </tr>
                <tr>
                    <td> <input type="email" id="email" name="email" value="<?php echo $dados_usuario['usu_email'] ?>" required> </td>
                    <td> <input type="email" id="confirmar_email" name="confirmar_email"  value="<?php echo $dados_usuario['usu_email'] ?>" required oninput="check_email(this)"> </td>
                </tr>
            </table>
       </td>
    </tr>
    
    <tr>
        <td>
            <table class="border_space">
                <tr>
                    <td> <label class="negrito" >Senha:</label> </td>
                    <td> <label class="negrito" >Confirmar Senha:</label> </td>
                </tr>
                <tr>
                    <td> <input type="password" id="senha" name="senha" value="<?php echo $dados_usuario['usu_nome'] ?>" required> </td>
                    <td> <input type="password" id="confirmar_senha" name="confirmar_senha" value="<?php echo $dados_usuario['usu_nome'] ?>" onChange="return validarSenha();" required> </td>
                </tr>
            </table>
       </td>
    </tr>
    
    <tr>
        <td>
            <table class="border_space">
                <tr>
                    <tr>
                    <td> <input class="blue_button" type="submit" value="Editar" name="send" id="send" /> </td>
                    </tr>
                </tr>
            </table>
       </td>
    </tr>
       
    </table>
    </form>
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
                            <td>  <label class="negrito" >Confirmar e-mail:</label> </td>
                        </tr>
                        <tr>
                            <td> <input type="email" id="email" name="email" placeholder="Ex: thalles@easykanban.com" required> </td>
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
                            <td> <label class="negrito" >Confirmar Senha:</label> </td>
                        </tr>
                        <tr>
                            <td> <input type="password" id="senha" name="senha" placeholder="" required> </td>
                            <td> <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="" onChange="return validarSenha();" required> </td>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="border_space">
                        <tr>
                            <tr>
                            <td> <input class="blue_button" type="submit" value="Cadastrar" name="send" id="send" /> </td>
                            </tr>
                        </tr>
                    </table>
               </td>
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
	
	function validarSenha(){

		document.getElementById("#senha").innerHTML='Os campos de não podem ser diferente';
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
		
		$("#editar_usuario_formulario").submit(function() {  // quando os dados forem submetidos...
			$("#editar_usuario_formulario").fadeOut("slow", function(){
				$(this).before("<p><strong>Usuário cadastrado com Sucesso!</strong></p>"); // exibe mensagem de confirmação para o usuário
				setTimeout("$.fancybox.close()", 1000); // fecha caixa de dialogo
			});
		});

	});
</script>

</body>
</html>

<?php
	}
	else
	{
		$index_url = '../index.php';
		header('Location: ' . $index_url);
	}
	
?>




