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
  $sql = "select inet_ntoa(ipaddr),ptr,score,status,checkdate from $dbname.result where ptr REGEXP '.*$search.*' or  inet_ntoa(ipaddr) REGEXP '.*$search.*'";
}

$result = mysql_query($sql);

if (!$result) {
    echo "DB Error, could not list tables\n";
    echo 'MySQL Error: ' . mysql_error();
    exit;
}

$allcntrow = mysql_query("select count(*) from $dbname.result");
list($allcnt) = mysql_fetch_row($allcntrow);

$goodcntrow = mysql_query("select count(*) from $dbname.result where status = 'Good'");
list($goodcnt) = mysql_fetch_row($goodcntrow);

$ntrlcntrow = mysql_query("select count(*) from $dbname.result where status = 'Neutral'");
list($ntrlcnt) = mysql_fetch_row($ntrlcntrow);

$poorcntrow = mysql_query("select count(*) from $dbname.result where status = 'Poor'");
list($poorcnt) = mysql_fetch_row($poorcntrow);


print <<<END
<!DOCTYPE html>
<html>
  <head>
    <title>SenderBase Checker</title>
    <link href="css/bootstrap.css" rel="stylesheet">
  </head>
  <body>
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script src="js/bootstrap.js"></script>

  <div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
        <a class="brand" href="./index.php">Senderbase Checker</a>
           <ul class="nav">
            <li><a class="btn.btn-mini btn-primary" href="./index.php">All : {$allcnt}</a></li>
            <li><a class="btn.btn-mini btn-success" href="./index.php?rabel=status&param=good&order=forward">Good : {$goodcnt}</a></li>
            <li><a class="btn.btn-mini btn-warning" href="./index.php?rabel=status&param=neutral&order=forward">Neutral : {$ntrlcnt}</a></li>
            <li><a class="btn.btn-mini btn-danger" href="./index.php?rabel=status&param=poor&order=forward">Poor : {$poorcnt}</a></li>
            <li><a class="btn.btn-mini btn-info" href="./csv.php?rabel={$rabel}&param={$param}&order={$order}"  target="_blank">GET CSV</a></li>
           </ul>
        <form class="navbar-search">
          <input type="text" name="search" class="search-query" placeholder="HOSTNAME or IPADDRESS">
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
        "{$allcnt}";
        "{$goodcnt}";
        "{$ntrlcnt}";
        "{$poorcnt}";
        </pre>
      </div>

      <div class="row">
        <div "span12" style="background-color: white;">
          <table class="table table-striped">
            <thead>
            <tr>
              <td><strong>IP Address</strong></td>
              <td><strong>Hostname</strong></td>
              <td><strong>Reputation Score</strong></td>
              <td><strong>Status</strong></td>
              <td><strong>Update</strong></td>
              <td><strong></strong></td>
            </tr>
            </thead>
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
                <span class="btn"><a href="http://www.senderbase.org/lookup?search_string={$row[0]}" target="_blank">SenderBase</a></span>
              </td>
            </tr>
END;
}

print <<<END
          </table>
        </div>
      </div>
      <div id="footer">
        <br>
      </div>
    </div>
  </body>
</html>
END;

mysql_free_result($allcntrow);
mysql_free_result($goodcntrow);
mysql_free_result($ntrlcntrow);
mysql_free_result($poorcntrow);
mysql_free_result($result);
?>
