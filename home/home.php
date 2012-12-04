<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');

	// se a sessão for válida
	if (isset($_SESSION['usu_id'])) 
	{
		$usu_id =  $_SESSION['usu_id'];
		
		// ação padrão para o quadro de kanban
		$default_action = 'mostrar_todas_as_tarefas';
		
		// conectar ao banco de dados
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
		
		
		// consulta que retorna todos os dados do usuário logado no sistema
		$query = 'SELECT `usu_id`, `usu_nickname`, `usu_nome`, `usu_email`, `usu_senha`, `usu_dt_cadastro`, `usu_foto` FROM `usuario` WHERE `usu_id` = %s'
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
		$usu_nickname = $dados_usuario['usu_nickname'];
	
		/* Fecha conexão com o banco */
		mysqli_close($dbc);
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
            <?php if( isset($_SESSION['tip_id']) == MASTER ) echo '<li><a href="../usuario/config_usuario.php">Usuários</a></li>'; ?>
        </ul>
        
        <?php
        if ( isset($_SESSION['tip_id']) == MASTER  ) {
			echo '<div id="nova-tarefa" >
				<a id="bug" class="modalbox" href="#inline"> 
					<input class="orange_button" type="submit" value=" + Novo Usuário " > 
				</a>
			</div>';
		}
		?>
        
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
			<tr> <td> <strong> Nickname: </strong> <?php echo( $usu_nickname ); ?>  </td> </tr>
            <tr> <td> <strong> E-mail: </strong> <?php echo( $usu_email ); ?>  </td> </tr>
            <tr> <td> <br> </td> </tr>
            </table>
            
            <div id="edit_user">
				<a  id="botao_editar" class="modalbox" href="#edit_inline" class="gray_button">Editar Perfil</a>
            </div>
            
        </div>
    
        <div id="container_projetos" class="info" >
            <p class="label_titulo" > Meus Projetos </p>
            
            <div class="barra_de_rolagem">
            <?php
                // conecta ao banco de dados
                $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
                    die('Erro ao conectar ao BD!');
				
				// selecina os projetos do usuario logado no sistema, que estão com a situação = 1 (EM ANDAMENTO)
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
				
				// recupera o número de projetos retornados
				$numero_de_projetos = mysqli_num_rows($data);
					
                while ( $row = mysqli_fetch_array($data) ) 
                {
                    echo '<div class="projeto_info" id="sumario_projetos">
                          <table width="100%" class="border_space">';
						echo '<tr> <td> <strong> Nome: </strong> <strong >'; echo( substr($row['pro_nome'], 0, 40 ) ); echo '</strong> </td> </tr>';
						echo '<tr> <td> <strong> Descrição: </strong>';     echo( substr($row['pro_descricao'], 0, 100 ) ); echo '</td> </tr>';
						
						echo '<tr> <td> <strong> Situação: </strong>';     echo( $row['tip_situacao'] ); echo '</td> </tr>';
						echo '<tr>';
                 		echo '<tr> <td> <a href="../quadro_kanban/quadro_kanban.php?pro_id=' , $row['pro_id'] , ' &tip_id=' , $row['tip_id'] , '&action=', $default_action, '" class="gray_button">Entrar</a> <tr> </td>';
						echo '</tr>';
                    echo '</table>';
                    echo '</div>';
                }
				
				if ( $numero_de_projetos == 0 )
					echo '<p class="fim_da_lista"> Você não está ligado a nenhum projeto </p>';
            	else
					echo '<p class="fim_da_lista"> Não existem mais projetos cadastradas </p>';
				
				// fecha conexão com o bd
                mysqli_close($dbc);
        ?>
        </div>
            
        </div>

        <div id="container_tarefas" class="info">   
            <p class="label_titulo" > Minhas Tarefas </p>	
            
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
    <form id="editar_usuario_formulario" name="editar_usuario_formulario" method="post" action="../usuario/inserir_remover_usuario.php?action=atualizar" >
    <table class="border_space" >
    <tr>
        <td>
            <table class="border_space" >
                <tr> <td>  <label for="nome" class="negrito">Nome completo:</label> </td> </tr>
                <tr> <td>  <input type="text" id="nome" name="nome" value="<?php echo $dados_usuario['usu_nome'] ?>" required>  </td> </tr>
                
                <tr> <td>  <label for="nickname" class="negrito">Nickname:</label> </td> </tr>
                <tr> <td>  <input type="text" maxlength="10" id="nickname" name="nickname" value="<?php echo $dados_usuario['usu_nickname'] ?>" required>  </td> </tr>
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
                    <td> <input type="email" id="confirmar_email" name="confirmar_email"  value="<?php echo $dados_usuario['usu_email'] ?>" required oninput="checkEditEmail(this)"> </td>
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
                    <td> <input type="password" id="senha" name="senha" placeholder="Digite sua nova senha" required> </td>
                    <td> <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Digite sua nova senha" oninput="checkEditSenha(this)" required> </td>
                </tr>
            </table>
       </td>
    </tr>
    
    <tr>
        <td>
            <table class="border_space">
                <tr>
                    <tr>
                    <td> <input class="blue_button" type="submit" value="Salvar" name="editar_usuario" id="editar_usuario" /> </td>
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
	<form id="contact" name="contact" method="post" action="../usuario/inserir_remover_usuario.php?action=inserir" >
         <table class="border_space" >
            <tr>
            	<td>
                	<table class="border_space" >
                    	<tr> <td>  <label for="nome" class="negrito">Nome completo:</label> </td> </tr>
                    	<tr> <td>  <input type="text" id="nome" name="nome" placeholder="Ex: Thalles Santos Silva" required>  </td> </tr>
						
                    	<tr> <td>  <label for="nickname" class="negrito">Nickname:</label> </td> </tr>
                    	<tr> <td>  <input type="text" maxlength="10" id="nickname" name="nickname" placeholder="Ex: thalles" required>  </td> </tr>
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
                            <td> <input type="email" id="insert_email" name="insert_email" placeholder="Ex: thalles@easykanban.com" required> </td>
                            <td> <input type="email" id="confirmar_email" name="confirmar_email" required oninput="checkEmail(this)"> </td>
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
                            <td> <input type="password" id="insert_senha" name="insert_senha" placeholder="" required> </td>
                            <td> <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="" oninput="checkSenha(this)" required> </td>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="border_space">
                        <tr>
                            <tr>
                            <td> <input class="blue_button" type="submit" value="Cadastrar" name="inserir_usuario" id="inserir_usuario" /> </td>
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

	function checkEmail(input) {
		if (input.value != document.getElementById('insert_email').value) {
			input.setCustomValidity('Os endereços de email não correspondem.');
		} else {
			input.setCustomValidity('');
		}
	}


	function checkSenha(input) {
	  if (input.value != document.getElementById('insert_senha').value) {
		input.setCustomValidity('As senhas digitadas não correspondem.');
	  } else {
		input.setCustomValidity('');
	  }
	}
	
	function checkEditEmail(input) {
		if (input.value != document.getElementById('email').value) {
			input.setCustomValidity('Os endereços de email não correspondem.');
		} else {
			input.setCustomValidity('');
		}
	}


	function checkEditSenha(input) {
	  if (input.value != document.getElementById('senha').value) {
		input.setCustomValidity('As senhas digitadas não correspondem.');
	  } else {
		input.setCustomValidity('');
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




