<?php
require_once 'gdt/cldbgoeland.php';
header("Access-Control-Allow-Origin: *");
$idCommune = '632';
if (isset($_GET['jsoncriteres'])) {
    $jsonCriteres = $_GET['jsoncriteres'];
    $oCriteres = json_decode($jsonCriteres, false);
    if (isset($oCriteres->idcommune)) {
        $idCommune = $oCriteres->idcommune;
    }
}
$dbgo = new DBGoeland();
$bret = $dbgo->queryRetJson2("cn_thistreet_liste $idCommune");
if ($bret === true) {
    echo $dbgo->resString;
} else {
    http_response_code(400);
    echo 'cn_thistreet_liste:' . $dbgo->resErreur;
}
unset($dbgo);
