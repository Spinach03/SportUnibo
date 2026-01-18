<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || !isAdmin()){
    header("Location: ../login.php");
    exit;
}

$templateParams["titolo"] = "Campus Sports - Dashboard";
$templateParams["titolo_pagina"] = "Dashboard Overview";
$templateParams["nome"] = "dashboard.php";

require 'template/base.php';
?>
