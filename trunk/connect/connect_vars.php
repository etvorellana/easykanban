<?php
  header( 'Content-Type: text/html; charset=ISO-8859-1' );
	
  // Definição de constantes para conexão com o Banco de Dados
  define('DB_HOST', 'localhost');
  define('DB_USER', 'root');
  define('DB_PASSWORD', 'root');
  define('DB_NAME', 'easykanban-bd');
  
  define('BACKLOG', '1');
  define('REQUISITADO', '2');
  define('EM PROCESSO', '3');
  define('CONCLUIDO', '4');
  define('ARQUIVADO', '5');
 
  define('ADMIN', '1');
  define('COLABORADOR', '2');
?>
