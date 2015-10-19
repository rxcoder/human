<?php

// сесть встать




include_once(dirname(__FILE__)."/ServoTimeLine.cls.php");
$tl=new rxServoTimeLine();// таймлайн
$frames1=new servo_frame();// кадры
$frames2=new servo_frame();// кадры
$frames3=new servo_frame();// кадры
$frames4=new servo_frame();// кадры

$stepTime=10000;
$timeFrame=100;

// сесть
$frames1->addFrame("s2", 90, 0);
$frames1->addFrame("s2", 25, $stepTime);
$frames1->addFrame("s3", 45, 0);
$frames1->addFrame("s3", 175, $stepTime);
$frames1->addFrame("s4", 90, 0);
$frames1->addFrame("s4", 155, $stepTime);

$frames2->addFrame("s9", 90, 0);
$frames2->addFrame("s9", 155, $stepTime);
$frames2->addFrame("s10", 45, 0);
$frames2->addFrame("s10", 175, $stepTime);
$frames2->addFrame("s11", 90, 0);
$frames2->addFrame("s11", 25, $stepTime);

$frames3->addFrame("s2", 25, 0);
$frames3->addFrame("s2", 90, $stepTime);
$frames3->addFrame("s3", 175, 0);
$frames3->addFrame("s3", 45, $stepTime);
$frames3->addFrame("s4", 155, 0);
$frames3->addFrame("s4", 90, $stepTime);

$frames4->addFrame("s9", 155, 0);
$frames4->addFrame("s9", 90, $stepTime);
$frames4->addFrame("s10", 175, 0);
$frames4->addFrame("s10", 45, $stepTime);
$frames4->addFrame("s11", 25, 0);
$frames4->addFrame("s11", 90, $stepTime);



$st1=$frames1->frames;

$st2=array_merge($frames3->frames, $frames2->frames);
$st3=array_merge($frames4->frames, $frames1->frames);

$st4=$frames3->frames;



//print_r($st1);
//print_r($st2);



// создать модель поведения на основе созданных кадров
$sitDown = new servo_modelBlank();
$sitDown->timeWork=$stepTime;
$sitDown->timeFrame=$timeFrame;
$sitDown->repeats=0;
$sitDown->addKeyFrame($st1);

$sitDown1 = new servo_modelBlank();
$sitDown1->timeWork=$stepTime;
$sitDown1->timeFrame=$timeFrame;
$sitDown1->repeats=0;
$sitDown1->addKeyFrame($st2);

$sitDown2 = new servo_modelBlank();
$sitDown2->timeWork=$stepTime;
$sitDown2->timeFrame=$timeFrame;
$sitDown2->repeats=0;
$sitDown2->addKeyFrame($st3);

$sitDown3 = new servo_modelBlank();
$sitDown3->timeWork=$stepTime;
$sitDown3->timeFrame=$timeFrame;
$sitDown3->repeats=0;
$sitDown3->addKeyFrame($st4);

$tl->addModel($sitDown);
$tl->addModel($sitDown1);
$tl->addModel($sitDown2);
$tl->addModel($sitDown1);
$tl->addModel($sitDown2);
$tl->addModel($sitDown3);

// подсчитать модели - разложить их по таймлайну.
$tl->calculateTimeLine();

$time=0;
$time2=0;
$strCommand='';
$pause=0;
for($i=0;$i<sizeof($tl->servoKeys);$i++){
    // пауза между фреймами
    $time=$tl->servoKeys[$i];

    if($time>0)$pause=$time-$time2;
    print "$time, $time2<br>";
    $time2=$time;
    // установить сервы в нужные углы
    $arrPos=$tl->servoTimeline[$tl->servoKeys[$i]];


    $strCommand .= makeServoCommand($arrPos)."\n";// в обычном формате
    $strCommand .= "p$pause;\n";//

    //$binCommand .= makeServoCommandBin($arrPos);// в бинарном (в 3,5 раза меньше)


}

$fpc=fopen("commands.model", "w+");
fwrite($fpc,$strCommand);
fclose($fpc);

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

    if(sizeof($ret)>0)$ret="s".implode(";", $ret).";";
    else{
        //print_r($arrPos);
        $ret='';
    }
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