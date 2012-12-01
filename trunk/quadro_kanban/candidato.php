<?php
	include_once('../connect/connect_vars.php');
	include_once('../sessao_php/inicia_sessao.php');
	
	if (isset($_SESSION['usu_id']) and isset($_GET['pro_id']) and isset($_GET['tip_id']) ) 
	{
		$usu_id = $_SESSION['usu_id'];
		$pro_id = $_GET['pro_id'];
		$permissao = $_GET['tip_id'];
		
		function pegar_usuario_por_projeto( $parametro_pro_id )
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
			// Seleciona o banco de dados
			mysqli_select_db($dbc, "easykanban-bd")
				or die ('Erro ao selecionar o Banco de Dados');
			
			// selecioma todos os usuários logados ao projeto selecionado
			$query = "SELECT u.`usu_id`, u.`usu_nome`
					  FROM `usuario` u 
					  JOIN `usuario_projeto_tipo` up on up.`usu_id` = u.`usu_id` 
					  JOIN `projeto` p on p.`pro_id` = up.`pro_id`
					  WHERE p.`pro_id`= '$parametro_pro_id'"
			or die ("Erro ao construir a consulta");	
					
			//executa query de inserção na tabela cep
			$data = mysqli_query($dbc, $query)
				or die('Erro ao executar a inserção na tabela projeto');
			
			return $data;
			
			// fecha conexão com bd
			mysqli_close($dbc);	
		} // fim função pegar_usuario_por_projeto
		
	}
	else {
		header( 'Location: ../index.php' ) xor die ;	
	}	
	
?>

<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<title>Documento sem t&iacute;tulo</title>
    
    <link rel="stylesheet" type="text/css" media="all" href="../css/formulario.css">
    <link rel="stylesheet" type="text/css" media="all" href="../fancybox/jquery.fancybox.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.0.6"></script>
    
    <link type="text/css" rel="stylesheet" href="../css/main.css" />
    <link type="text/css" rel="stylesheet" href="../css/candidato.css" />
    
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
    <script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
    <link rel="stylesheet" href="/resources/demos/style.css" />
    
    
    <style>
    .quadro.over {
        border:             1px dashed #000;
        background-color:   #FFFFE0;
    }
    </style>
    
    <script>	
        $(function() {
            $("#data_inicio").datepicker({ dateFormat: "yy-mm-dd" }).val()
            $("#data_fim").datepicker({ dateFormat: "yy-mm-dd" }).val()
        });
        
        $(function() {
            $( "#data_inicio" ).datepicker();
        });
        
        $(function() {
            $( "#data_fim" ).datepicker();
        });
    </script>

	<script>
        function allowDrop(ev)
        {
            ev.preventDefault();
        }
        
        function drag(ev, responsavel )
        {
            var usuario_logado = <?php echo $_SESSION['usu_id'] ?>;
            
            if ( responsavel != usuario_logado ){
                alert('Permissão negada!');
                return false;	
            }

            ev.dataTransfer.setData("Text",ev.target.id);
        }
        
        // Quando o usu&aacute;rio arrasta sobre um dos painéis, retornamos 
        // false para que o evento não se propague para o navegador, o 
        // que faria com que o conteúdo fosse selecionado
        function dragOver(ev) { 
            return false; 
        } 
		
		function startAnim(ev){
			//document.getElementById(ev.target.id).style.backgroundColor="#CCC";
			document.getElementById(ev.target.id).classList.add('over');
		}
		
		function finishAnim(ev){
			//document.getElementById(ev.target.id).style.backgroundColor="#FFF";
			document.getElementById(ev.target.id).classList.remove('over');
		}

        function drop(ev, maximo_tarefas, quantidade_atual )
        {
			document.getElementById(ev.target.id).style.backgroundColor="trasparent";
			
            //alert( 'Máximo de Tarefas =' + String(maximo_tarefas) );
			//alert( 'Quantidade atual =' + String(quantidade_atual) );
			
			if ( quantidade_atual >= maximo_tarefas ){
				alert('Limite de tarefas excedido!');
				return false;
			}
			
            ev.preventDefault();
            var data = ev.dataTransfer.getData("Text");
            ev.target.appendChild(document.getElementById(data));
            
            var sit_id = String(ev.target.id);
            
            var tar_id = String(data);
            
            //alert( sit_id + " " + tar_id );

            // se a tarefa for alocada de posição, o servidor é requisitado para fazer a atualização do status da tarefa
            location.href= '<?php echo  'mudar_status_tarefa.php?pro_id=' , $pro_id , '&tip_id=', $permissao, '&action=change_state&tar_id='; ?>' + tar_id + '<?php echo'&sit_id='; ?>' + sit_id + '<?php if (isset($usu_id_selecionado)) echo '&usu_id_selecionado=', $usu_id_selecionado; ?>';
            
        }
    </script>
    
    
