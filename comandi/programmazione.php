<?php
header("Content.type: text/xml");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");



/*$_GET['durata']=60; debug
$_GET['ora']="23:30";
$_GET['giorni']="6";
*/

$durata=$_GET['durata'];
$orario=$_GET['ora'];
$_GET['data']="16/07";


//metto in variabili separate l'ora e i minuti asdasd
$orario = explode(":",$orario);
$ora=$orario[0];
$minuto=$orario[1];
$richiesta="";
//FINE

if(isset($_GET['giorni'])){
  $giornisett=$_GET['giorni'];


  $richiesta="(crontab -l && echo '$minuto $ora * * $giornisett sudo php-cgi -f /var/www/html/comandi/on.php durata=$durata') | crontab -";
  system("echo $richiesta >> crontab -e");

}elseif (isset($_GET['data'])){
  //$data=$_GET['data'];
  $data="16/07";

  $data = explode("/",$data);
  $giorno=$data[0];
  $mese=$data[1];

  $richiesta="(crontab -l && echo '$minuto $ora $giorno $mese * sudo php-cgi -f /var/www/html/comandi/on.php durata=$durata') | crontab -";
}
$retval="";


system($richiesta, $retval);
echo 1;
?>
