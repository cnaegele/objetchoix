<?php
require_once 'gdt/cldbgoeland.php';
header("Access-Control-Allow-Origin: *");
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
        if (isset($oCriteres->crtype)) {
            $crType = $oCriteres->crtype;
            switch ($crType) {
                case 'nom':
                    $sSql = 'cn_batiment_liste_parnom';
                    break;
                case 'eca':
                    $sSql = 'cn_batiment_liste_pareca';
                    break;
                case 'egid':
                    $sSql = 'cn_batiment_liste_paregid';
                    if (ctype_digit((string)$critere) || is_int($critere)) {
                        if ($critere < 0 || $critere > 999999999) {
                            $bParamsOk = false;
                            $msgErreur = 'parametre egid hors limite';
                        }
                    } else {
                        $bParamsOk = false;
                        $msgErreur = 'paramètre egid non numérique';
                    }
                    break;
                default:
                    $bParamsOk = false;
                    $msgErreur = 'paramètre crtype inconnu';
            }
        } else {
            $bParamsOk = false;
            $msgErreur = 'paramètre crtype manquant';
        }
    }
} else {
    $bParamsOk = false;
    $msgErreur = 'GET jsoncriteres manquant';
}
if ($bParamsOk) {
    $critere = str_replace("'", "''", $critere);
    $critere = str_replace("*", "%", $critere);
    if ($crType == 'nom') {
        if (substr($critere, -1, 1) != '%') {
            $critere = $critere . '%';
        }
        if (substr($critere, 0, 1) != '%') {
            $critere = '%' . $critere;
        }
    }
    if ($crType == 'egid') {
        $sSql .= " $critere";
    } else {
        $sSql .= " '$critere', $nombreMaximumRetour";
    }
    //echo '{"message": "' . str_replace('"', '\"', $sSql) . '"}';
    $dbgo = new DBGoeland();
    $bret = $dbgo->queryRetJson2($sSql);
    if ($bret === true) {
        echo $dbgo->resString;
    } else {
        $msgErreur = 'cn_batiment_liste: ' . $dbgo->resErreur;
    }
    unset($dbgo);
}
if ($msgErreur != '') {
    http_response_code(400);
    echo $msgErreur;
}