</head>

<body>
    <!-- Cabeçalho -->
    <div id="container-cabecalho">
    <header>
        <div id="nome_usuario" class="menu_acesso_rapido" >
            <label> <?php echo ( $_SESSION['usu_nome'] ) ?> </label>
        </div>
        
        <div id="logout" class="config_logout">
            <label> <a class="menu_acesso_rapido" href="../logout.php"> logout </a> </label>
        </div>
        
        <?php
        if ( $permissao == ADMIN )
        echo '
            <div id="config" class="config_logout">
                <label> <a class="menu_acesso_rapido" href="config_tarefas.php?pro_id=', $pro_id ,'&tip_id=', $permissao, '"> Configurações </a> </label>
            </div>';
        ?> 
    </header>
    </div>
    <!-- Fim cabeçalho -->
    
    <!-- Barra de Menu -->
    <div id="container-menu-tarefas">
    <?php
        if ($permissao == ADMIN ) {
            echo '
                <div id="nova-tarefa" >
                    <a id="bug" class="fancybox" href="#inline"> 
                        <input class="orange_button" type="submit" value="+ Nova Tarefa" > 
                    </a>
                </div>
            ';
        }
    ?>
    </div>
    <!-- Fim barra de Menu -->
    
    <!-- menu de opções -->
    <nav id="options_menu">
    <ul>
        <li> Mostrar somente minhas tarefas: <input type="checkbox" name="mostrar_tarefas" value="Bike"> </li>
        <li> Mostrar somente tarefas atrasadas:  <input type="checkbox" name="mostrar_tarefas" value="Car"> </li>
        <li>
            Mostrar Tarefas de:
            <select id="mostrar_tarefas" name="mostrar_tarefas" required>      
            <?php
               $pegar_usuario_por_projeto = pegar_usuario_por_projeto( $_GET['pro_id'] );
			   
				while ($row = mysqli_fetch_array($pegar_usuario_por_projeto)) {
					echo '<option value="' , $row['usu_id'] , '"> ' , $row['usu_nome'] , '</option>';
				}
				
				mysqli_data_seek($pegar_usuario_por_projeto, 0);
            ?>    
            </select>                     
        </li>
    </ul>
    </nav>
    <!-- Fim menu de opções -->


    
    <!-- Quadro de Kanban -->
    <div id="div_quadro">
    
        <div id="div_scroll_quadro">
        
            <?php 
                // conectar ao banco de dados
                $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
                die('Erro ao conectar ao BD!');
            
                // Seleciona o banco de dados
                mysqli_select_db($dbc, "easykanban-bd")
                    or die ('Erro ao selecionar o Banco de Dados');
            
                $query = "SELECT s.`sit_id`, t.`tar_id`, t.`tar_titulo`, r.`usu_id`, u.`usu_nickname`
							FROM `tarefa` t
							JOIN `projeto` p ON p.`pro_id` = t.`pro_id`
							JOIN `situacao` s ON s.`sit_id` = t.`sit_id`
							JOIN `responsavel` r on r.`tar_id` = t.`tar_id` 
							JOIN `usuario` u on u.`usu_id` = r.`usu_id`
							WHERE t.`pro_id` = '$pro_id'
							ORDER BY(s.`sit_id`)"
                or die ("Erro ao construir a consulta");		
                        
                //executa query de consulta na tabela tarefa
                $data = mysqli_query($dbc, $query)
                    or die('Erro ao executar a consulta na tabela tarefa');

				
				// seleciona o limite de tarefa de cada uma das colunas do quadro kanban
				$query = 'SELECT s.`sit_id`, l.`lin_limite`
							FROM `limite_tarefa` l
							JOIN `projeto` p on p.`pro_id` = l.`pro_id`
							JOIN `situacao` s on s.`sit_id` = l.`sit_id`
							WHERE p.`pro_id` = %s'
								or die ("Erro ao construir a consulta");
				
				// alimenta os parametros da conculta
				$query = sprintf($query, $pro_id );	
				
						
				//executa query na tabela limite_tarefa
				$data_limite_tarefas = mysqli_query($dbc, $query)
					or die('Erro ao execultar a inserção na tabela projeto');
	
	            // fecha conexão com o banco de dados
                mysqli_close($dbc);
                
                
				// pega os valores máximo que cada coluna suporta em um array, [0] Backlog, [1]Requisitado ...
				$max_tarefas = array();
				while ( $row_limite_tarefa = mysqli_fetch_array($data_limite_tarefas) )
					$max_tarefas[] = $row_limite_tarefa['lin_limite'];
				
				$linhas = mysqli_num_rows($data);
				
				print_r($max_tarefas);
				
				
				// array guarda a quantidade atual de tarefas em cada uma das colunas do quadro
				$atual_num_tarefas = array(0, 0, 0, 0, 0);
				
				while( $row_tarefas = mysqli_fetch_array($data) )
				{
					switch( $row_tarefas['sit_id'] ){
						case 1:
							$atual_num_tarefas[0]++;
							break;
						case 2:
							$atual_num_tarefas[1]++;
							break;
						case 3:
							$atual_num_tarefas[2]++;
							break;
						case 4:
							$atual_num_tarefas[3]++;
							break;
						case 5:
							$atual_num_tarefas[4]++;
							break;
					}
				}
				mysqli_data_seek( $data, 0 );
            ?>  
            
            <div id="1" class="quadro" ondragenter="startAnim(event)" ondragleave=" finishAnim(event)" ondrop="drop(event, <?php echo $max_tarefas[0], ',', $atual_num_tarefas[0] ?> )" ondragover="allowDrop(event)" >
				<label class="texto" > <strong> BACKLOG </strong> </label> <br/>
				<label class="texto" > [ <?php echo $atual_num_tarefas[0], ' / ', $max_tarefas[0] ?> ]  </label> <br>	
                <?php
                    $row_tarefas = mysqli_fetch_array($data);
                    $linha = 0;
                    if ( $row_tarefas['sit_id'] == 1 ){
                        do {
                            echo '<div id="', $row_tarefas['tar_id'], '" class="tarefa" draggable="true" ondragstart="drag(event, ', $row_tarefas['usu_id'], ')" >
							<div class="info_tarefa">  
								<strong class="tar_titulo">', $row_tarefas['tar_titulo'], ' </strong> <br/> <strong>', $row_tarefas['usu_nickname'], '<br/> ID:  ' , $row_tarefas['tar_id'] , '</strong>
								<a href="editar_tarefas.php?pro_id=', $pro_id, '&tar_id=', $row_tarefas['tar_id'], '&tip_id=', $permissao, '"> 
								<br/> <img src="../images/edit_button.png" alt="configurações" /> </a> 
							</div> 
							</div>';
							
                            $linha++;
                            $row_tarefas = mysqli_fetch_array($data);

                        }while ( $row_tarefas['sit_id'] == 1 );
                        
                        mysqli_data_seek( $data, $linha);
                        
                        if ( $data != NULL )
                            $row_tarefas = mysqli_fetch_array($data);
                    }
                ?>
            </div>
            
            

            <div id="2" class="quadro" ondragenter="startAnim(event)" ondragleave=" finishAnim(event)" ondrop="drop(event, <?php echo $max_tarefas[1], ',', $atual_num_tarefas[1] ?> )" ondragover="allowDrop(event)" >
                <label class="texto"> <strong>  REQUISITADO </strong> </label><br/>
				<label class="texto" > [ <?php echo $atual_num_tarefas[1], ' / ', $max_tarefas[1] ?> ]  </label> <br>
                <?php
                    if ( $row_tarefas['sit_id'] == 2 ){
                        do {
                            echo '<div id="', $row_tarefas['tar_id'], '" class="tarefa" draggable="true" ondragstart="drag(event, ', $row_tarefas['usu_id'], ')" >
							<div class="opcoes"> </div>  <a href="editar_tarefas.php?pro_id=', $pro_id, '&tar_id=', $row_tarefas['tar_id'], '&tip_id=', $permissao, '"> 
							<strong> ID:  ' , $row_tarefas['tar_id'] , ' </strong> <br/> <img src="../images/edit_button.png" alt="configurações" /> </a> </div>';
                            
                            $linha++;
                            $row_tarefas = mysqli_fetch_array($data);

                        }while ( $row_tarefas['sit_id'] == 2 );
                        
                        mysqli_data_seek( $data, $linha);
                        
                        if ( $data != NULL )
                            $row_tarefas = mysqli_fetch_array($data);
                    }
                ?>
            </div>
            
            

            <div id="3" class="quadro" ondrop="drop(event, <?php echo $max_tarefas[2], ',', $atual_num_tarefas[3] ?>)" ondragover="allowDrop(event)" >
                <label class="texto"> <strong>  EM PROCESSO </strong> </label><br/>
				<label class="texto" > [ <?php echo $atual_num_tarefas[2], ' / ', $max_tarefas[2] ?> ]  </label> <br>
                <?php
                    
					if ( $row_tarefas['sit_id'] == 3 ){
						do {
							echo '<div id="', $row_tarefas['tar_id'], '" class="tarefa" draggable="true" ondragstart="drag(event, ', $row_tarefas['usu_id'], ')" >
							<div class="opcoes"> </div>  <a href="editar_tarefas.php?pro_id=', $pro_id, '&tar_id=', $row_tarefas['tar_id'], '&tip_id=', $permissao, '"> 
							<strong> ID:  ' , $row_tarefas['tar_id'] , ' </strong> <br/> <img src="../images/edit_button.png" alt="configurações" /> </a> </div>';
							
							$linha++;
							$row_tarefas = mysqli_fetch_array($data);

						}while ( $row_tarefas['sit_id'] == 3 );
						
						mysqli_data_seek( $data, $linha);
						
						if ( $data != NULL )
							$row_tarefas = mysqli_fetch_array($data);
					}
                    
                ?>
            </div>
            


            <div id="4" class="quadro" ondrop="drop(event, <?php echo $max_tarefas[3], ',', $atual_num_tarefas[3] ?>)" ondragover="allowDrop(event)" >
                <label class="texto"> <strong>  CONCLUIDO </strong> </label><br/>
				<label class="texto" > [ <?php echo $atual_num_tarefas[3], ' / ', $max_tarefas[3] ?> ]  </label> <br>
                <?php
                    
					if ( $row_tarefas['sit_id'] == 4 ){
						do {
							echo '<div id="', $row_tarefas['tar_id'], '" class="tarefa" draggable="true" ondragstart="drag(event, ', $row_tarefas['usu_id'], ')" >
							<div class="opcoes"> </div>  <a href="editar_tarefas.php?pro_id=', $pro_id, '&tar_id=', $row_tarefas['tar_id'], '&tip_id=', $permissao, '"> 
							<strong> ID:  ' , $row_tarefas['tar_id'] , ' </strong> <br/> <img src="../images/edit_button.png" alt="configurações" /> </a> </div>';
							
							$linha++;
							$row_tarefas = mysqli_fetch_array($data);

						}while ( $row_tarefas['sit_id'] == 4 );
						
						mysqli_data_seek( $data, $linha);
						
						if ( $data != NULL )
							$row_tarefas = mysqli_fetch_array($data);
					}
                    
                ?>
            </div>
            
         
            <div id="5" class="quadro" ondrop="drop(event, <?php echo $max_tarefas[4], ',', $atual_num_tarefas[4] ?>)" ondragover="allowDrop(event)" >
                <label class="texto"> <strong>  ARQUIVADO </strong> </label><br/>
				<label class="texto" > [ <?php echo $atual_num_tarefas[4], ' / ', $max_tarefas[4] ?> ]  </label> <br>
                <?php
            
					if ( $row_tarefas['sit_id'] == 5 ){
						do {
							echo '<div id="', $row_tarefas['tar_id'], '" class="tarefa" draggable="false" ondragstart="drag(event, ', $row_tarefas['usu_id'], ')" >
							<div class="opcoes"> </div>  <a href="editar_tarefas.php?pro_id=', $pro_id, '&tar_id=', $row_tarefas['tar_id'], '&tip_id=', $permissao, '"> 
							<strong> ID:  ' , $row_tarefas['tar_id'] , ' </strong> <br/> <img src="../images/edit_button.png" alt="configurações" /> </a> </div>';
							
							$linha++;
							$row_tarefas = mysqli_fetch_array($data);

						}while ( $row_tarefas['sit_id'] == 5 );
						
						mysqli_data_seek( $data, $linha);
						
						if ( $data != NULL )
							$row_tarefas = mysqli_fetch_array($data);
					}
                    
                ?>
            </div>
        </div>
    
    </div>
	<!-- invisivel inline form -->
	<div id="inline">
	<h2> Adicionar novo Projeto </h2> <br />
    
	<form id="contact" name="contact" method="post" action="add_tarefa.php?pro_id=<?php echo $pro_id, '&tip_id=', $permissao; ?>" >
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
                            <td> <label class="negrito" >Prioridade:</label> </td>
                        </tr>
                        <tr>
                            <td> <input class="selector" type="text" id="data_inicio" name="data_inicio" /> </td>
                            <td> <input class="selector" type="text" id="data_fim" name="data_fim" /> </td>
							
                            <td>									
                            <select class="tipo_situacao" name="prioridade" required>
								<option value="1">  Baixa </option>
                                <option value="2">  Média </option>
                                <option value="3">  Alta  </option>
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
                            <td>     
							<select class="tipo_situacao" name="tipo_situacao" required>      
                            <?php
								while ($row = mysqli_fetch_array($pegar_usuario_por_projeto)) {
									echo '<option value="' , $row['usu_id'] , '"> ' , $row['usu_nome'] , '</option>';
								}
							?>    
                            </select>          
                            </td>
                            
                            <td>									
                            <select class="tipo_situacao" name="tip_tarefa" required>
								<option value="1"> Tarefa </option>
                                <option value="2"> Nova Característica </option>
                                <option value="3"> Defeito  </option>
                                <option value="4"> Melhoria  </option>
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
	
    <script type="text/javascript" language="javascript">
    
        var atual_numero_de_tarefas = "<?php echo $atual_num_tarefas[0] ?>";
        var maximo_numero_de_tarefas = "<?php echo $max_tarefas[0] ?>";
    
		$(document).ready(function(){
			$('.fancybox').fancybox();
				
            $("#contact").submit(function() {  // quando os dados forem submetidos...
			    
			    alert( 'Atual - ' + atual_numero_de_tarefas );
                alert( 'Maximo - ' + maximo_numero_de_tarefas );
        
				if ( atual_numero_de_tarefas < maximo_numero_de_tarefas ){
                    $("#contact").fadeOut("slow", function(){
                        $(this).before("<p><strong>Tarefa inserida com Sucesso!</strong></p>"); // exibe mensagem de confirmação para o usuário
                        setTimeout("$.fancybox.close()", 1000); // fecha caixa de dialogo
                    });
                }
                else{
                    $(this).before("<p><strong>Número máximo de tarefas exedido!</strong></p>");
					return false;
				}
				
            });
			
		});
	</script>
	
</body>

</html>

	

    
