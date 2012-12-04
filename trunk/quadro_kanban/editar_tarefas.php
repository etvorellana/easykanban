
<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	if ( isset($_SESSION['usu_id']) and isset($_GET['pro_id']) and $_GET['tar_id'] and isset($_GET['tip_id']) )
	{
		$usu_id = $_SESSION['usu_id'];
		$usu_nome = $_SESSION['usu_nome'];
		$pro_id = $_GET['pro_id'];
		$tar_id = $_GET['tar_id'];
		$permissao = $_GET['tip_id'];
		
		function pegar_usuario_por_projeto( $parametro_pro_id, $parametro_usu_id )
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
			// Seleciona o banco de dados
			mysqli_select_db($dbc, "easykanban-bd")
				or die ('Erro ao selecionar o Banco de Dados');
			
			// selecioma todos os usu�rios logados ao projeto selecionado
			$query = 'SELECT u.`usu_id`, u.`usu_nome`
					  FROM `usuario` u 
					  JOIN `usuario_projeto_tipo` up on up.`usu_id` = u.`usu_id` 
					  JOIN `projeto` p on p.`pro_id` = up.`pro_id`
					  WHERE p.`pro_id`= %s
					  AND NOT (u.`usu_id` = %s)'
			or die ("Erro ao construir a consulta");
			
			// alimenta os parametros da conculta
			$query = sprintf($query, $parametro_pro_id, $parametro_usu_id );	
					
			//executa query de inser��o na tabela cep
			$data = mysqli_query($dbc, $query)
				or die('Erro ao executar a inser��o na tabela projeto');
			
			while ($row = mysqli_fetch_array($data)) {
				echo '<option value="' , $row['usu_id'] , '"> ' , $row['usu_nome'] , '</option>';
			}
			
			// fecha conex�o com bd
			mysqli_close($dbc);	
		} // fim fun��o pegar_usuario_por_projeto
	}
?>

<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <title>Editar Tarefa</title>

    <link rel="stylesheet" type="text/css" media="all" href="../css/formulario.css">
    <link rel="stylesheet" type="text/css" media="all" href="../fancybox/jquery.fancybox.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.0.6"></script>
    
    <link rel="stylesheet" type="text/css" href="../css/main.css" />
    <link rel="stylesheet" type="text/css" href="../css/config_tarefas.css" />
    
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
    <script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
    <link rel="stylesheet" href="/resources/demos/style.css" />
	
    <script type="text/javascript" language="javascript">
		$(document).ready(function(){
			$('.fancybox').fancybox();
			
            $("#contact").submit(function() {  // quando os dados forem submetidos...
                $("#contact").fadeOut("slow", function(){
                    $(this).before("<p><strong>Tarefa modificada com Sucesso!</strong></p>"); // exibe mensagem de confirma��o para o usu�rio
                    setTimeout("$.fancybox.close()", 1000); // fecha caixa de dialogo
                });
            });
			
		});
	</script>
    
    <script>	
        $(function() {
            $("#data_fim").datepicker({ dateFormat: "yy-mm-dd" }).val()
        });
        $(function() {
            $( "#data_fim" ).datepicker();
        });
    </script>
    
	</head> 

<body>
	<div id="container-cabecalho">
    <header>
        <div id="nome_usuario" class="menu_acesso_rapido" >
            <a href="../home/home.php"> <?php echo ( $_SESSION['usu_nome'] ) ?> </a> / <?php if (isset($_GET['quadro_kanban']) ) echo '<a href="quadro_kanban.php?pro_id= ', $pro_id, '&tip_id=', $permissao, '"> Quadro Kanban </a>'; else echo '<a href="config_tarefas.php?pro_id= ', $pro_id, '&tip_id=', $permissao, '"> Tarefas </a> '; ?>
        </div>
    	
        <div id="logout" class="config_logout">
        	<label > <a class="menu_acesso_rapido" href="../logout.php"> logout </a> </label>
        </div>
    </header>
	</div>
    
