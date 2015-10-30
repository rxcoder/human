<?php

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
</head>
<body style="background:#EEE;">

<center>
<canvas id="c" width="1000" height="700" style="background:#FFF;position: relative;border:1px solid #000;" oncontextmenu="return false;"></canvas>
</center>

<script type="text/javascript" src="./rx3d.cls.js"></script>
<script type="text/javascript">

var rx3dObj=new rx3dObjects("c", "pyramid1");

rx3dObj.addObject(
	"pyramid1",// name
	[[100,100,0], [500,100,0], [300,400,200], [100,100,400], [500,100,400]],// points
	[[0, 1, "#F00"], [0, 3, "#F00"], [1, 2, "#F00"], [2, 0, "#F00"], [2, 4, "#F00"], [3, 2, "#F00"], [4, 3, "#F00"], [4, 1, "#F00"]],// lines
	[[0],[1],[2,"#FF0000"],[3],[4]],// circles
	2,// zeroPoint
	2,// weightPoint
	[[1, "pyramid2"]],// links
	[0,0,0]// angle
);

rx3dObj.addObject(
	"pyramid2",// name
	[[100,100,0], [500,100,0], [300,400,200], [100,100,400], [500,100,400]],// points
	[[0, 1, "#0F0"], [0, 3, "#0F0"], [1, 2, "#0F0"], [2, 0, "#0F0"], [2, 4, "#0F0"], [3, 2, "#0F0"], [4, 3, "#0F0"], [4, 1, "#0F0"]],// lines
	[[0],[1],[2,"#FF0000"],[3],[4]],// circles
	2,// zeroPoint
	2,// weightPoint
	[[0, "pyramid3"], ],// links
	[0,0,0]// angle
);

rx3dObj.addObject(
	"pyramid3",// name
	[[100,100,0], [500,100,0], [300,400,200], [100,100,400], [500,100,400]],// points
	[[0, 1, "#00F"], [0, 3, "#00F"], [1, 2, "#00F"], [2, 0, "#00F"], [2, 4, "#00F"], [3, 2, "#00F"], [4, 3, "#00F"], [4, 1, "#00F"]],// lines
	[[0],[1],[2,"#FF0000"],[3],[4]],// circles
	4,// zeroPoint
	2,// weightPoint
	[],// links
	[0,0,0]// angle
);



rx3dObj.build();





var t=0;
var tmr;

function turn(){ 	rx3dObj.turn3D("pyramid2", [0,1,0]);
	rx3dObj.build();
	rx3dObj.fitWindow(70);
	rx3dObj.build();

	t++;
	if(t>100)clearInterval(tmr);}




</script>
      <br>
<form action="servo.php" method="post">

	<!--input type="range" min="-0.5" max="0.5" step="0.05" value="0" onmousemove="zVector[0]=this.value;drawTriangle();" onchange="zVector[0]=this.value;drawTriangle();" /-->

	<!--div id="pad" style="border:1px solid #777777;background:#FFF;width:200px;height:200px;" onmousemove="mouseAction(event);"></div-->

	<input type="text" name="zz" id="zz" value="" />
	<input type="button" onclick="tmr=setInterval(turn, 10);" value=" GO " />
	<!--input type="submit" value=" Отправить " /-->
</form>
</body>
</html>