<?php
require_once 'gdt/cldbgoeland.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
$bParamsOk = true;
$msgErreur = '';
if (isset($_GET['jsoncriteres'])) {
    $jsonCriteres = $_GET['jsoncriteres'];
    $oCriteres = json_decode($jsonCriteres, false);
    if (isset($oCriteres->critere)) {
        $critere = $oCriteres->critere;
    } else {
        $bParamsOk = false;
        $msgErreur = 'paramètre critere manquant';
    }
    if ($bParamsOk) {
        $nombreMaximumRetour = '100';
        if (isset($oCriteres->nombremaximumretour)) {
            $nombreMaximumRetour = $oCriteres->nombremaximumretour;
            if (ctype_digit((string)$nombreMaximumRetour) || is_int($nombreMaximumRetour)) {
                if ($nombreMaximumRetour < 0 || $nombreMaximumRetour > 999999) {
                    $nombreMaximumRetour = '100';
                }
            } else {
                $nombreMaximumRetour = '100';
            }
        }

        $sSql = '';
        if (ctype_digit((string)$critere) || is_int($critere)) {
            if ($critere > 0 && $critere < 10000000) {
                $sSql = "cn_objet_liste_parid $critere";
            }
        } else {
            $idTypeObjet = 0;
            if (isset($oCriteres->idtype)) {
                $idType = $oCriteres->idtype;
            }
            if (ctype_digit((string)$idType) || is_int($idType)) {
                if ($idType > 0 && $idType < 10000000) {
                    $idTypeObjet = $idType;
                }
            }
            $sSql = "cn_objet_liste_parnom $idTypeObjet";
            $critere = str_replace("'", "''", $critere);
            $critere = str_replace("*", "%", $critere);
            if (substr($critere, -1, 1) != '%') {
                $critere = $critere . '%';
            }
            if (substr($critere, 0, 1) != '%') {
                $critere = '%' . $critere;
            }
            $sSql .= " , '$critere', $nombreMaximumRetour";
        }
        //echo '{"message": "' . str_replace('"', '\"', $sSql) . '"}';
        $dbgo = new DBGoeland();
        $bret = $dbgo->queryRetJson2($sSql);
        if ($bret === true) {
            echo $dbgo->resString;
        } else {
            $msgErreur = 'cn_objet_liste_parnom:' . $dbgo->resErreur;
        }
        unset($dbgo);
    }
} else {
    $bParamsOk = false;
    $msgErreur = 'GET jsoncriteres manquant';
}
if ($msgErreur != '') {
    http_response_code(400);
    echo $msgErreur;
}
