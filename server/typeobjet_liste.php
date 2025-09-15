<?php
require_once 'gdt/cldbgoeland.php';
require 'gdt/gautentificationf5.php';
header("Access-Control-Allow-Origin: *");

$idCaller = 6;
//if (array_key_exists('empid', $_SESSION)) {
//    $idCaller = $_SESSION['empid'];
//}

$sSql = "cn_typething_listechoix $idCaller";
$dbgo = new DBGoeland();
$dbgo->queryRetJson2($sSql);
if ($dbgo->resErreur != '') {
    http_response_code(400);
    echo $dbgo->resErreur;
} else {
    echo $dbgo->resString;
}
unset($dbgo);
