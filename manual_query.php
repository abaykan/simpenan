<?php
// error_reporting(0);

$db_host = "";
$db_user = "";
$db_pass = "";

// Current url
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$CurPageURL = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Show tables
$params = $_GET;
$params['query'] = 'show tables';
$query_result = http_build_query($params);

?>
<!DOCTYPE html>
<html>
    <head>
            <title>._.</title>
            <link rel="stylesheet" href="https://hackcss.egoist.dev/hack.css">
            <link rel="stylesheet" href="https://hackcss.egoist.dev/dark.css">
    </head>
    <body class="hack dark">
        <div class="main container" style="padding-top: 3em;">
            <pre style="display: flex; justify-content: center; border:none;">    /\_/\
   ( o.o )  
    &gt; ^ &lt;
Manuaaal Query</pre>
            <p style="text-align: center;margin-bottom: 2rem;padding-bottom: 1rem;border-bottom: 1px dotted #ffffff;">
                Menu: 
                <a href="<?= parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) ?>">Show Databases</a>
                <?php
                if (isset($_GET['db']) && !empty($_GET['db'])) {
                    echo '- <a href="' . $_SERVER['PHP_SELF'] . '?' . $query_result . '">Show Tables</a>';   
                }
                ?>
            </p>

            <div class="grid" style="margin-bottom: 10px;margin-top: 5em;">
                <div class="cell -12of12">
                    <h2>Query</h2>
                    <form method="GET" class="form" action="">
                        <fieldset class="form-group">
                            <label for="nama">DB:</label>
                            <input id="nama" type="text" name="db" value="<?= isset($_GET['db']) ? $_GET['db'] : '' ?>" class="form-control">
                        </fieldset>
                        <fieldset class="form-group">
                            <label for="nama">Query:</label>
                            <input id="nama" type="text" name="query" value="<?= isset($_GET['query']) ? $_GET['query'] : '' ?>" class="form-control">
                        </fieldset>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-block">Run</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="grid">
                <div class="cell -12of12" style="max-width:100%;overflow-x: auto;">
                    <h2>Output | <a href="#" onclick="return copyClipboard()">Copy to Clipboard</a></h2>
                    <pre style="padding: 1em; border: 1px dotted #fff;" id="asd"><?php 
                    if (!isset($_GET['db'])) {
                        $mysqli = new mysqli($db_host, $db_user, $db_pass);
                        if ($mysqli->connect_errno) {
                            trigger_error('query failed: '.$mysqli->connect_error, E_USER_ERROR);
                        }

                        $result = $mysqli->query('SHOW databases')
                            or trigger_error('connect failed: '.join(',', $mysqli->error_list), E_USER_ERROR);

                        $rr = '';
                        echo 'Databases ('.$result->num_rows.'):<br/>';
                        foreach( $result as $row ) {
                            $r = array_shift($row);
                            $r = htmlentities($r);
                            echo " <a href='".$CurPageURL."?db=".$r."'>set</a> " . $r;
                            echo "<br/>";

                            $rr .= $r."\r\n";
                        }
                        echo '<textarea style="display:none;" id="output">'.$rr.'</textarea>';
                        echo "</div></body></html>";
                    } else {
                        $db = $_GET['db'];

                        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db);
                        if ($mysqli->connect_errno) {
                            trigger_error('query failed: '.$mysqli->connect_error, E_USER_ERROR);
                        }

                        if (empty($_GET['query'])) {
                            $_GET['query'] = "show tables";
                        }

                        $result = $mysqli->query($_GET['query'])
                            or trigger_error('connect failed: '.join(',', $mysqli->error_list), E_USER_ERROR);

                        $out = [];
                        $rr = '';

                        if (is_bool($result)) {
                            if ($result) {echo 'True';} else {echo 'False';}
                        } elseif (is_object($result)) {
                            foreach( $result as $key => $row ) {
                                if (count($row) <= 1) {
                                    $r = array_shift($row);
                                    if (strtolower($_GET['query']) == "show tables") {
                                        if ($key < 1) {
                                            echo 'Tables ('.$result->num_rows.'):<br/>';
                                        }
                                        $params = $_GET;
                                        $params['query'] = 'select * from ' . $r . ' limit 10';
                                        $query_result = http_build_query($params);
                                        echo '<a href="' . $_SERVER['PHP_SELF'] . '?' . $query_result . '">select</a> ' . htmlentities($r);
                                        $rr .= htmlentities($r)."\r\n";
                                    } else {
                                        echo htmlentities($r);
                                    }
                                    echo '<br/>';
                                } else {
                                    $out[] = $row;
                                }
                            }

                            if (count($out) != 0) {
                                $out_json = htmlentities(json_encode($out, JSON_PRETTY_PRINT));
                                print_r($out_json);
                                print_r('<textarea style="display:none;" id="output">'.$out_json.'</textarea>');
                            } else {
                                print_r('<textarea style="display:none;" id="output">'.$rr.'</textarea>');
                            }
                        }
                    }
                    ?>
                    </pre>
                </div>
            </div>
        </div>
        <?php
        if (isset($_GET['db'])) {
            echo '<script>
            ambil = document.getElementById("asd").innerHTML;
            result = ambil.trim();
            document.getElementById("asd").innerHTML = result;
        </script>';
        }
        ?>
        <script>
            function copyClipboard() {
                var copyText = document.getElementById("output");

                copyText.select();
                copyText.setSelectionRange(0, 99999); // For mobile devices

                navigator.clipboard.writeText(copyText.value);
                return new Promise((resolve) => setTimeout(resolve, 1000));
            }
        </script>
    </body>
</html>
