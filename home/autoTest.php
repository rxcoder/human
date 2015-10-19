<?php
set_time_limit(0);



include_once("../ArduinoSlider/sitStand.php");



$fp =fopen("com2", "a");

send('');



$time=0;
$time2=0;
for($i=0;$i<sizeof($tl->servoKeys);$i++){
    // пауза между фреймами
    $time=$tl->servoKeys[$i];
    if($time2>0)usleep((int) (($time-$time2) * 1000));
    $time2=$time;
    // установить сервы в нужные углы
    $arrPos=$tl->servoTimeline[$tl->servoKeys[$i]];
    if($arrPos["s2"]!=''){	    $str1=makeServoCommand(array("s2"=>$arrPos["s2"], "s3"=>$arrPos["s3"], "s4"=>$arrPos["s4"]));//
	    send($str1);    }

    if($arrPos["s9"]!=''){	    $str2=makeServoCommand(array("s9"=>$arrPos["s9"], "s10"=>$arrPos["s10"], "s11"=>$arrPos["s11"]));
	    send($str2);
    }

    //print_r($str1);
    //print_r($str2);
    //$binCommand .= makeServoCommandBin($arrPos);// либо в бинарном (в 3,5 раза меньше)
}


fclose($fp);

print $lo;

     print "OK";

function send($str){
    global $fp;
    print microtime(true)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Send: ";	for($i=0;$i<strlen($str);$i++){		fwrite($fp, substr($str,$i,1));
		print substr($str,$i,1);	}
	//fwrite($fp, '\n');
	fwrite($fp, "\n");
	print "\n<br>";
}


function pause2($millisecond){	$a1=microtime(true)+$millisecond/1000;
	while(microtime(true)<$a1){};}













?>