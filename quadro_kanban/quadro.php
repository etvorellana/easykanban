<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	if (isset($_SESSION['usu_id']) and isset($_GET['pro_id'])) 
	{
		$usu_id = $_SESSION['usu_id'];
		$pro_id = $_GET['pro_id'];
		
		if (isset($_POST['send'])) 
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
			// recupera os dados digitados no formulário
			$tar_titulo = trim($_POST['titulo']);
			$tar_descricao = trim($_POST['descricao']);
			$tar_comentario = trim ($_POST['comentario']);	
			$tar_data_inicio = trim ($_POST['data_inicio']);
			$tar_data_conclusao = trim ($_POST['data_fim']);
			$tar_tempo_estimado = trim($_POST['tempo_estimado']);
			$tar_data_criacao = date("d/m/Y h:i:s");

			if ( !empty($tar_titulo) )
			{

				// criando query de inserção na tabela tarefa
				$query = "INSERT INTO tarefa ( met_id, sit_id, pro_id, tar_titulo, tar_descricao, tar_comentario, tar_data_inicio, tar_data_conclusao, tar_tempo_estimado, tar_data_criacao) VALUES ( NULL, '1', '$pro_id', '$tar_titulo', '$tar_descricao', '$tar_comentario', '$tar_data_inicio', '$tar_data_conclusao', NULL, '$tar_data_criacao');"
				or die ('Erro ao contruir a consulta');
				
				//execulta query de inserção na tabela tarefa
				$data = mysqli_query($dbc, $query)
					or die('Erro ao execultar a inserção na tabela tarefa');
			}
			/* Fecha conexão com o banco */
			mysqli_close($dbc);
		}
		
	}
	else {
		$home = "../index.php";
		header("Location:" . $home );	
	}


?>

