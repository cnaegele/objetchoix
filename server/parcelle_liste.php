<?php
require_once 'gdt/cldbgoeland.php';
header("Access-Control-Allow-Origin: *");
$bParamsOk = true;
$msgParamsKO = '';
if (isset($_GET['jsoncriteres'])) {
    $jsonCriteres = $_GET['jsoncriteres'];
    $oCriteres = json_decode($jsonCriteres, false);
    $idCommune = 5586;
    if (isset($oCriteres->idcommune)) {
        $idCommune = $oCriteres->idcommune;
    }
    $critere = '';
    $nombreMaximumRetour = '100';
    $crType = '';
    if (isset($oCriteres->critere)) {
        $critere = $oCriteres->critere;
    } else {
        $bParamsOk = false;
        $msgParamsKO .= ' critere manquant';
    }
    if ($bParamsOk) {
        $nombreMaximumRetour = '100';
        if (isset($oCriteres->nombremaximumretour)) {
            $nombreMaximumRetour = $oCriteres->nombremaximumretour;
            if (filter_var($nombreMaximumRetour, FILTER_VALIDATE_INT, [
                    'options' => [
                        'min_range' => 0,
                        'max_range' => 999999
                    ]
                ]) === false) {
                $nombreMaximumRetour = '100';
            }
        }
        if (isset($oCriteres->crtype)) {
            $crType = $oCriteres->crtype;
            switch ($crType) {
                case 'numero':
                    $sSql = 'cn_parcelle_liste_parnumero';
                    break;
                case 'eca':
                    $sSql = 'cn_parcelle_liste_pareca';
                    break;
                case 'egrid':
                    $sSql = 'cn_parcelle_liste_paregrid';
                    break;
                default:
                    $bParamsOk = false;
                    $msgParamsKO .= ' crtype inconnu';
            }
        } else {
            $bParamsOk = false;
            $msgParamsKO .= ' crtype manquant';
        }
    }
} else {
    $bParamsOk = false;
    $msgParamsKO .= ' paramètres json manquant';
}
if ($bParamsOk) {
    $critere = str_replace("'", "''", $critere);
    $critere = str_replace("*", "%", $critere);
    $sSql .= " '$critere', $nombreMaximumRetour";
    if ($crType != 'egrid') {
        $sSql .= ", $idCommune";
    }
    //echo '{"message": "' . str_replace('"', '\"', $sSql) . '"}';
    $dbgo = new DBGoeland();
    $bret = $dbgo->queryRetJson2($sSql);
    if ($bret === true) {
        echo $dbgo->resString;
        unset($dbgo);
    } else {
        echo '{"message" : "cn_parcelle_liste:' . $dbgo->resErreur . '"}';
        unset($dbgo);
    }
} else {
    echo '{"message" : "cn_parcelle_liste:paramètres invalides' . $msgParamsKO . '"}';;
}
