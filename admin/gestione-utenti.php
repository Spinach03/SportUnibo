<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || !isAdmin()){
    header("Location: ../login.php");
    exit;
}

$templateParams["titolo"] = "Campus Sports - Gestione Utenti";
$templateParams["titolo_pagina"] = "Gestione Utenti";
$templateParams["nome"] = "gestione-utenti.php";

require 'template/base.php';
?>