<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<title>easykanban</title>
        
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.0.6"></script>
        <link rel="stylesheet" type="text/css" media="all" href="../css/formulario.css">
        <link rel="stylesheet" type="text/css" media="all" href="../fancybox/jquery.fancybox.css">
        

        
        <link type="text/css" rel="stylesheet" href="../css/main.css" />
		<link type="text/css" rel="stylesheet" href="../css/quadro.css" />
        
		<script>
			function allowDrop(ev)
			{
				ev.preventDefault();
			}
		
			function drag(ev)
			{
				// Quando o usuário inicia um drag, guardamos no dataset do evento 
				// o id do objeto sendo arrastado
				ev.dataTransfer.setData("ID",ev.target.id);
			}
		
			// Quando o usuário arrasta sobre um dos painéis, retornamos 
			// false para que o evento não se propague para o navegador, o 
			// que faria com que o conteúdo fosse selecionado
			function dragOver(ev) { return false; } 
		
			function drop(ev)
			{
				ev.preventDefault();
				var data = ev.dataTransfer.getData("ID");
				ev.target.appendChild(document.getElementById(data));
				
				var restult = String(data);
				alert (restult);
				
				// se a tarefa for alocada de posição, o servidor é requisitado para fazer a atualização do status da tarefa
				location.href= '<?php echo ( 'mudar_status_tarefa.php?pro_id=' . $pro_id . '&action=change_state&tar_id='); ?>' + restult;
			}
        </script>
        
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
            <div id="nova-tarefa" >
                <a id="bug" class="modalbox" href="#inline"> 
                    <input class="orange_button" type="submit" value="+ Novo Projeto" > 
                </a>
            </div>
    	</div>
        
        <div id="options_menu">
        	<table width="50%">
           		<tr> 
                	<td> Mostrar somente minhas tarefas: <input type="checkbox" name="mostrar_tarefas" value="Bike"> </td>
            		<td> Mostrar somente tarefas atrasadas:  <input type="checkbox" name="mostrar_tarefas" value="Car"> </td>
                    <td> Mostrar Tarefas de: </td>
                </tr>
            </table>
        </div>
        
        <div id="main">
        
		<div id="div_quadro">
			
            <div id="div_scroll_quadro">
				
                <!-- O id da div representa o indice do banco de dados ex: 1 - BACKLOG -->  
                <div id="1" class="quadro" ondrop="drop(event)" ondragover="allowDrop(event)" >
                	<label class="texto" > BACKLOG </label> 
                    <?php
						if (isset($_SESSION['usu_id']) and isset($_GET['pro_id']))  
						{
							// conectar ao banco de dados
							$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
							die('Erro ao conectar ao BD!');
						
							$query = "SELECT t.tar_id, t.tar_titulo FROM tarefa t JOIN projeto p on p.pro_id = t.pro_id WHERE t.pro_id =" . $pro_id
							or die ("Erro ao construir a consulta");
														
							//execulta query de consulta na tabela tarefa
							$data = mysqli_query($dbc, $query)
								or die('Erro ao execultar a inserção na tabela tarefa');
							
							while ($row = mysqli_fetch_array($data)) {
								echo '<div id="' . $row['tar_id'] . '" class="tarefa" draggable="true" ondragstart="drag(event)" >' . $row['tar_titulo'] . '</div>';
							}
							
							mysqli_close($dbc);
						}

					?>
                </div>
                
                <!-- O id da div representa o indice do banco de dados ex: 2 - REQUISITADO -->  
                <div id="2" class="quadro" ondrop="drop(event)" ondragover="allowDrop(event)" >
                	<label class="texto"> REQUISITADO </label>
                    
                </div>
                
                <!-- O id da div representa o indice do banco de dados ex: 3 - EM PROCESSO -->  
                <div id="3" class="quadro" ondrop="drop(event)" ondragover="allowDrop(event)" >
                	<label class="texto"> EM PROCESSO </label>
                </div>
                
                <div id="4" class="quadro" ondrop="drop(event)" ondragover="allowDrop(event)" >
                	<label class="texto"> CONCLUIDO </label>
                </div>
                
                <div id="5" class="quadro" ondrop="drop(event)" ondragover="allowDrop(event)" >
                	<label class="texto"> ARQUIVADO </label>
                </div>
     		</div>
            
		</div>
    </div>
    
	<!-- invisivel inline form -->
	<div id="inline">
	<h2> Adicionar novo Projeto </h2> <br />
    
	<form id="contact" name="contact" method="post" action="<?php echo ( $_SERVER['PHP_SELF'] . '?pro_id=' . $pro_id ); ?>" >
		<table class="add_projeto" >
         	
            <tr>
                <td>
                    <table class="add_projeto">
                        <tr> <td>  <label for="titulo" class="negrito">T&iacute;tulo:</label> </td> </tr>
                        <tr> <td>  <input type="text" id="titulo" name="titulo" placeholder="Ex: Criação de Relatórios" required>  </td> </tr>
                    </table>
                </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                       	 	<tr> <td>  <label for="descricao" class="negrito">Descri&ccedil;&atilde;o:</label> </td> </tr>
                        	<tr> <td>  <textarea id="descricao" rows="3" maxlength="250" name="descricao" placeholder="Pequena descrição sobre a terafa" ></textarea>  </td> </tr>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                       	 	<tr> <td>  <label for="comentario" class="negrito">Coment&aacute;rio:</label> </td> </tr>
                        	<tr> <td>  <textarea id="comentario" rows="3" maxlength="250" name="comentario" placeholder="Comentários sobre a tarefa" ></textarea>  </td> </tr>
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
                            <td> <label class="negrito" >Prioridade:</label> </td>
                        </tr>
                        <tr>
                            <td>     
							<select name="tipo_situacao" required>      
                            <?php
								if (isset($_SESSION['usu_id']) and isset($_GET['pro_id']))  
								{
									// conectar ao banco de dados
									$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
									die('Erro ao conectar ao BD!');
								
									$query = "SELECT usu_id, usu_nome FROM usuario WHERE usu_id=" . $usu_id
									or die ("Erro ao construir a consulta");
																
									//execulta query de inserção na tabela cep
									$data = mysqli_query($dbc, $query)
										or die('Erro ao execultar a inserção na tabela projeto');
									
									while ($row = mysqli_fetch_array($data)) {
										echo '<option value="' . $row['usu_id'] . '">' . $row['usu_nome'] . '</option> ';
									}
									
									mysqli_close($dbc);
								}
							?>    
                            </select>          
                            </td>
                            
                            <td>									
                            <select name="tipo_situacao" required>
								<option value="Baixa"> Baixa </option>
                                <option value="Média"> Média </option>
                                <option value="Alta">  Alta  </option>
							</select>
                            </td>
                        </tr>
                    </table>
               </td>
            </tr>

            <tr>
            <td> <input class="blue_button" type="submit" value="Cadastrar" name="send" id="send" /> </td>
            </tr>
            
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
            $(".modalbox").fancybox(); //inicia a caixa de dialogo com o formulário
            
            // fazer validação javascript
            //
            //
            
            sleep( 1000 );					// espera 115 minutos para o usuário poder ler a mensagem na tela		
            $("#contact").submit(function() {  // quando os dados forem submetidos...
                $("#contact").fadeOut("slow", function(){
                    $(this).before("<p><strong>Projeto cadastrado com Sucesso!</strong></p>"); // exibe mensagem de confirmação para o usuário
                    setTimeout("$.fancybox.close()", 1000); // fecha caixa de dialogo
                });
            });
    
        });
    </script>

	</body>
</html>