</head>
<body>    
    <div id="main">
	<?php
        // conectar ao banco de dados
        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
            die('Erro ao conectar ao BD!');
			
		mysqli_select_db($dbc, "easykanban-bd")
			or die ('Erro ao selecionar o Banco de Dados');
			
        $query = 'SELECT t.`tar_id`, t.`tip_t_id` , t.`pri_id` , t.`met_id` , t.`sit_id` , t.`pro_id` , t.`tar_titulo` , t.`tar_descricao` , t.`tar_comentario` , t.`tar_data_inicio` , t.`tar_data_conclusao` , t.`tar_tempo_estimado` , t.`tar_data_criacao` , u.`usu_id`, u.`usu_nome` , s.`sit_descricao` , p.`pro_id`, p.`pro_nome`, r.`res_id`, pt.`pri_id`, pt.`pri_descricao`, tp.`tip_t_id`, tp.`tip_t_descricao`
				FROM  `tarefa` AS t
				JOIN  `projeto` p ON p.`pro_id` = t.`pro_id` 
				JOIN  `responsavel` r ON r.`tar_id` = t.`tar_id` 
				JOIN  `usuario` u ON u.`usu_id` = r.`usu_id` 
				JOIN  `situacao` s ON s.`sit_id` = t.`sit_id` 
				JOIN  `prioridade_tarefa` pt ON pt.`pri_id` = t.`pri_id`
				JOIN  `tipo_tarefa` tp ON tp.`tip_t_id` = t.`tip_t_id`
				WHERE p.`pro_id` =%s AND t.`tar_id`= %s'
                 or die ('Erro ao contruir a consulta');                
                 
        // alimenta os parametros da conculta
        $query = sprintf($query, $pro_id, $tar_id ); 	
        
        // executa consulta
        $data = mysqli_query($dbc, $query) or die ('Erro ao executar consulta');

        $row = mysqli_fetch_array($data);
		
		echo '<div id="menu_perfil">
			  <table width="100%" class="border_space">';
		
		echo '<tr> <td> <strong class="nome_titulo">', $row['tar_titulo'], '</strong> </td> </tr>
			  <tr> <td>  <strong> Descri��o: </strong>', $row['tar_descricao'], '</td> </tr>';
		  
		echo '<tr> <td width="60%"> <strong> In�cio da Tarefa: </strong>', $row['tar_data_inicio'], '</td>
			  <td> <strong> Previs�o de T�rmino:   </strong>', $row['tar_data_conclusao'], '</td> </tr>';
		
		echo '<tr> <td> <strong> Pertencente ao Projeto: </strong>', $row['pro_nome'], '</td>
		<td> <strong> Respons�vel: </strong>', $row['usu_nome'], '</td> </tr>';
		
		echo '<tr> 
			  <td> <strong> Situa��o: </strong>', $row['sit_descricao'], '</td>
			  <td> <strong> Prioridade: </strong>', $row['pri_descricao'], '</td>
			  </tr> 
		      
			  <tr> <td> <strong> Tipo: </strong>', $row['tip_t_descricao'], '</tr> </td>
			  <tr> <td> <strong> Data de Cria��o: </strong>', $row['tar_data_inicio'], '</tr> </td>
	
			  </table>
		
			  <div id="botoes_tarefas">
				<a id="botao_editar" class="fancybox" href="#inline" > Editar Tarefa </a>
			  </div>
		
		</div>';
        
		// fecha conex�o com o banco
        mysqli_close($dbc);
		
		
 		?>
 	</div>
    
        
	<!-- invisivel inline form -->
	<div id="inline" title="Editar Tarefa">
	<h2> Editar Tarefa </h2> <br />
	<form id="contact" name="contact" method="post" action="atualizar_deletar_tarefa.php<?php echo '?pro_id=', $pro_id, '&tip_id=', $permissao, '&tar_id=', $tar_id, '&res_id=', $row['res_id'], '&action=atualizar' ?>" >
		<table class="add_projeto" >
         	
            <tr>
                <td>
                    <table title="Titulo da Tarefa" class="add_projeto">
                        <tr> <td>  <label for="titulo" class="negrito">T&iacute;tulo:</label> </td> </tr>
                        <tr> <td>  <input type="text" maxlength="499" id="titulo" name="titulo" value="<?php echo $row['tar_titulo'] ?>" required>  </td> </tr>
                    </table>
                </td>
            </tr>
            
            <tr>
            	<td>
                    <table title="Descri��o, m�ximo 250 caracteres" class="add_projeto">
                        <tr>
                       	 	<tr> <td>  <label for="descricao" class="negrito">Descri&ccedil;&atilde;o:</label> </td> </tr>
                        	<tr> <td>  <textarea id="descricao" rows="3" maxlength="250" name="descricao" ><?php echo $row['tar_descricao'] ?></textarea> </td> </tr>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table title="Coment�rios adicionais, m�ximo 250 caracteres" class="add_projeto">
                        <tr>
                       	 	<tr> <td>  <label for="comentario" class="negrito">Coment&aacute;rio:</label> </td> </tr>
                        	<tr> <td>  <textarea id="comentario" rows="3" maxlength="250" name="comentario" ><?php echo $row['tar_comentario'] ?></textarea>  </td> </tr>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                            <td> <label class="negrito" >Inicio:</label> </td>
                            <td> <label class="negrito" >Conclus&atilde;o:</label> </td>
                            <td> <label class="negrito" >Prioridade:</label> </td>
                        </tr>
                        <tr>
                        	
                            <td title="Data de Cria��o da Tarefa"> 
                            <input disabled class="selector" type="text" id="data_inicio" name="data_inicio" value="<?php echo $row['tar_data_inicio'] ?>" required />
                            </td>	
                            <td title="Data de Conclus�o da Tarefa"> 
                            <input class="selector" type="text" id="data_fim" name="data_fim" value="<?php echo $row['tar_data_conclusao'] ?>" required /> 
                            </td>

                            <td title="Prioriade da Tarefa" >									
                            <select class="tipo_situacao corrigir_campos" name="prioridade" id="prioridade" required>
								<option value="1" <?php if ($row['pri_id'] == 1 ) echo 'selected>', $row['pri_descricao']; else echo '> Baixa';?> </option>
                                <option value="2" <?php if ($row['pri_id'] == 2 ) echo 'selected>', $row['pri_descricao']; else echo '> M�dia'; ?> </option>
                                <option value="3" <?php if ($row['pri_id'] == 3 ) echo 'selected>', $row['pri_descricao']; else echo '> Alta'; ?> </option>
							</select>
                            </td>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                            <td> <label class="negrito" >Alocado para:</label> </td>
                            <td> <label class="negrito" >Tipo:</label> </td>
                        </tr>
                        <tr>
                            <td title="Respons�vel pela Tarefa" >     
                                <select class="tipo_situacao" name="responsavel" required>  
                                <?php  
									echo '<option selected value="' , $row['usu_id'] , '"> ' , $row['usu_nome'] , '</option>';
                                    pegar_usuario_por_projeto( $_GET['pro_id'],  $row['usu_id']);
                                ?>    
                                </select>          
                            </td>
                            
                            <td title="Tipo da Tarefa" >									
                                <select id="tip_tarefa" class="tipo_situacao" name="tip_tarefa" required>
                                    <option value="1" <?php if ($row['tip_t_id'] == 1 ) echo 'selected>', $row['tip_t_descricao']; else echo '> Tarefa';?> </option>
                                    <option value="2" <?php if ($row['tip_t_id'] == 2 ) echo 'selected>',$row['tip_t_descricao']; else echo '> Nova Caracter�stica';?> </option>
                                    <option value="3" <?php if ($row['tip_t_id'] == 3 ) echo 'selected>',$row['tip_t_descricao']; else echo '> Defeito';?>  </option>
                                    <option value="4" <?php if ($row['tip_t_id'] == 4 ) echo 'selected>',$row['tip_t_descricao']; else echo '> Melhoria';?>  </option>
                                </select>
                            </td>
                         
                        </tr>
                    </table>
               </td>
            </tr>

            <tr>
            	<td> 
                	<table class="add_projeto" >
                    	<tr> <td> <br> </tr> </td>
                		<tr> <td> <input class="blue_button" type="submit" value="Salvar" name="editar_tarefa" />  </td> </tr>
                    </table>
                </td>
            </tr>
            
         </table>
	</form>
	</div>
    
</body>
</html>