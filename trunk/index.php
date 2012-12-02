<?php
	require_once('connect/connect_vars.php');
	
	// Inicia a sessão
	session_start();
	
	// limpa a variável de erros
	$error_msg = "";
	
	// Se o usuário não estiver logado, é oferecida a tela de login
	if (!isset($_SESSION['usu_id'])) 
	{
		if (isset($_POST['submit'])) 
		{
			// conecta com o banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			
			// coleta os dados digirtados pelo usuário
			$usu_email = mysqli_real_escape_string($dbc, trim($_POST['email']));
			$usu_senha = mysqli_real_escape_string($dbc, trim($_POST['senha']));
			
			if (!empty($usu_email) && !empty($usu_senha)) 
			{
				// procura pelo usuário e senha digitados pelo usuário no banco de dados
				$query = "SELECT usu_id, usu_nome, usu_email FROM usuario WHERE usu_email = '$usu_email' AND usu_senha = SHA('$usu_senha')" 
					or die ('Erro na consulta');
					
				$data = mysqli_query($dbc, $query);
				
				$result = mysqli_num_rows($data);

				if ( $result == 1) 
				{	
					// O log-in está OK, então configuramos as variáveis de sessão e cookies do usu_id e usu_nome e redirecionamo o usuário para a página de abertura
					$row = mysqli_fetch_array($data);
					$_SESSION['usu_id'] = $row['usu_id'];
					$_SESSION['usu_nome'] = $row['usu_nome'];
					setcookie('usu_id', $row['usu_id'], time() + (60 * 60 * 24 * 30));    // expira em 30 dias
					setcookie('usu_nome', $row['usu_nome'], time() + (60 * 60 * 24 * 30));  // expira em 30 dias
					
					
					// tip_id = 3 é o usuario master
					$query = "SELECT tp.tip_id FROM usuario_tipo tp WHERE tp.usu_id =" . $row['usu_id'] . " AND tp.tip_id = 3" or die('Erro na consulta');
					$master_data = mysqli_query($dbc, $query);
					$result = mysqli_num_rows($master_data);
					
					if ( $result == 1 ) //usuario master logado
					{
						$master_row = mysqli_fetch_array($master_data);
						$_SESSION['tip_id'] = $master_row['tip_id'];
						setcookie('tip_id', $row['tip_id'], time() + (60 * 60 * 24 * 30));  // expira em 30 dias
					}
					
					$home_url = 'home/home.php';
					header('Location: ' . $home_url);
					
				}
				else {
					// O nome de usuário e/ou a senha estão incorretos 
					$error_msg = 'Entre com um email e/ou senha válidos.';
				}
			}
			else {
				// O nome de usuário e/ou senha não estão cadastrados 
				$error_msg = 'Por favor, você deve preencher os campos de email e senha';
			}
			
			// encerra conexão com o banco de dados */
			mysqli_close($dbc);
		}
	}
?>

<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <title>easykanban</title>
     <link rel="stylesheet" type="text/css" href="css/main.css" />
    <link rel="stylesheet" type="text/css" href="index.css" />
  </head> 

  <body>
    <div id="container-cabecalho">
        <header>

        </header>
    </div>
        
    <div id="container-menu">
        <ul>
            <li class="atual"> <a href=""> Home </a></li>
            <li><a href="projeto.php"> Contato </a></li>
            <li><a href="#"> Sobre </a></li>
            <li><a href="#"> Ajuda </a></li>
        </ul>
        <br style="clear:left"/>
    </div>
    
	<div id="allcontent">
    
    <div id="main">
        <article>
            <section>
                <h2>article section h2</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sodales urna non odio egestas tempor. Nunc vel vehicula ante. Etiam bibendum iaculis libero, eget molestie nisl pharetra in. In semper consequat est, eu porta velit mollis nec. Curabitur posuere enim eget turpis feugiat tempor. Etiam ullamcorper lorem dapibus velit suscipit ultrices. Proin in est sed erat facilisis pharetra.</p>
            </section>
            <section>
                <h2>article section h2</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sodales urna non odio egestas tempor. Nunc vel vehicula ante. Etiam bibendum iaculis libero, eget molestie nisl pharetra in. In semper consequat est, eu porta velit mollis nec. Curabitur posuere enim eget turpis feugiat tempor. Etiam ullamcorper lorem dapibus velit suscipit ultrices. Proin in est sed erat facilisis pharetra.</p>
            </section>
    
        </article>
    </div>

    <div id="sidebar">

        <div id="campos_login" class="centro">
            
			<?php
                // If the session var is empty, show any error message and the log-in form; otherwise confirm the log-in
                if (empty($_SESSION['usu_id'])) {
                	echo '<p style="color: red" class="error">' . $error_msg . '</p>';
            ?>
            
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <table>
            	<td> <tr> <h3 align="center">Bem-Vindo <strong></strong></h3> </tr> </td> <br />
                <td> <tr> <label for="email"> <strong> Email: </strong> </label> </tr>  </td>
                <tr>  <input type="email" id="email" name="email" required> </tr>  <br /><br />
        
                <td> <tr> <label for="senha"> <strong> Senha: </strong> </label> </tr> </td>
                <td> <tr> <input type="password" id="senha" name="senha" required></tr> </td> <br /> <br />
                
                <td> <tr> <input class="blue_button" type="submit" value="Log In" name="submit" /> </tr> </td> <br /> 
            </table>
            
            </form>
			<p> Esqueceu sua senha? <a href="index.html">Clique aqui! </a> </p>
             
         </div>
        
    </div>
    
    </div>
    
    <?php
      }
      else {
        // Confirm the successful log-in
        echo('<p class="login">You are logged in as ' . $_SESSION['usu_nome'] . '.</p>');
      }
    ?>
    
    <div id="footer">
      &copy; 2012, easykanban<br/>
      Contato: <strong> eakykanban@gmail.com </strong>
      <br />
      Todas as marcas e marcas registradas que aparecem neste site pertencem a seus respectivos proprietários.
    </div>
    
  </body>
</html>




