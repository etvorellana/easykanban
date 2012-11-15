<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	$usu_id =  $_SESSION['usu_id'];
	
	// se a sessão for válida
	if (isset($_SESSION['usu_id'])) 
	{
		if (isset($_POST['submit'])) 
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
			$nome = trim ($_POST['nome']);	
			$email = trim($_POST['email']);
			$senha = trim($_POST['senha']);
			$tipo = trim($_POST['tipo']);
			
			if ( !empty($nome) && !empty($email) && !empty($senha) && !empty($tipo) )
			{
				$query = "INSERT INTO usuario (tip_id, usu_nome, usu_email, usu_senha) VALUES ('$tipo', '$nome', '$email', SHA('$senha') )" or 
					die ('Erro ao contruir a consulta');
				
				$result = mysqli_query($dbc, $query)
					or die('Erro ao execultar a consulta');
					
				echo("Inserido com Sucesso!");
			}
				
			/* Fecha conexão com o banco */
			mysqli_close($dbc);
		}
	}
?>

<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <title>easykanban</title>
    <link rel="stylesheet" type="text/css" href="../css/main.css" />
    <link rel="stylesheet" type="text/css" href="../css/add_tarefa.css" />
    <link rel="stylesheet" type="text/css" href="../css/formulario.css" />
    
  </head> 

  <body>
	<div id="container-cabecalho">
    <header>
    
		<ul>
        <li> <a class="menu_acesso_rapido" href="#"> <?php echo ( $_SESSION['usu_nome']) ?> </a> </li>
    	</ul>
    	
        <div id="acessiobilidade">
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
        <br style="clear:left"/>
    </div>
        
    <div id="main">
    	<div id="formulario">
    	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
         <table>
         	<tr> <td>  <label class="negrito" >Nome completo:</label> </td> </tr>
            <tr> <td>  <input type="text" id="nome" name="nome" placeholder="Ex: Thalles Santos Silva" required>  </td> </tr>
            
            <tr>
            	<td>
                    <table>
                        <tr>
                            <td>  <label class="negrito" >Endereço de e-mail:</label> </td>
                            <td>  <label </label> </td>
                            <td>  <label class="negrito" >Confirmar e-mail:</label> </td>
                        </tr>
                        <tr>
                            <td> <input type="email" id="email" name="email" placeholder="Ex: thalles@easykanban.com" required> </td>
                            <td>  <label </label> </td>
                            <td> <input type="email" id="confirmar_email" name="confirmar_email" required oninput="check(this)"> </td>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table>
                        <tr>
                            <td> <label class="negrito" >Senha:</label> </td>
                            <td>  <label </label> </td>
                            <td> <label class="negrito" >Confirmar Senha:</label> </td>
                        </tr>
                        <tr>
                            <td> <input type="password" name="senha" placeholder="" required> </td>
                            <td>  <label </label> </td>
                            <td> <input type="password" name="confirmar_senha" placeholder="" required> </td>
                        </tr>
                    </table>
               </td>
            </tr>
            
         	<tr> <td>  <label class="negrito" >Tipo:</label> </td> </tr>
            <tr> <td>  <input type="text" id="tipo" name="tipo" placeholder="Nome do usuário" required>  </td> </tr>
            
            <tr>
            <td> <input type="submit" value="Cadastrar" name="submit" /> </td>
            </tr>
            
         </table>
         </form>
         
		<script>
        function check(input) {
          if (input.value != document.getElementById('email').value) {
			input.setCustomValidity('The two email addresses must match.');
          } else {
            // input is valid -- reset the error message
            input.setCustomValidity('');
          }
        }
        </script>
         
         </div>
    </div>
    


    <div id="footer">
      &copy; 2012, easykanban <br/>
      Contato: <strong> eakykanban@gmail.com </strong>
      <br />
      Todas as marcas e marcas registradas que aparecem neste site pertencem a seus respectivos proprietários.
    </div>
    
  </body>
</html>




