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
<canvas id="c" width="1000" height="700" style="background:#FFF;position: relative;" onmousemove="setZ(event, 'move');" onmousedown="setZ(event, 'down');" onmouseup="setZ(event, 'up');" oncontextmenu="return false;"></canvas>
</center>

<script type="text/javascript">
var c_canvas = document.getElementById("c");
var context = c_canvas.getContext("2d");








// начальная точка
var baseX=300;
var baseY=200;

// угол поворота в 3D
var baseX2=0;
var baseY2=0;

// текущее положение
var currentX=0;
var currentY=0;
var currentZ=0;

// текущее положение
var xRad=0;
var yRad=0;
var zRad=0;

// сдвиг осей. Проекция на 2D = [x, y]
var xVector=1;// множитель X
var yVector=1;// множитель Y
var zVector=[0, 0];// Влияние оси Z на множители X и Y


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



function transform3D(coord){
    /*coord=[
    	coord[0]*angleX,
    	coord[1]*angleY,
    	coord[2]*angleZ,
    ];
    */
    return coord;}


// начертить линию от текущей позиции до указанной
function arc3D(coord, radius, a1, a2, clc){
	coord=transform3D(coord);
    var x=coord[0];
    var y=coord[1];
    var z=coord[2];

    // расчитать координаты
	var xTo=baseX+(x*xVector)+(z*zVector[0]);
	var yTo=baseY+(y*yVector)+(z*zVector[1]);

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
	//coord=transform3D(coord);
    var x=coord[0];
    var y=coord[1];
    var z=coord[2];



    // расчитать координаты линии
	var xFrom=baseX+(currentX*xVector)+(currentZ*zVector[0]);//
	var yFrom=baseY+(currentY*yVector)+(currentZ*zVector[1]);//
	var xTo=baseX+(x*xVector)+(z*zVector[0]);
	var yTo=baseY+(y*yVector)+(z*zVector[1]);


    // отразить для наглядности. Поменять точку начала координат с верхнего левого угла в нижний левый.
    //yFrom=c_canvas.height-yFrom;
    //yTo=c_canvas.height-yTo;


    // нарисовать линию на 2D холсте
	context.moveTo(xFrom, yFrom);
	context.lineTo(xTo, yTo);

	currentX=x;
	currentY=y;
	currentZ=z;
}


// передвинуть текущую позицию
function moveTo3D(coord){
	coord=transform3D(coord);
	currentX=coord[0];
	currentY=coord[1];
	currentZ=coord[2];
}

// динамическое изменение Z
function setZ(e, action){
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


xVector=Math.cos(mouseMoved[0]/50);
yVector=Math.cos(mouseMoved[1]/50);
zVector=[Math.sin(mouseMoved[0]/50), Math.sin(mouseMoved[1]/50)];


        }    }

	c_canvas.width = c_canvas.width;// очистка холста

    document.getElementById("zz").value=(Math.floor(xVector*100)/100)+" x "+(Math.floor(yVector*100)/100)+", ["+(Math.floor(zVector[0]*100)/100)+" x "+(Math.floor(zVector[1]*100)/100)+"]";


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
		moveTo3D([100,100,0]);
		lineTo3D([500,100,0]);
		lineTo3D([300,400,200]);
		lineTo3D([100,100,0]);
		lineTo3D([100,100,400]);
		lineTo3D([300,400,200]);
		lineTo3D([500,100,400]);
		lineTo3D([100,100,400]);

		moveTo3D([500,100,400]);
		lineTo3D([500,100,0]);

		context.strokeStyle = "#555555";
		context.stroke();
	context.closePath();
	context.beginPath();
		arc3D([500,100,0], 3, 0, Math.PI * 2, false);
		arc3D([300,400,200], 3, 0, Math.PI * 2, false);
		arc3D([100,100,0], 3, 0, Math.PI * 2, false);
		arc3D([100,100,400], 3, 0, Math.PI * 2, false);
		arc3D([500,100,400], 3, 0, Math.PI * 2, false);



		context.strokeStyle = "#1E93E1";
		context.stroke();
	context.closePath();

}



</script>
      <br>
<form action="servo.php" method="post">

	<!--input type="range" min="-0.5" max="0.5" step="0.05" value="0" onmousemove="zVector[0]=this.value;drawTriangle();" onchange="zVector[0]=this.value;drawTriangle();" /-->

	<!--div id="pad" style="border:1px solid #777777;background:#FFF;width:200px;height:200px;" onmousemove="setZ(event);"></div-->

	<input type="text" name="zz" id="zz" value="" />
	<!--input type="submit" value=" Отправить " /-->
</form>
</body>
</html>