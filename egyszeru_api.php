<?php

$protocol=$_SERVER['PROTOCOL'] = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http';
$sajat_cim = $protocol.'://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['PHP_SELF'];

$kliens_app = $_SERVER['HTTP_USER_AGENT'];

$leiras = '
A php tudja kezelni alapértelmezetten a get és post kéréseket

 a get kérések változói a $_GET változóban vannak, ami egy tömb
 a post kérések változói a  $_POST változóba kerülnek, ami szintén tömb

Próbáld ki:

';

$probald_ki_url = $sajat_cim.'?egyvaltozo=valami';
$curl_parancs = "curl $sajat_cim?egyvaltozo=valami";

$method = $_SERVER['REQUEST_METHOD'] . ' kapcsolódási módot használtál.';

# Innentől kezdve történik a kliens kérésére adott válasz összerakása


# Ha curl vagy http klienst használunk
if (preg_match('/^curl/',$kliens_app) or preg_match('/^httpie/i',$kliens_app))
{
    # akkor JSON állományként adjuk vissza a választ
    echo json_encode(array('leiras'=>$leiras,'probald_ki_url'=>$probald_ki_url,'method'=>$method,'parameterek'=>$_REQUEST,'curl_parancs_pelda'=>$curl_parancs));

} else {
    # ha böngészőt (vagy bármi egyebet), akkor html oldalt kapunk
    if (count($_REQUEST)) {
        $text = "A következő url paramétereket küldted:<br>";
        $text .= "<div style='display:table'>";
        foreach ($_REQUEST as $key=>$val) {
            $text .= "<div style='display:table-row'>";
            $text .= "<div style='display:table-cell;color:red'>$key &nbsp;</div>";
            $text .= "<div style='display:table-cell;color:gray'>&nbsp; = &nbsp;</div>";
            $text .= "<div style='display:table-cell;color:green'>$val </div>";
            $text .= "</div>";
        }
        $text .= "</div>";
    }
    echo "<pre>$leiras<a href='$probald_ki_url'>$probald_ki_url</a></pre>".$text."<br>".$method."<br><br><i>curl parancs példa:</i> ".$curl_parancs;
}
?>
