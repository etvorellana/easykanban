<?php
	include_once('../connect/connect_vars.php');
	include_once('../sessao_php/inicia_sessao.php');
	
	if (isset($_SESSION['usu_id']) and isset($_GET['pro_id'])) 
	{
		$usu_id = $_SESSION['usu_id'];
		$pro_id = $_GET['pro_id'];
	
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
					or die('Erro ao executar a inser��o na tabela tarefa');
	
				mysqli_close($dbc);
				
				return $result;
			}
		} //fim fun��o get_tarefas


		function get_user_from_project( $parametro_pro_id )
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
			// selecioma todos os usu�rios logados ao projeto selecionado
			$query = 'SELECT u.usu_id, u.usu_nome
					  FROM usuario u 
					  JOIN usuario_projeto_tipo up on up.usu_id = u.usu_id 
					  JOIN projeto p on p.pro_id = up.pro_id
					  WHERE p.pro_id=%s'
			or die ("Erro ao construir a consulta");
			
			// alimenta os parametros da conculta
			$query = sprintf($query, $parametro_pro_id);	
					
			//executa query de inser��o na tabela cep
			$data = mysqli_query($dbc, $query)
				or die('Erro ao execultar a inser��o na tabela projeto');
			
			while ($row = mysqli_fetch_array($data)) {
				echo '<option value="' , $row['usu_id'] , '"> ' , $row['usu_nome'] , '</option>';
			}
			
			// fecha conex�o com bd
			mysqli_close($dbc);	
		} // fim fun��o get_user_from_project
		
		
		
		function get_limite_tarefas_por_coluna ( $project_id )
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
			// selecioma todos os usu�rios logados ao projeto selecionado
			$query = 'SELECT s.sit_id, l.lin_limite
					  FROM limite_tarefa l
					  JOIN projeto p on p.pro_id = l.pro_id
					  JOIN situacao s on s.sit_id = l.sit_id
					  WHERE p.pro_id =%s'
							or die ("Erro ao construir a consulta");
			
			// alimenta os parametros da conculta
			$query = sprintf($query, $project_id );	
					
			//executa query de inser��o na tabela cep
			$data = mysqli_query($dbc, $query)
				or die('Erro ao execultar a inser��o na tabela projeto');
			
			// fecha conex�o com bd
			mysqli_close($dbc);		
			
			return $data;	
		}	
	}
	else {
		header( 'Location: ../index.php' ) xor die ;	
	}


?>

