<?php
header("Content-type: text/xml");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

$accesa=false;

if(system("gpio read 1")==0){
  $accesa=true;
}

system("gpio write 1 0"); //La accendo

while (system("gpio read 3")==0) {
    sleep(3);
}

system("gpio write 2 0");

sleep($_GET['durata']);

system("gpio write 2 1");

if($accesa==false){
  system("gpio write 1 1");
}

echo 1;

?>
