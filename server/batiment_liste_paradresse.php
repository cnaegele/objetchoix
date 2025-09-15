<?php
require_once 'gdt/cldbgoeland.php';
header("Access-Control-Allow-Origin: *");
$bParamsOk = true;
$msgErreur = '';
if (isset($_GET['jsoncriteres'])) {
    $jsonCriteres = $_GET['jsoncriteres'];
    $oCriteres = json_decode($jsonCriteres, false);
    if (isset($oCriteres->idadresse)) {
        $idAdresse = $oCriteres->idadresse;
        if (ctype_digit((string)$idAdresse) || is_int($idAdresse)) {
            if ($idAdresse < 0 || $idAdresse > 9999999) {
                $bParamsOk = false;
                $msgErreur = 'paramètre idadresse hors limite';
            }
        } else {
            $bParamsOk = false;
            $msgErreur = 'paramètre idadresse non numérique';
        }
    } else {
        $bParamsOk = false;
        $msgErreur = 'paramètre idadresse manquant';
    }
    $bAnnexe = '1';
    if (isset($oCriteres->bannexe)) {
        $bAnnexe = strval($oCriteres->bannexe);
        if ($bAnnexe != '1' && $bAnnexe != '0') {
            $bParamsOk = false;
            $msgErreur = 'paramètre bannexe invalide';
        }
    }
    $typeRetourSP = '';
    if (isset($oCriteres->typeretoursp)) {
        $typeRetourSP = $oCriteres->typeretoursp;
    }
} else {
    $bParamsOk = false;
    $msgErreur = 'GET jsoncriteres manquant';
}
if ($bParamsOk) {
    $sSql = "cn_batiment_liste_paradresse $idAdresse, $bAnnexe, '$typeRetourSP'";
    //echo '{"message": "' . str_replace('"', '\"', $sSql) . '"}';
    $dbgo = new DBGoeland();
    $bret = $dbgo->queryRetJson2($sSql);
    if ($bret === true) {
        echo $dbgo->resString;
    } else {
        $msgErreur =  'cn_batiment_liste_paradresse: ' . $dbgo->resErreur;
    }
    unset($dbgo);
}
if ($msgErreur != '') {
    http_response_code(400);
    echo $msgErreur;
}
