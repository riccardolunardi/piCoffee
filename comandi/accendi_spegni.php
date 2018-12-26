<?php
header("Content-type: text/xml");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");


if(system("gpio read 1")==1){
  system("gpio write 1 0");
}else {
  system("gpio write 1 1");
};








?>
