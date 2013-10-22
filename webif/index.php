<?php
$dbname = 'senderbase_db';

if (!mysql_connect('localhost', 'sbchkr', 'password')) {
    echo 'Could not connect to mysql';
    exit;
}

$rabel = NULL;
$param = NULL;
$order = NULL;
$search = NULL;

if(isset($_GET['rabel'])) { $rabel = $_GET['rabel']; }
if(isset($_GET['param'])) { $param = $_GET['param']; }
if(isset($_GET['order'])) { $order = $_GET['order']; }
if(isset($_GET['search'])) { $search = $_GET['search']; }

$sql = "select inet_ntoa(ipaddr),ptr,score,status,checkdate from $dbname.result order by checkdate desc";

if($rabel == 'status' && $param == 'poor' && $order == 'forward'){
  $sql = "select inet_ntoa(ipaddr),ptr,score,status,checkdate from $dbname.result where status = 'Poor' order by score ,ipaddr";
}
if($rabel == 'status' && $param == 'poor' && $order == 'reverse'){
  $sql = "select inet_ntoa(ipaddr),ptr,score,status,checkdate from $dbname.result where status = 'Poor' order by score desc ,ipaddr";
}
if($rabel == 'status' && $param == 'neutral' && $order == 'forward'){
  $sql = "select inet_ntoa(ipaddr),ptr,score,status,checkdate from $dbname.result where status = 'Neutral' order by score ,ipaddr";
}
if($rabel == 'status' && $param == 'neutral' && $order == 'reverse'){
  $sql = "select inet_ntoa(ipaddr),ptr,score,status,checkdate from $dbname.result where status = 'Neutral' order by score desc ,ipaddr";
}
if($rabel == 'status' && $param == 'good' && $order == 'forward'){
  $sql = "select inet_ntoa(ipaddr),ptr,score,status,checkdate from $dbname.result where status = 'Good' order by score ,ipaddr";
}
if($rabel == 'status' && $param == 'good' && $order == 'reverse'){
  $sql = "select inet_ntoa(ipaddr),ptr,score,status,checkdate from $dbname.result where status = 'Good' order by score desc ,ipaddr";
}
if(!is_null($search)){
  $sql = "select inet_ntoa(ipaddr),ptr,score,status,checkdate  from $dbname.result where ptr = '$search' or inet_ntoa(ipaddr) = '$search'";
}

$result = mysql_query($sql);
//$allcnt = mysql_query("select count(*) from senderbase_db.result");
//$goodcnt = mysql_query("select count(*) from senderbase_db.result where status = 'Good'");
//$ntrlcnt = mysql_query("select count(*) from senderbase_db.result where status = 'Neutral'");
//$poorcnt = mysql_query("select count(*) from senderbase_db.result where status = 'Poor'");

if (!$result) {
    echo "DB Error, could not list tables\n";
    echo 'MySQL Error: ' . mysql_error();
    exit;
}

print <<<END
<!DOCTYPE html>
<html>
  <head>
    <title>FATTOOLS : SenderBase Checker</title>
    <link href="css/bootstrap.css" rel="stylesheet">
  </head>
  <body>
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script src="js/bootstrap.js"></script>

  <div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
          <a class="brand" href="./index.php">FATTOOLS : Senderbase Checker</a>
           <ul class="nav">
            <li class="active"><a href="./index.php">All</a></li>
            <li class="active"><a href="./index.php?rabel=status&param=good&order=forward">Good</a></li>
            <li class="active"><a href="./index.php?rabel=status&param=neutral&order=forward">Neutral</a></li>
            <li class="active"><a href="./index.php?rabel=status&param=poor&order=forward">Poor</a></li>
           </ul>
           <form class="navbar-search">
             <input type="text" name="search" class="search-query" placeholder="HOSTNAME or IPADDR">
           </form>
      </div>
    </div>
  </div>

    <div class="container">
      <div id="header">
          <br>
          <br>
          <br>
        <pre>
        Debug
        "{$rabel}";
        "{$param}";
        "{$order}";
        "{$search}";
        </pre>
      </div>

      <div class="row">
        <div "span12" style="background-color: white;">
          <table class="table">
            <tr>
              <td>IP Address</td>
              <td>Hostname</td>
              <td>Reputation Score</td>
              <td>Status</td>
              <td>Last upate</td>
              <td>senderbase.org</td>
            </tr>
END;

while ($row = mysql_fetch_row($result)) {
print <<<END
            <tr>
              <td>{$row[0]}</td>
              <td>{$row[1]}</td>
              <td>{$row[2]}</td>
              <td>{$row[3]}</td>
              <td>{$row[4]}</td>
              <td>
                <span class="btn"><a href="http://www.senderbase.org/lookup?search_string={$row[0]}" target="_blank">Check now</a></span>
              </td>
            </tr>
END;
}

print <<<END
          </table>
        </div>


      </div>

    </div>
  </body>
</html>
END;

mysql_free_result($result);
?>
