<?php
	require_once('../sessao_php/inicia_sessao.php');
?>

<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <title>easykanban</title>
    <link rel="stylesheet" type="text/css" href="../css/main.css" />
    <link rel="stylesheet" type="text/css" href="home.css" />
  </head> 

  <body>
	<div id="container-cabecalho">
    <header>
    
		<ul class="menu_acesso_rapido">
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
        <li><a href="#">Empresas</a></li>
        <li><a href="#">Relatórios</a></li>
        <li><a href="#">Configurações</a></li>
        </ul>
        <br style="clear:left"/>
        </div>
    <div id="main">

    </div>

    <div id="footer">
      &copy; 2012, easykanban <br/>
      Contato: <strong> eakykanban@gmail.com </strong>
      <br />
      Todas as marcas e marcas registradas que aparecem neste site pertencem a seus respectivos proprietários.
    </div>
    
  </body>
</html>




