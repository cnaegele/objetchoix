<?php
require_once 'gdt/cldbgoeland.php';
header("Access-Control-Allow-Origin: *");
$bParamsOk = true;
$msgParamsKO = '';
if (isset($_GET['jsoncriteres'])) {
    $jsonCriteres = $_GET['jsoncriteres'];
    $oCriteres = json_decode($jsonCriteres, false);
    if (isset($oCriteres->crtype)) {
        $crtype = $oCriteres->crtype;
        if ($crtype != 'nom' && $crtype != 'nomcomplet') {
            $bParamsOk = false;
            $msgParamsKO .= ' crtype invalide';
        }
    } else {
        $bParamsOk = false;
        $msgParamsKO .= ' crtype manquant';
    }
    $critereRue = '';
    if (isset($oCriteres->critererue)) {
        $critereRue = $oCriteres->critererue;
        $critereRue = str_replace("'", "''", $critereRue);
    }
    $critereNumero = '';
    if (isset($oCriteres->criterenumero)) {
        $critereNumero = $oCriteres->criterenumero;
        if ($critereNumero !== '') {
            if (ctype_digit($critereNumero) || is_int($critereNumero)) {
                if ($critereNumero < 0 || $critereNumero > 9999) {
                    $bParamsOk = false;
                    $msgParamsKO .= ' criterenumero hors limite';
                }
            } else {
                $bParamsOk = false;
                $msgParamsKO .= ' criterenumero non num&eacute;rique';
            }
        }
    }
    $critereNumeroExt = '';
    if (isset($oCriteres->criterenumeroext)) {
        $critereNumeroExt = $oCriteres->criterenumeroext;
        $critereNumeroExt = str_replace("'", "''", $critereNumeroExt);
    }
    $nombreMaximumRetour = '';
    if (isset($oCriteres->nombremaximumretour)) {
        $nombreMaximumRetour = $oCriteres->nombremaximumretour;
    }
} else {
    $bParamsOk = false;
    $msgParamsKO .= ' param&egrave;tres json manquant';
}
if ($bParamsOk) {
    $bnomcomplet = '0';
    if ($crtype == 'nomcomplet') {
        $bnomcomplet = '1';
    }
    $sSql = "cn_adresse_liste $bnomcomplet";
    if ($critereRue != '') {
        $critereRue = str_replace("*", "%", $critereRue);
        if (substr($critereRue, -1, 1) != '%') {
            $critereRue = $critereRue . '%';
        }
        if ($bnomcomplet == 1) {
            if (substr($critereRue, 0, 1) != '%') {
                $critereRue = '%' . $critereRue;
            }
        }
        $sSql .= ",'$critereRue'";
    } else {
        $sSql .= ",NULL";
    }
    if ($critereNumero != '') {
        $sSql .= ",$critereNumero";
    } else {
        $sSql .= ",NULL";
    }
    if ($critereNumeroExt != '') {
        $sSql .= ",'$critereNumeroExt'";
    } else {
        $sSql .= ",NULL";
    }
    if ($nombreMaximumRetour != '') {
        $sSql .= ",$nombreMaximumRetour";
    } else {
        $sSql .= ",NULL";
    }
    //echo '{"message": "' . str_replace('"', '\"', $sSql) . '"}';
    $dbgo = new DBGoeland();
    $bret = $dbgo->queryRetJson2($sSql);
    if ($bret === true) {
        echo $dbgo->resString;
        unset($dbgo);
    } else {
        echo '{"message" : "adresse_liste:' . $dbgo->resErreur . '"}';
        unset($dbgo);
    }
} else {
    echo '{"message" : "adresse_liste:param&egrave;tres invalides' . $msgParamsKO . '"}';;
}