<!DOCTYPE HTML>
	<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
    	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        
        <link rel="stylesheet" type="text/css" media="all" href="../css/formulario.css">
        <link rel="stylesheet" type="text/css" media="all" href="../fancybox/jquery.fancybox.css">
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.0.6"></script>
        
        <link type="text/css" rel="stylesheet" href="../css/main.css" />
		<link type="text/css" rel="stylesheet" href="../css/quadro.css" />
        
		<script type="text/javascript">
        	function alertselected(selectobj){
        		alert(selectobj.selectedIndex)
        	}
        </script>
        
        <script type="text/javascript">  
			function enter(ev) {  
				
				if(window.event && window.event.keyCode == 13) {  
					flag_tarefa
					return false; 
				}  
				else  
					return true; 
			}  
		</script>
        
		<script>
			var max_tarefas_backlog = 0;
			var max_tarefas_requisitado = 0;
			var max_tarefas_processo = 0;
			var max_tarefas_concluido = 0;
			var max_tarefas_backlog = 0;
		
            function allowDrop(ev)
            {
                ev.preventDefault();
            }
            
            function drag(ev)
            {
                ev.dataTransfer.setData("Text",ev.target.id);
            }
			
			// Quando o usu&aacute;rio arrasta sobre um dos pain�is, retornamos 
			// false para que o evento n�o se propague para o navegador, o 
			// que faria com que o conte�do fosse selecionado
			function dragOver(ev) { 
				return false; 
			} 

            
            function drop(ev, numero_tarefas, maximo_tarefas)
            {
				alert( String(numero_tarefas) + ' >= ' + String(maximo_tarefas) );
				
				if ( numero_tarefas >= maximo_tarefas ){
					return false;
				}
				
                ev.preventDefault();
                var data=ev.dataTransfer.getData("Text");
                ev.target.appendChild(document.getElementById(data));
				
				var sit_id = String(ev.target.id);
				
				var tar_id = String(data);
				
				alert( sit_id + " " + tar_id );

				// se a tarefa for alocada de posi��o, o servidor � requisitado para fazer a atualiza��o do status da tarefa
				location.href= '<?php echo  'mudar_status_tarefa.php?pro_id=' , $pro_id , '&action=change_state&tar_id='; ?>' + tar_id + '<?php echo'&sit_id='; ?>' + sit_id;
				
            }
        </script>
        
	</head>
	
	<body>
    	
        <div id="container-cabecalho">
        <header>
            <div id="nome_usuario" class="menu_acesso_rapido" >
                <label> <?php echo ( $_SESSION['usu_nome']) ?> </label>
            </div>
            <div id="logout" class="config_logout">
                <label> <a class="menu_acesso_rapido" href="../logout.php"> logout </a> </label>
            </div>
            <div id="config" class="config_logout">
            	<label> <a class="menu_acesso_rapido" href="config_tarefas.php?pro_id=<?php echo $pro_id ?>"> Configura��es </a> </label>
            </div>

        </header>
        
        
        </div>
        
        <div id="container-menu">
            <div id="nova-tarefa" >
                <a id="bug" class="modalbox" href="#inline"> 
                    <input class="orange_button" type="submit" value="+ Nova Tarefa" > 
                </a>
            </div>
    	</div>
        
        <nav id="options_menu">
        
            <ul>
                <li> Mostrar somente minhas tarefas: <input type="checkbox" name="mostrar_tarefas" value="Bike"> </li>
                <li> Mostrar somente tarefas atrasadas:  <input type="checkbox" name="mostrar_tarefas" value="Car"> </li>
                <li>
               		Mostrar Tarefas de:
                    <select id="mostrar_tarefas" name="mostrar_tarefas" onChange="alertselected(this)" required>      
                    <?php
						get_user_from_project( $_GET['pro_id'] );
                    ?>    
                    </select>
                </li>

            </ul>
        </nav>
        
        <div id="main">
        
		<div id="div_quadro">
			
            <div id="div_scroll_quadro">
				
                <!-- O id da div representa o indice do banco de dados ex: 1 - BACKLOG -->  
                <?php
					$data_limite = get_limite_tarefas_por_coluna ( $pro_id );
					$row_limite = mysqli_fetch_array($data_limite);
					
					$data_tarefa = get_tarefas_from_project( $pro_id, 1 );
					$GLOBALS['numero_tarefas_backlog'] = mysqli_num_rows($data_tarefa);
					$GLOBALS['maximo_tarefas_backlog'] = $row_limite['lin_limite'];
				?>
                <div id="1" class="quadro" ondrop="drop(event, <?php echo $GLOBALS['numero_tarefas_backlog'] ?>, <?php echo $GLOBALS['maximo_tarefas_backlog'] ?> )" ondragover="allowDrop(event)" >
                	<label class="texto" > BACKLOG </label> <br>
                    <label class="texto" > [ <?php echo $GLOBALS['maximo_tarefas_backlog'], ' /  <input type="text" class="edit_max_tarefas" name="max" onkeypress="return enter(this)" value="', $GLOBALS['maximo_tarefas_backlog'], '" required>'  ?> ]  </label> <br>
                    <a class="modalbox" href="#tarefa_inline"> Editar </a>
                    <?php
						while ( $row_tarefas = mysqli_fetch_array($data_tarefa)) {
							echo '<div id="', $row_tarefas['tar_id'], '" class="tarefa" draggable="true" ondragstart="drag(event)" > ID:' , $row_tarefas['tar_id'] , '</div>';
						}
					?>
                </div>
                
                
                
                <?php 
					$row_limite = mysqli_fetch_array($data_limite);
					$data_tarefa = get_tarefas_from_project( $pro_id, 2 );
					$GLOBALS['numero_tarefas_requisitado'] = mysqli_num_rows($data_tarefa);
					$GLOBALS['maximo_tarefas_requisitado'] = $row_limite['lin_limite'];
				?>       
                <!-- O id da div representa o indice do banco de dados ex: 2 - REQUISITADO -->  
                <div id="2" class="quadro" ondrop="drop(event, <?php echo $GLOBALS['numero_tarefas_requisitado'] ?>, <?php echo $GLOBALS['maximo_tarefas_requisitado'] ?> )" ondragover="allowDrop(event)" >
                	<label class="texto"> REQUISITADO </label><br>
                    <label class="texto" > [ <?php echo $GLOBALS['numero_tarefas_requisitado'], ' / ', $GLOBALS['maximo_tarefas_requisitado']; ?> ]  </label> <br>
                    <?php
						while ( $row_tarefas = mysqli_fetch_array($data_tarefa)) {
							echo '<div id="', $row_tarefas['tar_id'], '" class="tarefa" draggable="true" ondragstart="drag(event)" > ID:' , $row_tarefas['tar_id'] , '</div>';
						}
					?>
                </div>
                
                
                
                
                <?php 
					$row_limite = mysqli_fetch_array($data_limite);
					$data_tarefa = get_tarefas_from_project( $pro_id, 3 );
					$GLOBALS['numero_tarefas_processo'] = mysqli_num_rows($data_tarefa);
					$GLOBALS['maximo_tarefas_processo'] = $row_limite['lin_limite'];
				?>  
                <!-- O id da div representa o indice do banco de dados ex: 3 - EM PROCESSO -->  
                <div id="3" class="quadro" ondrop="drop(event, <?php echo $GLOBALS['numero_tarefas_processo'] ?>, <?php echo $GLOBALS['maximo_tarefas_processo'] ?> )" ondragover="allowDrop(event)" >
                	<label class="texto"> EM PROCESSO </label><br>
                    <label class="texto" > [ <?php echo $GLOBALS['numero_tarefas_processo'], ' / ', $GLOBALS['maximo_tarefas_processo']; ?> ]  </label> <br>
                    <?php
						while ( $row_tarefas = mysqli_fetch_array($data_tarefa)) {
							echo '<div id="', $row_tarefas['tar_id'], '" class="tarefa" draggable="true" ondragstart="drag(event)" > ID:' , $row_tarefas['tar_id'] , '</div>';
						}
					?>
                </div>
                
                
                
                <?php 
					$row_limite = mysqli_fetch_array($data_limite);
					$data_tarefa = get_tarefas_from_project( $pro_id, 4 );
					$GLOBALS['numero_tarefas_concluido'] = mysqli_num_rows($data_tarefa);
					$GLOBALS['maximo_tarefas_concluido'] = $row_limite['lin_limite'];
				?>  
                <div id="4" class="quadro" ondrop="drop(event, <?php echo $GLOBALS['numero_tarefas_concluido'] ?>, <?php echo $GLOBALS['maximo_tarefas_concluido'] ?> )" ondragover="allowDrop(event)" >
                	<label class="texto"> CONCLUIDO </label><br>
                    <label class="texto" > [ <?php echo $GLOBALS['numero_tarefas_concluido'], ' / ', $GLOBALS['maximo_tarefas_concluido']; ?> ]  </label> <br>
                    <?php
						while ( $row_tarefas = mysqli_fetch_array($data_tarefa)) {
							echo '<div id="', $row_tarefas['tar_id'], '" class="tarefa" draggable="true" ondragstart="drag(event)" > ID:' , $row_tarefas['tar_id'] , '</div>';
						}
					?>
                </div>
                
                
                
                <?php 
					$row_limite = mysqli_fetch_array($data_limite);
					$data_tarefa = get_tarefas_from_project( $pro_id, 5 );
					$GLOBALS['numero_tarefas_arquivado'] = mysqli_num_rows($data_tarefa);
					$GLOBALS['maximo_tarefas_arquivado'] = $row_limite['lin_limite'];
				?>  
                <div id="5" class="quadro" ondrop="drop(event, <?php echo $GLOBALS['numero_tarefas_arquivado'] ?>, <?php echo $GLOBALS['maximo_tarefas_arquivado'] ?> )" ondragover="allowDrop(event)" >
                	<label class="texto"> ARQUIVADO </label><br>
                    <label class="texto" > [ <?php echo $GLOBALS['numero_tarefas_arquivado'], ' / ', $GLOBALS['maximo_tarefas_arquivado']; ?> ]  </label> <br>
                    <?php
						while ( $row_tarefas = mysqli_fetch_array($data_tarefa)) {
							echo '<div id="', $row_tarefas['tar_id'], '" class="tarefa" draggable="false" ondragstart="drag(event)" > ID:' , $row_tarefas['tar_id'] , '</div>';
						}
					?>
                </div>
                
                
                
     		</div>
            
		</div>
    </div>
    
	<!-- invisivel inline form -->
	<div id="inline">
	<h2> Adicionar novo Projeto </h2> <br />
    
	<form id="contact" name="contact" method="post" action="add_tarefa.php?pro_id=<?php echo $pro_id; ?>" >
		<table class="add_projeto" >
         	
            <tr>
                <td>
                    <table class="add_projeto">
                        <tr> <td>  <label for="titulo" class="negrito">T&iacute;tulo:</label> </td> </tr>
                        <tr> <td>  <input type="text" id="titulo" name="titulo" placeholder="Ex: Cria&ccedil;&atilde;o de Relat&oacute;rios" required>  </td> </tr>
                    </table>
                </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                       	 	<tr> <td>  <label for="descricao" class="negrito">Descri&ccedil;&atilde;o:</label> </td> </tr>
                        	<tr> <td>  <textarea id="descricao" rows="3" maxlength="250" name="descricao" placeholder="Pequena descri&ccedil;&atilde;o sobre a terafa" ></textarea>  </td> </tr>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                       	 	<tr> <td>  <label for="comentario" class="negrito">Coment&aacute;rio:</label> </td> </tr>
                        	<tr> <td>  <textarea id="comentario" rows="3" maxlength="250" name="comentario" placeholder="Coment&aacute;rios sobre a tarefa" ></textarea>  </td> </tr>
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
                            <td> <label class="negrito" >Tempo Estimado:</label> </td>
                        </tr>
                        <tr>
                            <td> <input type="date" id="data_inicio" name="data_inicio" required> </td>
                            <td> <input type="date" id="data_fim" name="data_fim" required> </td>
                            <td> <input type="text" id="tempo_estimado" name="tempo_estimado" placeholder="Tempo em Horas. Ex: 2:00" required> </td>
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
                            <td> <label class="negrito" >Prioridade:</label> </td>
                        </tr>
                        <tr>
                            <td>     
							<select class="tipo_situacao" name="tipo_situacao" required>      
                            <?php
								get_user_from_project( $_GET['pro_id'] );
							?>    
                            </select>          
                            </td>
                            
                            <td>									
                            <select class="tipo_situacao" name="tip_tarefa" required>
								<option value="1"> Tarefa </option>
                                <option value="2"> Nova Caracter�stica </option>
                                <option value="3"> Defeito  </option>
                                <option value="4"> Melhoria  </option>
							</select>
                            </td>
                            
                            <td>									
                            <select class="tipo_situacao" name="prioridade" required>
								<option value="1">  Baixa </option>
                                <option value="2">  M�dia </option>
                                <option value="3">  Alta  </option>
                                
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
                		<tr> <td> <input class="blue_button" type="submit" value="Cadastrar" name="send" id="send" />  </td> </tr>
                    </table>
                </td>
            </tr>
            
         </table>
	</form>
	</div>

	<!-- invisivel inline form -->
	<div id="tarefa_inline">
	<h2> M�ximo Tarefa </h2> <br />
    
	<form id="contact" name="contact" method="post" action="add_tarefa.php?pro_id=<?php echo $pro_id; ?>" >
		<table class="add_projeto" >
            <tr> <td>  <label for="max" class="negrito">M�ximo:</label> </td>  </tr>
            <tr> <td>  <input type="text" id="max" name="max" placeholder="Ex: 4" required>  </td> </tr> 
            <tr> <td> <br> </td> </tr>
            <tr> <td> <input class="blue_button" type="submit" value="Salvar" name="save" id="save" />  </td> </tr>  
         </table>
	</form>
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
            $(".modalbox").fancybox(); //inicia a caixa de dialogo com o formul&aacute;rio
            
            // fazer valida��o javascript
            //
            //
				
            sleep( 1000 );					// espera 115 minutos para o usu&aacute;rio poder ler a mensagem na tela		
            $("#contact").submit(function() {  // quando os dados forem submetidos...
			
				// o n�mero de tarefas alocadas foi maior que o permitido
				var atual_numero_de_tarefas = "<?php echo $GLOBALS['numero_tarefas_backlog'] ?>";
				var maximo_numero_de_tarefas = "<?php echo $GLOBALS['maximo_tarefas_backlog'] ?>";

				//alert( atual_numero_de_tarefas + ' >= '  + maximo_numero_de_tarefas );

				if ( atual_numero_de_tarefas >= maximo_numero_de_tarefas ){
					$(this).before("<p><strong>N�mero m&aacute;ximo de tarefas excedido!</strong></p>"); // exibe mensagem de confirma��o para o usu&aacute;rio

					return false;
				}
			
                $("#contact").fadeOut("slow", function(){
                    $(this).before("<p><strong>Projeto cadastrado com Sucesso!</strong></p>"); // exibe mensagem de confirma��o para o usu&aacute;rio
                    setTimeout("$.fancybox.close()", 1000); // fecha caixa de dialogo
                });
            });
    
        });
    </script>

	</body>
</html>
