<?php
set_time_limit(0);








//s0250;04130;03130;1150;09130;10130

// работает только после того, как будет что-то посано из терминала



//$fp =fopen("com2", "w");
//fwrite($fp, "s04130");// chr($i)
//fclose($fp);

$fp =fopen("com2", "a");
 send('');
//sleep(5);

//send("s0190;1290");sleep(1);

$lo='';
      /*  */
for($i=0;$i<65;$i+=1){

    //$cmd1="s01".($i+60).";";

    $cmd1="02".(90-$i).";";
    $cmd2="03".(48+$i*2).";";
    $cmd3="04".(90+$i).";";

    $cmd4="11".(90-$i).";";
    $cmd5="10".(45+$i*2).";";
    $cmd6="09".(97+$i).";";

    send("s".$cmd1.$cmd2.$cmd3);
    send("s".$cmd4.$cmd5.$cmd6);	pause2(100);
	//sleep(1);
    $lo+=fread($fp, 100);

}


for($i=65;$i>0;$i-=1){

    //$cmd1="s01".($i+60).";";

    $cmd1="02".(90-$i).";";
    $cmd2="03".(48+$i*2).";";
    $cmd3="04".(90+$i).";";

    $cmd4="11".(90-$i).";";
    $cmd5="10".(45+$i*2).";";
    $cmd6="09".(97+$i).";";

    send("s".$cmd1.$cmd2.$cmd3);
    send("s".$cmd4.$cmd5.$cmd6);
	pause2(100);
	//sleep(1);
    $lo+=fread($fp, 100);


}


send("s0290;0345;0490;");
send("s1190;1045;0990;");

//sleep(10);
fclose($fp);

print $lo;

     print "OK";

function send($str){
    global $fp;
    print microtime(true)." Send: ";	for($i=0;$i<strlen($str);$i++){		fwrite($fp, substr($str,$i,1));
		print substr($str,$i,1);	}
	//fwrite($fp, '\n');
	fwrite($fp, "\n");
	print "\n<br>";
}


function pause2($millisecond){	$a1=microtime(true)+$millisecond/1000;
	while(microtime(true)<$a1){};}
?>