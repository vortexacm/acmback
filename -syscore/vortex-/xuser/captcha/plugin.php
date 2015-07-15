<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

# make a new captch
$this->vLoadClass($this->vconf['path'],"/vActions.phar/class.captcha.php");

# set picture color
$bgcolors  = array("#F4F4F4","#e0d8bf","#efceee","#e8efbe","#f5cfb4","#bad2ea");
shuffle($bgcolors);

# set picture chars
$chars[0] = "0123456789";
$chars[1] = "abcdefghijklmnopqrstuvxwyz";
$chars[2] = "ABCDEFGHIJKLMNOPQRSTUVXWYZ";
shuffle($chars);

$maxp = array(4,5,6,3);
shuffle($maxp);

# make a captcha picture
$a = new captcha($maxp[1],200,50,$bgcolors[2],$_SERVER['DOCUMENT_ROOT'],"#EFEFEF",1,22,$chars[1]);


exit();
?>