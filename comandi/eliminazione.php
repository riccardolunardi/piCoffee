<?php
header("Content.type: text/xml");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");


// php is weird sometimes
// the output is an array split on new lines
//exec('crontab -l', $data);
// this is the one you are replacing
//$_GET['programmazioniDaEliminare'][0]="04 01 2 6 * sudo php-cgi -f /var/www/html/comandi/on.php durata=3";

if(isset($_GET['programmazioniDaEliminare'])){
  $cronjobDaElim=json_decode($_GET['programmazioniDaEliminare']);
  //$cronjobDaElim=$_GET['programmazioniDaEliminare'];
  for($i=0;$i<count($cronjobDaElim);$i++){
    //get contents of cron tab
    $output = shell_exec('crontab -l');

    //echo "<pre>$output</pre>";
    //Find string
    $cronjob = $cronjobDaElim[$i];
    if (strstr($output, $cronjob)) {
       echo 'Trovato';
    } else {
       echo 'Non trovato';
    }

    //Copy cron tab and remove string
    var_dump($output);
    echo "<br>";

    $newcron = str_replace($cronjob."\n","",$output);

    echo "<pre>$cronjob</pre>";
    file_put_contents('/tmp/crontab.txt', $newcron);
    exec('crontab /tmp/crontab.txt');
  }
}

echo 1;
?>
