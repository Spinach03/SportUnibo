<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || !isAdmin()){
    header("Location: ../login.php");
    exit;
}

$templateParams["titolo"] = "Campus Sports - Gestione Campi";
$templateParams["titolo_pagina"] = "Gestione Campi";
$templateParams["nome"] = "gestione-campi.php";

require 'template/base.php';
?>
