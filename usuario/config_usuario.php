
<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	if ( isset($_SESSION['usu_id']) )
	{
		$usu_id = $_SESSION['usu_id'];
		$usu_nome = $_SESSION['usu_nome'];
	}
	else
	{
		$index_url = '../index.php';
		header('Location: ' . $index_url);
	}
?>

<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <title>Configurar Usuários</title>

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    
    <link rel="stylesheet" type="text/css" href="../css/main.css" />
    <link rel="stylesheet" type="text/css" href="../css/config_tarefas.css" />
    
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
    <script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
    <link rel="stylesheet" href="/resources/demos/style.css" />
	
    <script type="text/javascript" src="../js/table_row.js"></script>

	</head> 

<body>
	<div id="container-cabecalho">
    <header>
        <div id="nome_usuario" class="menu_acesso_rapido" >
            <a href="../home/home.php"> <?php echo ( $_SESSION['usu_nome'] ) ?> </a> / <?php echo '<a href="../home/home.php"> Home </a> '; ?>
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
			
        $query = 'SELECT `usu_id`, `usu_nickname`, `usu_nome`, `usu_email`, `usu_senha`, `usu_dt_cadastro`, `usu_foto` FROM `usuario` WHERE `usu_id` = %s'
                 or die ('Erro ao contruir a consulta');                
                 
		// alimenta os parametros da conculta
		$query = sprintf($query, $usu_id ); 	
		
		// executa consulta
		$master_data = mysqli_query($dbc, $query);
		
		// captura os dadas deste registro
		$dados_usuario = mysqli_fetch_array($master_data);
	
		/* Fecha conexão com o banco */
		mysqli_close($dbc);
		
		echo '<div id="menu_perfil">
            <table id="dados">
            <tr>
            
            <td rowspan="5">';
            
            if (empty($row['usu_foto'])) {
                echo '<img src="../nopic.jpg" alt="Profile Picture" />';
            }
            
            echo '</td>
            </tr>
            <tr> <td> <br> </td> </tr>
            
            <tr> <td> <strong class="nome_titulo">', $dados_usuario['usu_nome'], '</strong> </td> </tr>
			<tr> <td> <strong> Nickname: </strong>', $dados_usuario['usu_nickname'], '</td> </tr>
            <tr> <td> <strong> E-mail: </strong>', $dados_usuario['usu_email'], '</td> </tr>
            <tr> <td> <br> </td> </tr>
            </table>
            
        </div>';
		
		// volta o ponteiro da consulta que possui os dados do usuario logado (master)
		mysqli_data_seek($master_data, 0);
		
 		?>
        
            
        <div id="usuarios" class="info">   
            <p class="label_titulo" > Usuários </p>	
            <div = class="css_colaboradores_usuarios">
                
            <?php	
				// conectar ao banco de dados
				$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
					die('Erro ao conectar ao BD!');
					
				mysqli_select_db($dbc, "easykanban-bd")
					or die ('Erro ao selecionar o Banco de Dados');
					
				$query = 'SELECT `usu_id`, `usu_nickname`, `usu_nome`, `usu_email`, `usu_senha`, `usu_dt_cadastro`, `usu_foto` FROM `usuario` WHERE not (`usu_id` =  \'$usu_id\' )'
				or die ('Erro ao contruir a consulta');                
				
				// executa consulta
				$data = mysqli_query($dbc, $query);
			
				/* Fecha conexão com o banco */
				mysqli_close($dbc);
				
                echo '<table class="tabela_zebrada" width="100%" > 
                <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Nickname</th>
					<th>Data/Hora de Cadastro</th>
                    <th>Apagar</th>
                </tr>
                </thead> ';
                 
                while ($row = mysqli_fetch_array($data))
                {
                    echo '<tr> 
                          <td>', $row['usu_nome'], '</td>
                          
                          <td>', $row['usu_email'], '</td> 
                          
                          <td>', $row['usu_nickname'], '</td>
						  
						  <td>', $row['usu_dt_cadastro'], '</td>';
 
                    echo '<td align="center">
                            <a href="inserir_remover_usuario.php?usu_id=', $row['usu_id'], '&action=deletar_usuario"> <img src="../images/del.png" title="Remover"/> </a>
                          </td>';
                                
                }
                
                echo '</table>';
                    
            ?>    
               
            </div>
        </div> 
        
 	</div>
    
</body>
</html>