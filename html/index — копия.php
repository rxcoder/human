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
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body style="background:#EEE;">

<center>
<canvas id="c" width="1000" height="700" style="background:#FFF;position: relative;" onmousewheel="mouseAction(event, 'wheel');" onmousemove="mouseAction(event, 'move');" onmousedown="mouseAction(event, 'down');" onmouseup="mouseAction(event, 'up');" oncontextmenu="return false;"></canvas>
</center>

<script type="text/javascript" src="./rx3d.cls.js"></script>
<script type="text/javascript">
var c_canvas = document.getElementById("c");
var context = c_canvas.getContext("2d");



// начальная точка
var baseX=500;
var baseY=350;

// угол поворота в 3D
var baseX2=0;
var baseY2=0;

// текущее положение
var currentX=0;
var currentY=0;
var currentZ=0;

// координаты панели навигации (canvas's)
var xPad=0;
var yPad=0;
var wPad=0;
var hPad=0;

// просчет сдвига мышью
var mouseFrom=[0,0];
var mouseMoved=[0,0];
var mouseBase=[baseX,baseY];
var mouseDwn=false;
var mouseBtn=0;



var arrPoints=[
   [100,100,0],
   [500,100,0],
   [300,400,200],
   [100,100,400],
   [500,100,400]
];

arrPoints=toCentre(arrPoints);





// начертить линию от текущей позиции до указанной
function arc3D(coord, radius, a1, a2, clc){
    var x=coord[0];
    var y=coord[1];
    var z=coord[2];

    // расчитать координаты
	var xTo=baseX+x;
	var yTo=baseY+y;

    // отразить для наглядности. Поменять точку начала координат с верхнего левого угла в нижний левый.
    //yTo=c_canvas.height-yTo;

    // нарисовать линию на 2D холсте
    context.moveTo(xTo, yTo);
	context.arc(xTo, yTo, radius, a1, a2, clc);

	currentX=x;
	currentY=y;
	currentZ=z;
}




// начертить линию от текущей позиции до указанной
function lineTo3D(coord){
    var x=coord[0];
    var y=coord[1];
    var z=coord[2];



    // расчитать координаты линии
	var xFrom=baseX+currentX;//
	var yFrom=baseY+currentY;//
	var xTo=baseX+x;//
	var yTo=baseY+y;//


    // отразить для наглядности. Поменять точку начала координат с верхнего левого угла в нижний левый.
    // yFrom=c_canvas.height-yFrom;
    // yTo=c_canvas.height-yTo;


    // нарисовать линию на 2D холсте
	context.moveTo(xFrom, yFrom);
	context.lineTo(xTo, yTo);

	currentX=x;
	currentY=y;
	currentZ=z;
}


// передвинуть текущую позицию
function moveTo3D(coord){
	currentX=coord[0];
	currentY=coord[1];
	currentZ=coord[2];
}

// динамическое изменение Z
function mouseAction(event, action){
    if(xPad==0){
    	getOffsetSum(c_canvas);
		wPad=parseInt(c_canvas.width);
		hPad=parseInt(c_canvas.height);    }

    // кнопка мыши
    if (event.which == null) mouseBtn= (event.button < 2) ? "LEFT" : ((event.button == 4) ? "MIDDLE" : "RIGHT"); // >
    else mouseBtn=(event.which < 2) ? "LEFT" : ((event.which == 2) ? "MIDDLE" : "RIGHT");  //  >

    var xmouse=event.clientX-xPad;
    var ymouse=event.clientY-yPad;


    // обработка событий
    if(action=='down'){    	mouseFrom=[xmouse, ymouse];
    	mouseDwn=true;
    	mouseBase=[baseX,baseY];

        baseX2=xmouse-baseX;
        baseY2=ymouse-baseY;
    }
    else if(action=='up'){    	mouseDwn=false;    }
    else if(mouseDwn && (action=='move')){        mouseMoved=[xmouse-mouseFrom[0], ymouse-mouseFrom[1]];

        if(mouseBtn=="LEFT"){// перемещение правой кнопкой мыши
	    	baseX=mouseBase[0]+mouseMoved[0];
	    	baseY=mouseBase[1]+mouseMoved[1];        }
        else{
			 if(mouseBtn=="RIGHT")arrPoints=turn3D(arrPoints, [mouseMoved[1],mouseMoved[0],0]);
			 else arrPoints=turn3D(arrPoints, [0,0,mouseMoved[1]]);
			 mouseFrom[0]=xmouse;
			 mouseFrom[1]=ymouse;        }    }
    else if(action=='wheel'){    	arrPoints=scale3D(arrPoints, 1+(event.deltaY/1000));
    }





	c_canvas.width = c_canvas.width;// очистка холста

    document.getElementById("zz").value=mouseMoved[0]+" x "+mouseMoved[1]+", ["+baseX+" x "+baseY+"]";


    // перерисовка
    drawGrid();
    drawTriangle();}

// запомнить р-ры и позицию canvas'a
function getOffsetSum(elem){
    var top=0, left=0;
    while(elem) {
        top = top + parseFloat(elem.offsetTop);
        left = left + parseFloat(elem.offsetLeft);
        elem = elem.offsetParent;
    }
    xPad=Math.round(left);
	yPad=Math.round(top);
    return {top: Math.round(top), left: Math.round(left)}
}



