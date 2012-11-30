
<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	if ( isset($_SESSION['usu_id']) and isset($_GET['pro_id']) and isset($_GET['tip_id']) )
	{
		$usu_id = $_SESSION['usu_id'];
		$usu_nome = $_SESSION['usu_nome'];
		$pro_id = $_GET['pro_id'];
		$permissao = $_GET['tip_id'];
		
				// Quando o usuário submeter os dados de cadastro de nova empresa
		if (isset($_POST['edit'])) 
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
			mysqli_select_db($dbc, "easykanban-bd")
				or die ('Erro ao selecionar o Banco de Dados');
			
			// recupera os dados digitados no formulário
			$pro_nome = trim ($_POST['nome']);	
			$pro_descrição = trim($_POST['descricao']);
			$data_fim = trim($_POST['data_fim']);
			$tip_situacao = trim ($_POST['tipo_situacao']);	

			if ( !empty($pro_nome) && !empty($pro_descrição) && !empty($data_fim) && !empty($tip_situacao) )
			{
				$por_usu_criador = $usu_id;
				
				// criando query de inserção na tabela projeto
				$query = "UPDATE projeto SET
						  tip_id = '$tip_situacao',
			              pro_nome = '$pro_nome',
			              pro_descricao= '$pro_descrição',
			              pro_dt_fim = '$data_fim'
						  WHERE pro_id = '$pro_id' "
				or die ('Erro ao contruir a consulta');
				
				// alimenta os parametros da conculta
				$query = sprintf($query, $tip_situacao, $pro_nome, $pro_descrição, $data_fim, $pro_id );	
				
				//execulta query de inserção na tabela cep
				$data = mysqli_query($dbc, $query)
					or die('Erro ao execultar a inserção na tabela projeto');
				
			}
				
			/* Fecha conexão com o banco */
			mysqli_close($dbc);
		}
	}
	


?>

<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <title>Gerência de Tarefas</title>

    <link rel="stylesheet" type="text/css" media="all" href="../css/formulario.css">
    <link rel="stylesheet" type="text/css" media="all" href="../fancybox/jquery.fancybox.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.0.6"></script>
    
    <link rel="stylesheet" type="text/css" href="../css/main.css" />
    <link rel="stylesheet" type="text/css" href="../css/config_tarefas.css" />
  	
    <script type="text/javascript" src="../js/table_row.js"></script>
    	
    <script type="text/javascript">
		// função
		function getTarId( tar_id ) {  				
			//alert(String(tar_id));
			var tar_id_edit = tar_id;
						
		}  
    </script>
    


	</head> 

<body>
	<div id="container-cabecalho">
    <header>
		<div id="nome_usuario" class="menu_acesso_rapido">
        	<label> <?php echo ( $usu_nome ) ?> </label>
    	</div>
    	
        <div id="logout" class="config_logout">
        	<label > <a class="menu_acesso_rapido" href="../logout.php"> logout </a> </label>
        </div>
    </header>
    </div>
    
	<div id="container_voltar">
        <a id="bug" href="candidato.php?pro_id=<?php echo $pro_id, '&tip_id=', $permissao ?> "> 
        	<input class="purple_button" type="submit" value="Voltar ao Quadro" > 
        </a>
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
			
			
        $query = 'SELECT t.`tar_id`, t.`tip_t_id` , t.`pri_id` , t.`met_id` , t.`sit_id` , t.`pro_id` , t.`tar_titulo` , t.`tar_descricao` , t.`tar_comentario` , t.`tar_data_inicio` , t.`tar_data_conclusao` , t.`tar_tempo_estimado` , t.`tar_data_criacao` , u.`usu_nome` , s.`sit_descricao` , p.`pro_id`, p.`pro_nome`, p.`pro_descricao`, p.`pro_dt_inicio`, p.`pro_dt_fim`, p.`pro_dt_criacao`, p.`pro_usu_criador`
				FROM  `tarefa` AS t
				JOIN  `projeto` p ON p.`pro_id` = t.`pro_id` 
				JOIN  `responsavel` r ON r.`tar_id` = t.`tar_id` 
				JOIN  `usuario` u ON u.`usu_id` = r.`usu_id` 
				JOIN  `situacao` s ON s.`sit_id` = t.`sit_id` 
				WHERE p.`pro_id` =%s '
                 or die ('Erro ao contruir a consulta');                
                 
        // alimenta os parametros da conculta
        $query = sprintf($query, $pro_id ); 	
        
        // executa consulta
        $data = mysqli_query($dbc, $query) or die ('Erro ao executar consulta');
    
		$num_tarefas = mysqli_num_rows($data);

        $row = mysqli_fetch_array($data);
		
		echo '<div class="projeto_info_hover" id="menu_perfil">
			  <table width="100%" class="border_space">';
		
		
		echo '<tr> <td> <strong class="nome_titulo">', $row['pro_nome'], '</strong> </td> </tr>
			  <tr> <td>  <strong> Descrição: </strong>', $row['pro_descricao'], '</td> </tr>';
		  
		echo '<tr> <td width="60%"> <strong> Data de Início: </strong>', $row['pro_dt_inicio'], '</td>
			  <td> <strong> Previsão de Término:   </strong>', $row['pro_dt_fim'], '</td> </tr>';
		
		echo '<tr> <td> <strong> Número de Tarefas: </strong>', $num_tarefas, '</td>
		<td> <strong> Sua Função: </strong>'; if ( $row['pro_usu_criador'] == $usu_id ) echo'Criador/Administradar'; else if ($row['tip_id'] == 1 ) echo'Administrador'; else echo'Colaborador'; echo '</td> </tr>';
	
		echo '</table>
		
			  <div id="botoes_tarefas">
				<a id="botao_editar" class="modalbox" href="#inline" > Editar Quadro </a>
			  </div>
		
		</div>';
        
		// fecha conexão com o banco
        mysqli_close($dbc);
 ?>

        <div id="usuarios" class="info">   
            <strong class="label_titulo" > Tarefas </strong>	
            <div = class="css_colaboradores_usuarios">
            
<?php	
		echo '<table class="tabela_zebrada" width="100%" > 
		<thead>
		<tr>
			<th>Nome da Tarefa</th>
			<th>Responsável</th>
			<th>Status</th>
			<th>Editar</th>
			<th>Apagar</th>
		</tr>
		</thead> ';
		 
		do 
		{
			echo '<tr> 
				  <td>', $row['tar_titulo'], '</td>
				  
				  <td>', $row['usu_nome'], '</td> 
				  
				  <td>', $row['sit_descricao'], '</td>';
			
			echo '<td align="center">
					<a href="editar_tarefas.php?tar_id=', $row['tar_id'], '&pro_id=', $row['pro_id'], '&tip_id=', $permissao, '"> <img src="../images/edit_button.png" title="Editar"/> </a>
				  </td>';
							
			echo '<td align="center">
					<a href="inserir_remover_tarefas.php?tar_id=', $row['tar_id'], "&pro_id=", $pro_id, '&action=remove"> <img src="../images/del.png" title="Remover"/> </a>
				  </td>';
						
		}while ($row = mysqli_fetch_array($data));
		
		echo '</table>';
		
		
?>    
           
            </div>
        </div>
    </div>
    
    <?php 
		include_once('edit_tarefa.php');
	?>
	
</body>
</html>