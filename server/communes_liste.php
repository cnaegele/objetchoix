<?php
require_once 'gdt/cldbgoeland.php';
header("Access-Control-Allow-Origin: *");
$dbgo = new DBGoeland();
$sSql = 'cn_thistreet_commune_liste';
//echo '{"message": "' . str_replace('"', '\"', $sSql) . '"}';
$bret = $dbgo->queryRetJson2($sSql);
if ($bret === true) {
    echo $dbgo->resString;
} else {
    echo '{"message" : "cn_thistreet_commune_liste:' . $dbgo->resErreur . '"}';
}
unset($dbgo);

