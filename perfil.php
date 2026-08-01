<?php
    require_once __DIR__ . '/src/bootstrap/session.php';
    require 'functions.php';
    session_start();
    comprobar_session_admin('views/perfilView.php');
?>
    