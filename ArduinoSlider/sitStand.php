<?php

// сесть встать




include_once(dirname(__FILE__)."/ServoTimeLine.cls.php");
$tl=new rxServoTimeLine();// таймлайн
$frames=new servo_frame();// кадры

// сесть
$frames->addFrame("s2", 90, 0);
$frames->addFrame("s2", 155, 5000);
$frames->addFrame("s3", 45, 0);
$frames->addFrame("s3", 175, 5000);
$frames->addFrame("s4", 90, 0);
$frames->addFrame("s4", 155, 5000);
$frames->addFrame("s9", 90, 0);
$frames->addFrame("s9", 155, 5000);
$frames->addFrame("s10", 45, 0);
$frames->addFrame("s10", 175, 5000);
$frames->addFrame("s11", 90, 0);
$frames->addFrame("s11", 155, 5000);

// создать модель поведения на основе созданных кадров
$sitDown = new servo_modelBlank();
$sitDown->timeWork=5000;
$sitDown->timeFrame=150;
$sitDown->repeats=0;
$sitDown->addKeyFrame($frames->frames);
$tl->addModel($sitDown);



// вторая модель передвижения----------------------------------------------------------------
$frames->reset();
$frames->addFrame("s2", 155, 0);
$frames->addFrame("s2", 90, 5000);
$frames->addFrame("s3", 175, 0);
$frames->addFrame("s3", 45, 5000);
$frames->addFrame("s4", 155, 0);
$frames->addFrame("s4", 90, 5000);
$frames->addFrame("s9", 155, 0);
$frames->addFrame("s9", 90, 5000);
$frames->addFrame("s10", 175, 0);
$frames->addFrame("s10", 45, 5000);
$frames->addFrame("s11", 155, 0);
$frames->addFrame("s11", 90, 5000);

$standUp = new servo_modelBlank();
$standUp->timeWork=5000;
$standUp->timeFrame=150;
$standUp->repeats=0;
$standUp->addKeyFrame($frames->frames);
$tl->addModel($standUp);



// подсчитать модели - разложить их по таймлайну.
$tl->calculateTimeLine();

$time=0;
$time2=0;

for($i=0;$i<sizeof($tl->servoKeys);$i++){
    // пауза между фреймами
    $time=$tl->servoKeys[$i];
    if($time2>0)usleep((int) (($time-$time2) * 1000));
    $time2=$time;

    // установить сервы в нужные углы
    $arrPos=$tl->servoTimeline[$tl->servoKeys[$i]];




    // <<<<< --------  тут команды нужно слать

    $strCommand .= makeServoCommand($arrPos);// либо в обычном формате
    $binCommand .= makeServoCommandBin($arrPos);// либо в бинарном (в 3,5 раза меньше)


}
          /*

print strlen($strCommand)."<br>";
print strlen($binCommand)."<br>";

$cnt=0;
$str="";
for($i=0;$i<strlen($binCommand);$i++){
    $n=decbin(ord($binCommand[$i]));
    if(strlen($n)<8)$n=str_pad($n, 8, "0",STR_PAD_LEFT);
	print $n." ";
    $cnt++;
	if($cnt==9){$cnt=0;print " $str<br>";$str='';}
}
*/











function makeServoCommand($arrPos){
	$ret=array();
    if($arrPos["s1"]!=''){$ret[]="01".intval($arrPos["s1"]);}
    if($arrPos["s2"]!=''){$ret[]="02".intval($arrPos["s2"]);}
    if($arrPos["s3"]!=''){$ret[]="03".intval($arrPos["s3"]);}
    if($arrPos["s4"]!=''){$ret[]="04".intval($arrPos["s4"]);}
    if($arrPos["s5"]!=''){$ret[]="05".intval($arrPos["s5"]);}
    if($arrPos["s6"]!=''){$ret[]="06".intval($arrPos["s6"]);}
    if($arrPos["s7"]!=''){$ret[]="07".intval($arrPos["s7"]);}
    if($arrPos["s8"]!=''){$ret[]="08".intval($arrPos["s8"]);}
    if($arrPos["s9"]!=''){$ret[]="09".intval($arrPos["s9"]);}
    if($arrPos["s10"]!=''){$ret[]="10".intval($arrPos["s10"]);}
    if($arrPos["s11"]!=''){$ret[]="11".intval($arrPos["s11"]);}
    if($arrPos["s12"]!=''){$ret[]="12".intval($arrPos["s12"]);}

    if(sizeof($ret)>0)$ret="s".implode(";", $ret);

    return $ret;
}


function makeServoCommandBin($arrPos){
	$data=array();
	$flags=array(0,0);


    if($arrPos["s1"]!=''){$flags[0]+=bindec("00000001");$data[]=chr(intval($arrPos["s1"]));}
    if($arrPos["s2"]!=''){$flags[0]+=bindec("00000010");$data[]=chr(intval($arrPos["s2"]));}
    if($arrPos["s3"]!=''){$flags[0]+=bindec("00000100");$data[]=chr(intval($arrPos["s3"]));}
    if($arrPos["s4"]!=''){$flags[0]+=bindec("00001000");$data[]=chr(intval($arrPos["s4"]));}
    if($arrPos["s5"]!=''){$flags[0]+=bindec("00010000");$data[]=chr(intval($arrPos["s5"]));}
    if($arrPos["s6"]!=''){$flags[0]+=bindec("00100000");$data[]=chr(intval($arrPos["s6"]));}

    if($arrPos["s12"]!=''){$flags[1]+=bindec("00000001");$data[]=chr(intval($arrPos["s12"]));}
    if($arrPos["s11"]!=''){$flags[1]+=bindec("00000010");$data[]=chr(intval($arrPos["s11"]));}
    if($arrPos["s10"]!=''){$flags[1]+=bindec("00000100");$data[]=chr(intval($arrPos["s10"]));}
    if($arrPos["s9"]!=''){$flags[1]+=bindec("00001000");$data[]=chr(intval($arrPos["s9"]));}
    if($arrPos["s8"]!=''){$flags[1]+=bindec("00010000");$data[]=chr(intval($arrPos["s8"]));}
    if($arrPos["s7"]!=''){$flags[1]+=bindec("00100000");$data[]=chr(intval($arrPos["s7"]));}

    if(sizeof($data)>0){
    	$ret=chr(1);// номер команды. 1=управление сервами
        $ret.=chr($flags[0]).chr($flags[1]);// добавляем флаги
        $ret.=implode("", $data);
    	return $ret;
    }
}
















//$data=($binarydata>>5)&1;
//print $data; //   pack ("c", bindec("00010000"))













?>
