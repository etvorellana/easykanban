<?php
  session_start();

  // If the session vars aren't set, try to set them with a cookie
  if (!isset($_SESSION['usu_id'])) {
    if (isset($_COOKIE['usu_id']) && isset($_COOKIE['usu_nome']) && isset($_COOKIE['tip_id']) ) {
      $_SESSION['usu_id'] = $_COOKIE['usu_id'];
      $_SESSION['usu_nome'] = $_COOKIE['usu_nome'];
	  $_SESSION['tip_id'] = $_COOKIE['tip_id'];
    }
  }
?>
