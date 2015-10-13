<?php
set_time_limit(0);



$servos=($_REQUEST['servos'])?$_REQUEST['servos']:array(0,88,90,48,90,93,90,89,92,97,50,90,94);


exec("mode com2 xon=on BAUD=96 data=8");// ���� ��� ������� �� � ����� �������� �����, �� ���������
$fp =fopen("com2", "w");                   // ��� ���...
exec("mode com2 xon=on BAUD=96 data=8");

/*
   Serial port:
      MODE COMm[:] [BAUD=b] [PARITY=p] [DATA=d] [STOP=s]
                                [to=on|off] [xon=on|off] [odsr=on|off]
                                [octs=on|off] [dtr=on|off|hs]
                                [rts=on|off|hs|tg] [idsr=on|off]

MODE COM2 /STATUS         - ��������� �����


baud=b �������� �������� � ����� � �������
	11		110 ���
	15		150 ���
	30		300 ���
	60		600 ���
	12		1200 ���
	24		2400 ���
	48		4800 ���
	96		9600 ���
	19 		19 200 ���

	  ���������
			38400  => 38400,
			57600  => 57600,
			115200 => 115200

parity=p ����� �������� ������ ��������.
	n	���
	e	��� (even)    �� �����.
	o	����� (odd)
	m	������� (mark)
	s	������ (space)

data=d ����� ����� ������ � �������.
    5-8   �� �����. 7

stop=s ����� �������� �����, ������������ ����� �������. ���� �������� �������� ����� 110, �� ��������� ������������ �������� 2. � ��������� ������ ������������ �������� 1.
	1, 1,5 ��� 2.

to={on|off} ����� ��������� ����������� ���� ��������. �� ��������� ����� �������� (off).
xon={on|off} ����� ������������� ��������� xon/xoff ��� ���������� ���������.
odsr={on|off} ��������� ��� ���������� ������������ ������� � �������������� ������� Data Set Ready (DSR).
octs={on|off} ��������� ��� ���������� ������������ ������� � �������������� ������� Clear To Send (CTS).
dtr={on|off|hs} ����� ������������� ������� ���������� ��������� ������ (DTR). ��������� ��������: on (��������), off (���������), handshake (������������).
rts={on|off|hs|tg} ����� ������������� ������� Request To Send (RTS). ��������� ��������: on (��������), off (���������), handshake (������������) � toggle (������������).
idsr={on|off} ����� ������������� ������������� ������� DSR.



*/







// send('');
//sleep(5);

//send("s01".$servos[1].";02".$servos[2].";03".$servos[3].";");
//send("s04".$servos[4].";05".$servos[5].";06".$servos[6].";");
//send("s07".$servos[7].";08".$servos[8].";09".$servos[9].";");
//send("s10".$servos[10].";11".$servos[11].";12".$servos[12].";");

send("s01".$servos[1].";", true);
send("s02".$servos[2].";", true);
send("s03".$servos[3].";", true);
send("s04".$servos[4].";", true);
send("s05".$servos[5].";", true);
send("s06".$servos[6].";", true);
send("s07".$servos[7].";", true);
send("s08".$servos[8].";", true);
send("s09".$servos[9].";", true);
send("s10".$servos[10].";", true);
send("s11".$servos[11].";", true);
send("s12".$servos[12].";", true);

fclose($fp);


     print "OK";

function send($str, $wait=false){
    global $fp;
    print microtime(true)." Send: ";	for($i=0;$i<strlen($str);$i++){		fwrite($fp, substr($str,$i,1));
		print substr($str,$i,1);	}
	//fwrite($fp, '\n');
	fwrite($fp, "\n");
	if($wait)pause2(100);
	print "\n<br>";
}


function pause2($millisecond){	$a1=microtime(true)+$millisecond/1000;
	while(microtime(true)<$a1){};}
?><html>
<head>
<title></title>
<meta name="title" content="">
<meta name="description" content="">
<meta name="keywords" content="">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="content-language" content="ru">
<meta name="robots" content="index,FOLLOW">
<meta name="author" content="RXcoder">
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<form action="servo.php" method="post">
<table style="border:1px solid #555555;">
<tr>
<td>�6 <input type="text" name="servos[6]" value="<?=$servos[6] ?>" /></td>
<td>�7 <input type="text" name="servos[7]" value="<?=$servos[7] ?>" /></td>
</tr><tr>
<td>�5 <input type="text" name="servos[5]" value="<?=$servos[5] ?>" /></td>
<td>�8 <input type="text" name="servos[8]" value="<?=$servos[8] ?>" /></td>
</tr><tr>
<td>�4 <input type="text" name="servos[4]" value="<?=$servos[4] ?>" /></td>
<td>�9 <input type="text" name="servos[9]" value="<?=$servos[9] ?>" /></td>
</tr><tr>
<td>�3 <input type="text" name="servos[3]" value="<?=$servos[3] ?>" /></td>
<td>�10 <input type="text" name="servos[10]" value="<?=$servos[10] ?>" /></td>
</tr><tr>
<td>�2 <input type="text" name="servos[2]" value="<?=$servos[2] ?>" /></td>
<td>�11 <input type="text" name="servos[11]" value="<?=$servos[11] ?>" /></td>
</tr><tr>
<td>�1 <input type="text" name="servos[1]" value="<?=$servos[1] ?>" /></td>
<td>�12 <input type="text" name="servos[12]" value="<?=$servos[12] ?>" /></td>
</tr>
</table>
<input type="submit" value=" ��������� " />
</form>
</body>
</html>