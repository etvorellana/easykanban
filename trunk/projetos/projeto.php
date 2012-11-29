<?php
	include_once('../connect/connect_vars.php');
	include_once('../sessao_php/inicia_sessao.php');

		
	if (isset($_SESSION['usu_id'])) 
	{
		$usu_id = $_SESSION['usu_id'];
		
		// Quando o usuário submeter os dados de cadastro de nova empresa
		if (isset($_POST['send'])) 
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
			// Seleciona o banco de dados
			mysqli_select_db($dbc, "easykanban-bd")
				or die ('Erro ao selecionar o Banco de Dados');
			
			// recupera os dados digitados no formulário
			$nome = trim ($_POST['nome']);	
			$descrição = trim($_POST['descricao']);
			$data_inicio = trim($_POST['data_inicio']);
			$data_fim = trim($_POST['data_fim']);
			$tip_situacao = trim ($_POST['tipo_situacao']);	
			
			if ( !empty($nome) && !empty($data_inicio) && !empty($data_fim) && !empty($tip_situacao) )
			{
				$por_usu_criador = $usu_id;
				
				// criando query de inserção na tabela projeto
				$query = "INSERT INTO projeto ( tip_id, pro_nome, pro_descricao, pro_dt_inicio, pro_dt_fim, pro_dt_criacao, pro_usu_criador ) VALUES ( '$tip_situacao', '$nome', '$descrição', '$data_inicio', '$data_fim', CURRENT_TIMESTAMP(), '$por_usu_criador' )"
				or die ('Erro ao contruir a consulta');
				
				//execulta query de inserção na tabela cep
				$data = mysqli_query($dbc, $query)
					or die('Erro ao execultar a inserção na tabela projeto');
					
				// recupera o id do projeto inserido e insere na tabela usuario_projeto_tipo
				$ultimo_pro_id = mysqli_insert_id($dbc);
				
				$query = "INSERT INTO usuario_projeto_tipo ( pro_id, usu_id, tip_id ) VALUES ( '$ultimo_pro_id', '$usu_id',  1 )"
				or die ('Erro ao criar a consulta');

				// executa query de inserção na tabela usuario_empresa
				$data = mysqli_query($dbc, $query)
					or die('Erro ao execultar a inserção na tabela usuario_projeto_tipo');
					
				// insere na tebela limite_tarefas, que configura quantas tarefas (por padrão) poderão ser criadas em cada coluna do kanban 
				
				// inserção indica que a coluna 1, 2, 3, 4, 5, (BACKLOG, REQUITADO, ...) tera como padrão suporte a quadro tarefas
				for ( $iCount = 1; $iCount <= 5; $iCount++ ) {
					$query = "INSERT INTO limite_tarefa( pro_id, sit_id ) VALUES ( '$ultimo_pro_id', '$iCount' )";

					// executa query de inserção na tabela limite_tarefa
					$data = mysqli_query($dbc, $query)
						or die('Erro ao execultar a inserção na tabela limite_tarefa');
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

    <link rel="stylesheet" type="text/css" media="all" href="../css/formulario.css">
    <link rel="stylesheet" type="text/css" media="all" href="../fancybox/jquery.fancybox.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.0.6"></script>
    
    <link rel="stylesheet" type="text/css" href="../css/main.css" />
    <link rel="stylesheet" type="text/css" href="../css/projeto.css" />
    
   <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
    <script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
    <link rel="stylesheet" href="/resources/demos/style.css" />
    <script>	
        $(function() {
            $("#data_inicio").datepicker({ dateFormat: "dd/mm/yy" }).val()
            $("#data_fim").datepicker({ dateFormat: "dd/mm/yy" }).val()
        });
        
        $(function() {
            $( "#data_inicio" ).datepicker();
        });
        
        $(function() {
            $( "#data_fim" ).datepicker();
        });
    </script>
        
	<script type="text/javascript">  
        function enter(ev) {  
            if(window.event && window.event.keyCode == 13) {  
                alert('Enter!');
                return false; 
            }  
            else  
                return true; 
        }  
    </script>
    
    
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
                <li><a href="../home/home.php">Home</a></li>
                <li class="atual"><a href="projeto.php">Projetos</a></li>
                <li><a href="#">Relatórios</a></li>
                <li><a href="#">Configurações</a></li>
            </ul>
            
            <div id="nova-tarefa" >
                <a id="bug" class="modalbox" href="#inline"> 
                	<input class="orange_button" type="submit" value="+ Novo Projeto" >
                </a>
            </div>
            
        	<br style="clear:left"/>
        </div>
        
        <div id="main">
            <div>
            	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                	<input type="text" onkeypress="return enter(this)" name="busca" id="busca" placeholder="Buscar Projetos" />
                	<input type="hidden" value="buscar" name="buscar" />
                </form>
            </div>
            
            <?php
            // se a sessão estiver devidamente definida
            if (isset($_SESSION['usu_id'])) 
            {
				if ( isset($_POST['buscar']) )
				{
					$valor_busca = $_POST['busca'];
					
					// conecta ao banco de dados
					$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
						die('Erro ao conectar ao BD!');
						
					// Seleciona o banco de dados
					mysqli_select_db($dbc, "easykanban-bd")
						or die ('Erro ao selecionar o Banco de Dados');
					
					// constroi query de inserção
					$query = "SELECT p.`pro_id`, ts.`tip_situacao`, p.`pro_nome`, p.`pro_descricao`, p.`pro_dt_inicio`, p.`pro_dt_fim`, p.`pro_usu_criador`, up.`tip_id`
							  FROM `projeto` p
							  JOIN `usuario_projeto_tipo` up on up.`pro_id` = p.`pro_id`
							  JOIN `usuario` u on u.`usu_id` = up.`usu_id` 
							  JOIN `tipo_situacao` ts on ts.`tip_id` = p.`tip_id`
							  WHERE u.`usu_id` = '$usu_id'
							  AND p.`pro_nome` LIKE '%$valor_busca%' ORDER BY (pro_nome)";
					
				} 
				else 
				{
					// conecta ao banco de dados
					$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
						die('Erro ao conectar ao BD!');
						
					// Seleciona o banco de dados
					mysqli_select_db($dbc, "easykanban-bd")
						or die ('Erro ao selecionar o Banco de Dados');	
						
					$query = 'SELECT p.`pro_id`, ts.`tip_situacao`, p.`pro_nome`, p.`pro_descricao`, p.`pro_dt_inicio`, p.`pro_dt_fim`, p.`pro_usu_criador`, up.`tip_id`
							  FROM `projeto` p
							  JOIN `usuario_projeto_tipo` up on up.`pro_id` = p.`pro_id`
							  JOIN `usuario` u on u.`usu_id` = up.`usu_id` 
							  JOIN `tipo_situacao` ts on ts.`tip_id` = p.`tip_id`
							  WHERE u.`usu_id` = %s'
								or die ('Erro ao contruir a consulta');
							 
					// alimenta os parametros da conculta
					$query = sprintf($query, $usu_id ); 	
				}
					
				// executa consulta
				$data = mysqli_query($dbc, $query) or die ('Erro ao execultar consulta');
			
				while ($row = mysqli_fetch_array($data)) 
				{
					echo '<div class="projeto_info_hover" id="menu_perfil">
						  <table width="100%" class="border_space">'; 
					
					echo '<tr> <td> <strong class="nome_titulo">', $row['pro_nome'], '</strong> </td> </tr>
						  <tr> <td>  <strong> Descrição: </strong>', $row['pro_descricao'], '</td> </tr>';
					  
					echo '<tr> <td width="60%"> <strong> Data de Início: </strong>', $row['pro_dt_inicio'], '</td>
						  <td> <strong> Previsão de Término:   </strong>', $row['pro_dt_fim'], '</td> </tr>';
					
					echo '<tr> <td> <strong> Situação do Projeto: </strong>', $row['tip_situacao'], '</td>
					<td> <strong> Sua Função: </strong>'; 
					if ( $row['pro_usu_criador'] == $usu_id ) 
						echo'Criador/Administrador'; 
					else if ($row['tip_id'] == ADMIN ) 
							echo'Administrador'; 
						else { echo'Colaborador'; echo '</td> </tr>
							<tr> <td> <strong> Criador/Administrador: </strong>', $row['pro_usu_criador'], '</td> </tr>'; 
						}
					
					echo '</table>';
					
					echo '<div align="center" id="botoes_projeto">';
						echo '<a href="../quadro_kanban/quadro.php?pro_id=' , $row['pro_id'] , ' &tip_id=' , $row['tip_id'] , '" class="gray_button">Entrar</a>';
						
						if ( $row['tip_id'] == ADMIN )
							echo '<a id="config_button" href="config_projeto.php?pro_id=' . $row['pro_id'] . ' " class="gray_button">Configurações</a>';
					echo '</div>
						  </div>';
				}
				mysqli_close($dbc);
				
            }
        ?>
        
        <p id="fim_da_lista"> Não existem mais projetos cadastradas </p>
            
        </div>
    </div>
    
	<!-- invisivel inline form -->
	<div id="inline">
	<h2> Adicionar novo Projeto </h2> <br />
    
	<form id="contact" name="contact" method="post" action="<?php echo ( $_SERVER['PHP_SELF'] ); ?>" >
         <table class="add_projeto" >
         	
            <tr>
                <td>
                    <table class="add_projeto">
                        <tr> <td>  <label for="nome" class="negrito">Nome:</label> </td> </tr>
                        <tr> <td>  <input type="text" id="nome" name="nome" placeholder="Ex: .Projeto de Contabilidade Pública" required>  </td> </tr>
                    </table>
                </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                       	 	<tr> <td>  <label for="descricao" class="negrito">Descrição:</label> </td> </tr>
                        	<tr> <td>  <textarea id="descricao" rows="6" maxlength="250" name="descricao" placeholder="Pequena descrição sobre o Projeto" ></textarea>  </td> </tr>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                            <td> <label class="negrito" >Inicio:</label> </td>
                            <td> <label class="negrito" >Conclusão:</label> </td>
                            <td> <label class="negrito" >Situação:</label> </td>
                        </tr>
                        <tr>
                            <td> <input class="selector" type="text" id="data_inicio" name="data_inicio" /> </td>
                            <td> <input class="selector" type="text" id="data_fim" name="data_fim" /> </td>
                            
                            <td>
                            <select id="tipo_situacao" name="tipo_situacao" required>
                                <option value="1">Em andamento</option>
                                <option value="2">Concluído </option>
                                <option value="3">Parado</option>
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
                        <td> <input class="blue_button" type="submit" value="Cadastrar" name="send" id="send" /> </td>
                        </tr>
                    </table>
               </td>
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

<?php
	}else{
		$home = "../index.php";
		header("Location:" . $home );
	}
?>




