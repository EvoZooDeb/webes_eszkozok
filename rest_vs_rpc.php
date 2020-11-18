<?php
## by Miklós Bán 2019.09.27
#
#
$ua = $_SERVER['HTTP_USER_AGENT'];

$response_type = 'html';
if (preg_match('/^curl/',$ua) or preg_match('/^httpie/i',$ua))
    $response_type = 'json';

$method = $_SERVER['REQUEST_METHOD'];

if (isset($_GET['rest'])) {

        $var = "";
        if (isset($_GET['var'])) $var = $_GET['var'];

	if ($method == 'GET') {
            if (isset($_GET['vars'])) {
                $text = "<div style='display:table'>";
		foreach ($_REQUEST as $key=>$val) {
		    $text .= "<div style='display:table-row'>";
		    $text .= "<div style='display:table-cell'>$key</div>";
		    $text .= "<div style='display:table-cell'>$val</div>";
		    $text .= "</div>";
		}
                $text .= "</div>";

                if ($response_type == 'html') {
                    echo resp($text);
                } else {
                    echo resp($_REQUEST);
                }
          } elseif (isset($_GET['help'])) {
              echo resp("curl -X GET http://172.18.157.172/l2wapi/?vars\&rest
		curl -X PUT -d 'foo' http://172.18.157.172/l2wapi/?vars\&rest
		curl -X POST -d 'foo=bar' http://172.18.157.172/l2wapi/?vars\&rest
		curl -X DELETE -d 'foo' http://172.18.157.172/l2wapi/?vars\&rest");
           } elseif (isset($_GET['response_type'])) {
                echo resp($response_type);
	   }
	} elseif ($method == 'PUT') {
	  if (isset($_GET['vars'])) {
		$putdata = fopen("php://input", "r");
                $text = "";
		while ($data = fread($putdata, 1024))
			$text .= $data."\n";
		fclose($putdata);
		echo resp("Variable content updated for var $var:\n".$text);
	  } elseif (isset($_GET['help'])) {
		$putdata = fopen("php://input", "r");
		$text = "";
		while ($data = fread($putdata, 1024))
			$text .= $data."\n";
		fclose($putdata);
            	echo resp("Help item updated for var $var:\n$text");
	  }
	} elseif ($method == 'POST') {

	  if (isset($_GET['vars'])) {
                ob_start();
                var_dump($_POST);
                $content = ob_get_contents();
                ob_end_clean();
                echo resp("New var created:\n".$content);

	  } elseif (isset($_GET['help'])) {
                ob_start();
                var_dump($_POST);
                $content = ob_get_contents();
                ob_end_clean();
		echo resp("Help content added to var $var:\n".$content);
	  }
	} elseif ($method == 'DELETE') {
	  if (isset($_GET['vars'])) {
		echo resp("Variable deleted: $var");
	  } elseif (isset($_GET['help'])) {
		echo resp("Help deleted for var $var");
	  }
	}

} elseif (isset($_GET['rpc'])) {
        $var = "";
        if (isset($_GET['var'])) $var = $_GET['var'];

	if (isset($_GET['vars'])) {
                $text = "<div style='display:table'>";
		foreach ($_REQUEST as $key=>$val) {
		    $text .= "<div style='display:table-row'>";
		    $text .= "<div style='display:table-cell'>$key</div>";
		    $text .= "<div style='display:table-cell'>$val</div>";
		    $text .= "</div>";
		}
                $text .= "</div>";

                if ($response_type == 'html') {
                    echo resp($text);
                } else {
                    echo resp($_REQUEST);
                }

	} elseif (isset($_GET['help'])) {
		echo resp("?vars
		?help<br>
		?help&var=
		?addvar&var=
		?addhelp&var=
		?deletevar&var=
		?deletehelp&var=
		?newvalue&var=");
        } elseif (isset($_GET['addvar'])) {
		echo resp("New variable added: $var");
        } elseif (isset($_GET['addhelp'])) {
            	echo resp("New help item created for var: $var");
        } elseif (isset($_GET['deletevar'])) {
		echo resp("Variable deleted: $var");
        } elseif (isset($_GET['deletehelp'])) {
		echo resp("Help deleted for var $var");
        } elseif (isset($_GET['newvalue'])) {
                ob_start();
                var_dump($_POST);
                $content = ob_get_contents();
                ob_end_clean();
                echo resp("New values posted to $var\n".$content);
        }
}

function resp($text) {
    global $response_type;
    if ($response_type == 'json') {
        if (is_array($text))
            return json_encode($text,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

        $response = json_encode(array_map('trim',preg_split('/\n/',$text)),JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    
    } elseif ($response_type == 'xml') {
        $response = "xml type response not implemented yet, try to create it";

    } elseif ($response_type == 'html') {
        $response = preg_replace('/\n/','<br>',$text);
    } else {
        $response = $response_type." type response not implemented, try to create it";
    }
    return $response;
}

?>
