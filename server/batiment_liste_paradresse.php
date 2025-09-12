<?php
require_once 'gdt/cldbgoeland.php';
header("Access-Control-Allow-Origin: *");
$bParamsOk = true;
$msgParamsKO = '';
if (isset($_GET['jsoncriteres'])) {
    $jsonCriteres = $_GET['jsoncriteres'];
    $oCriteres = json_decode($jsonCriteres, false);
    if (isset($oCriteres->idadresse)) {
        $idAdresse = $oCriteres->idadresse;
        if (ctype_digit((string)$idAdresse) || is_int($idAdresse)) {
            if ($idAdresse < 0 || $idAdresse > 9999999) {
                $bParamsOk = false;
                $msgParamsKO .= ' idadresse hors limite';
            }
        } else {
            $bParamsOk = false;
            $msgParamsKO .= ' idadresse non numérique';
        }
    } else {
        $bParamsOk = false;
        $msgParamsKO .= ' idadresse manquant';
    }
    $bAnnexe = '1';
    if (isset($oCriteres->bannexe)) {
        $bAnnexe = strval($oCriteres->bannexe);
        if ($bAnnexe != '1' && $bAnnexe != '0') {
            $bParamsOk = false;
            $msgParamsKO .= ' bannexe invalide';
        }
    }
    $typeRetourSP = '';
    if (isset($oCriteres->typeretoursp)) {
        $typeRetourSP = $oCriteres->typeretoursp;
    }
} else {
    $bParamsOk = false;
    $msgParamsKO .= ' paramètres json manquant';
}
if ($bParamsOk) {
    $sSql = "cn_batiment_liste_paradresse $idAdresse, $bAnnexe, '$typeRetourSP'";
    //echo '{"message": "' . str_replace('"', '\"', $sSql) . '"}';
    $dbgo = new DBGoeland();
    $bret = $dbgo->queryRetJson2($sSql);
    if ($bret === true) {
        echo $dbgo->resString;
        unset($dbgo);
    } else {
        echo '{"message" : "cn_batiment_liste_paradresse:' . $dbgo->resErreur . '"}';
        unset($dbgo);
    }
} else {
    echo '{"message" : "cn_batiment_liste_paradresse:paramètres invalides' . $msgParamsKO . '"}';;
}