// вращение массива точек вокруг центра (0,0,0)
function turn3D(pointArr, angle)
{
  var x, y, z;

  for (var i=0; i < pointArr.length; i++)
  {
    y = pointArr[i][1];
    z = pointArr[i][2];
    pointArr[i][1] = y*Math.cos(angle[0]*Math.PI/180) - z*Math.sin(angle[0]*Math.PI/180);
    pointArr[i][2] = z*Math.cos(angle[0]*Math.PI/180) + y*Math.sin(angle[0]*Math.PI/180);
  }

  for (var i=0; i < pointArr.length; i++)
  {
    x = pointArr[i][0];
    z = pointArr[i][2];
    pointArr[i][0] = x*Math.cos(angle[1]*Math.PI/180) + z*Math.sin(angle[1]*Math.PI/180);
    pointArr[i][2] = z*Math.cos(angle[1]*Math.PI/180) - x*Math.sin(angle[1]*Math.PI/180);
  }

  for (var i=0; i < pointArr.length; i++)
  {
    x = pointArr[i][0];
    y = pointArr[i][1];
    pointArr[i][0] = x*Math.cos(angle[2]*Math.PI/180) - y*Math.sin(angle[2]*Math.PI/180);
    pointArr[i][1] = y*Math.cos(angle[2]*Math.PI/180) + x*Math.sin(angle[2]*Math.PI/180);
  }
  return pointArr;
}

function toCentre(pointArr){
    var x1=0, y1=0, z1=0;
    var x2=0, y2=0, z2=0;

	for (var i=0; i < pointArr.length; i++)
	{
        if(pointArr[i][0]>x2)x2=pointArr[i][0];
        else if((x1!=0) && (x1>pointArr[i][0]))x1=pointArr[i][0];
        if(pointArr[i][1]>y2)y2=pointArr[i][1];
        else if((y1!=0) && (y1>pointArr[i][1]))y1=pointArr[i][1];
        if(pointArr[i][2]>z2)z2=pointArr[i][2];
        else if((z1!=0) && (z1>pointArr[i][2]))z1=pointArr[i][2];
	}

	for (var i=0; i < pointArr.length; i++)
	{
	    pointArr[i][0]-=(x1+x2)/2;
	    pointArr[i][1]-=(y1+y2)/2;
	    pointArr[i][2]-=(z1+z2)/2;
	}
	return pointArr;
}

function scale3D(pointArr, mod){	for (var i=0; i < pointArr.length; i++)
	{
		pointArr[i][0] = pointArr[i][0]*mod;
		pointArr[i][1] = pointArr[i][1]*mod;
		pointArr[i][2] = pointArr[i][2]*mod;
	}
	return pointArr;}



function drawGrid(){
	// сетка x20
	for (var x = 0; x <= 1000; x += 20) {
		context.moveTo(x, 0);
		context.lineTo(x, 700);
	}
	for (var y = 0; y <= 700; y += 20) {
		context.moveTo(0, y);
		context.lineTo(1000, y);
	}
	context.strokeStyle = "#eee";
	context.stroke();
	context.beginPath();
	// сетка x100
	for (var x = 0; x <= 1000; x += 100) {
		context.moveTo(x, 0);
		context.lineTo(x, 700);
	}
	for (var y = 0; y <= 700; y += 100) {
		context.moveTo(0, y);
		context.lineTo(1000, y);
	}
	context.strokeStyle = "#eee";
	context.stroke();
	// контур
	context.beginPath();
	context.moveTo(0, 0);
	context.lineTo(1000, 0);
	//context.moveTo(1000, 0);
	context.lineTo(1000, 700);
	//context.moveTo(1000, 700);
	context.lineTo(0, 700);
	context.lineTo(0, 0);

	context.strokeStyle = "#000";
	context.stroke();
}



function drawTriangle(){
	context.beginPath();
		moveTo3D(arrPoints[0]);
		lineTo3D(arrPoints[1]);
		lineTo3D(arrPoints[2]);
		lineTo3D(arrPoints[0]);
		lineTo3D(arrPoints[3]);
		lineTo3D(arrPoints[2]);
		lineTo3D(arrPoints[4]);
		lineTo3D(arrPoints[3]);

		moveTo3D(arrPoints[4]);
		lineTo3D(arrPoints[1]);

		context.strokeStyle = "#555555";
		context.stroke();
	context.closePath();
	context.beginPath();
		arc3D(arrPoints[1], 3, 0, Math.PI * 2, false);
		arc3D(arrPoints[2], 3, 0, Math.PI * 2, false);
		arc3D(arrPoints[0], 3, 0, Math.PI * 2, false);
		arc3D(arrPoints[3], 3, 0, Math.PI * 2, false);
		arc3D(arrPoints[4], 3, 0, Math.PI * 2, false);



		context.strokeStyle = "#1E93E1";
		context.stroke();
	context.closePath();

}



</script>
      <br>
<form action="servo.php" method="post">

	<!--input type="range" min="-0.5" max="0.5" step="0.05" value="0" onmousemove="zVector[0]=this.value;drawTriangle();" onchange="zVector[0]=this.value;drawTriangle();" /-->

	<!--div id="pad" style="border:1px solid #777777;background:#FFF;width:200px;height:200px;" onmousemove="mouseAction(event);"></div-->

	<input type="text" name="zz" id="zz" value="" />
	<!--input type="submit" value=" Отправить " /-->
</form>
</body>
</html>