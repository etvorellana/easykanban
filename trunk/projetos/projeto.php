<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	if( isset($_SESSION['usu_id']) )
		$usu_id = $_SESSION['usu_id'];
	else{
		$home = "../index.php";
		header("Location:" . $home );
	}
		
	if (isset($_SESSION['usu_id'])) 
	{

		// Quando o usuário submeter os dados de cadastro de nova empresa
		if (isset($_POST['send'])) 
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
			// recupera os dados digitados no formulário
			$nome = trim ($_POST['nome']);	
			$descrição = trim($_POST['descricao']);
			$data_inicio = trim($_POST['data_inicio']);
			$data_fim = trim($_POST['data_fim']);
			$tip_situacao = trim ($_POST['tipo_situacao']);	
			$data_hora = date("d/m/Y h:i:s");

			if ( !empty($nome) && !empty($data_inicio) && !empty($data_fim) && !empty($tip_situacao) )
			{

				// criando query de inserção na tabela projeto
				$query = "INSERT INTO projeto ( tip_id, pro_nome, pro_descricao, pro_dt_inicio, pro_dt_fim, pro_dt_criacao ) VALUES ( '$tip_situacao', '$nome', '$descrição', '$data_inicio', '$data_fim', '$data_hora' )"
				or die ('Erro ao contruir a consulta');
				
				//execulta query de inserção na tabela cep
				$data = mysqli_query($dbc, $query)
					or die('Erro ao execultar a inserção na tabela projeto');
					
				// recupera o id do projeto inserido e insere na tabela usuario_projeto
				$ultimo_pro_id = mysqli_insert_id($dbc);
				
				$query = "INSERT INTO usuario_projeto ( usu_id, pro_id ) VALUES ( '$usu_id', '$ultimo_pro_id' )"
				or die ('Erro ao criar a consulta');
				
				// execulta query de inserção na tabela usuario_empresa
				$data = mysqli_query($dbc, $query)
					or die('Erro ao execultar a inserção na tabela usuario_empresa');
	
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
    <link rel="stylesheet" type="text/css" href="../css/projeto.css" />
  
  </head> 

  <body>
  	<div id="corpo">
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
            <li><a href="../empresa/empresa.php">Empresas</a></li>
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
                <input type="text" name="busca" id="busca" placeholder="Buscar Projetos" />
            </div>
            
            <?php
            // se a sessão estiver devidamente definida
            if (isset($_SESSION['usu_id'])) 
            {
                // conecta ao banco de dados
                $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
                    die('Erro ao conectar ao BD!');
                    
                    
                $query = "SELECT p.pro_id, ts.tip_situacao, p.pro_nome, p.pro_descricao, p.pro_dt_inicio, p.pro_dt_fim " .
						"FROM projeto p " .
						"JOIN usuario_projeto up on up.pro_id = p.pro_id " .
						"JOIN usuario u on u.usu_id = up.usu_id " .
						"JOIN tipo_situacao ts on ts.tip_id = p.tip_id " .
						"WHERE u.usu_id=" . $usu_id 
                         or die ('Erro ao contruir a consulta');
						 

                // executa consulta
                $data = mysqli_query($dbc, $query) or die ('Erro ao execultar consulta');
                //*$row = mysqli_num_rows($data); */
            
                while ($row = mysqli_fetch_array($data)) 
                {
                    echo '<div class="projeto_info_hover" id="menu_perfil">';
                    echo '<table width="100%" class="border_space">';
                    
					
                    echo '<tr> <td> <strong class="nome_titulo">'; echo( $row['pro_nome'] ); echo '</strong> </td> </tr>';
                    echo '<tr> <td>  <strong> Descrição: </strong>';     echo( $row['pro_descricao'] ); echo '</td> </tr>';
                      
                    echo '<tr> <td width="60%"> <strong> Data de Início: </strong>';  echo( $row['pro_dt_inicio'] );   echo '</td>';
                    echo '<td> <strong> Previsão de Término:   </strong>';  echo( $row['pro_dt_fim'] ); echo '</td> </tr>';
                    
					echo '<tr> <td> <strong> Situação do Projeto: </strong>';     echo( $row['tip_situacao'] ); echo '</td> </tr>';
                
                    echo '</table>';
                    
                    echo '<div id="botoes_empresa">';
                        echo '<a href="../quadro_kanban/quadro.php?pro_id=' . $row['pro_id'] . ' " class="gray_button">Entrar</a>';
                        echo '<a id="config_button" href="config_projeto.php?pro_id=' . $row['pro_id'] . ' " class="gray_button">Configurações</a>';
                    echo '</div>';
                    
                    echo '</div>';
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
                            <td> <input type="date" id="data_inicio" name="data_inicio" required> </td>
                            <td> <input type="date" id="data_fim" name="data_fim" required> </td>
                            <td>
                            <select name="tipo_situacao" required>
                                <option value="1">Parado</option>
                                <option value="2">Em andamento</option>
                                <option value="3">Concluído</option>
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




