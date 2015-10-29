
/*

var tri = new rx3d("c");

var arrPoints=[// массив точек
   [100,100,0],
   [500,100,0],
   [300,400,200],
   [100,100,400],
   [500,100,400]
];
var arrLines=[// массив линий, соединяющих точки
   [0, 1, "#FF5555"],
   [0, 3],
   [1, 2, "#5555FF"],
   [2, 0, "#000000"],
   [2, 4],
   [3, 2],
   [4, 3],
   [4, 1]
];
var arrCircle=[// массив кружков, находящихся по координатам точек
   [0],
   [1],
   [2,"#FF0000"],
   [3],
   [4]
];


//arrPoints=tri.toCentre(arrPoints);// центровать точки
arrPoints=tri.setZero(arrPoints, [300,400,200]);// установить ноль
arrPoints=tri.mirror(arrPoints);// отражение по вертикали, чтобы Y начинался снизу
// присвоить значения
tri.arrPoints=arrPoints;
tri.arrLines=arrLines;
tri.arrCircle=arrCircle;
// нарисовать
tri.redraw();
*/


function rx3dObjects(canvasId)
{
    // canvas
	this.canvasId = canvasId;
	this.c_canvas = document.getElementById(canvasId);
	this.context = this.c_canvas.getContext("2d");

	this.objects=[];// массив объектов rx3d
	this.objectNames=[];// имена объектов
	this.objectWeight=[];// точки сентра тяжести
	this.objectLinks=[];// ссылки одних наборов на другие наборы
	this.objectAngle=[];// начальные углы поворота объектов
	this.zeroPoint=[];// массив точек ноля для всех объектов


}
rx3dObjects.prototype = {
	//
	/*                                  [x,y,z]-точка в 3D           pN - номер точки в массиве точек
	name          имя набора точек
	points        массив точек  [[x,y,z], [x,y,z], [x,y,z] ...]
	lines         массив линий  [[p0,p1,color], [p1,p2], ...]       color не обязательно
	circles       массив кругов [[p1], [p0,color], [p3], ... ];     color не обязательно
	zeroPoint     точка ноля по координатам  p1
	weightPoint   точка центра тяжести и её вес [p1, weight]
	links         ссылки точек на блоки. [[p1, name1], [p2, name2], [p3, name3] ...]
	angle         на сколько повёрнута фигура изначально  [x,y,z]
	*/

	addObject: function(name, points, lines, circles, zeroPoint, weightPoint, links, angle){
        this.objects[name] = new rx3d(this.canvasId);
		points=this.objects[name].setZero(points, [points[zeroPoint][0], points[zeroPoint][1], points[zeroPoint][2]]);// установить ноль
		points=this.objects[name].mirror(points);// отражение по вертикали, чтобы Y начинался снизу
		this.objects[name].arrPoints=points;
		this.objects[name].arrLines=lines;
		this.objects[name].arrCircle=circles;
		this.zeroPoint[name]=zeroPoint;
		if(this.objectNames.length>0)this.objects[name].clear=false;// очищает холст только первый объект

		this.objects[name].redraw();

        //this.objects[name]=tri;
        this.objectNames[this.objectNames.length]=name;
		this.objectWeight[name]=weightPoint;
		this.objectAngle[name]=angle;
		this.objectLinks[name]=links;
	},
	// смена координат объектов исходя из массива objectLinks. name-блок, с которого начинается отсчет
	build: function(name){
        this._build(name);
	    this.redraw();
	},
	_build: function(name){
	    var x, y, z, point1, point2, pt;// сдвиг накапливается
	    for(var i=0;i<this.objectLinks[name].length;i++){// this.objectLinks[name] -  ссылки точек на блоки. [[p1, name1], [p2, name2], [p3, name3] ...]
	    	pt=this.objectLinks[name][i];// pt=[p1, name1]
            point1=this.objects[name].arrPoints[pt[0]];// точка, к которой привязываемся
            point2=this.objects[pt[1]].arrPoints[this.zeroPoint[pt[1]]];// точка которая привязывается

            x=point1[0]-point2[0];
            y=point1[1]-point2[1];
            z=point1[2]-point2[2];

               // alert(this.objects[pt[1]].arrPoints.length);
            for(var j=0;j<this.objects[pt[1]].arrPoints.length;j++){//
                //alert(this.objects[pt[1]].arrPoints[j][0]);
                this.objects[pt[1]].arrPoints[j][0]+=x;
                //alert(this.objects[pt[1]].arrPoints[j][0]);
                this.objects[pt[1]].arrPoints[j][1]+=y;
                this.objects[pt[1]].arrPoints[j][2]+=z;
            }
            //alert(name+" > "+pt[1]);
            //alert(point1);


            this._build(pt[1]);
	    }
	},
	// обновить картинку
    redraw: function(){
        this.c_canvas.width = this.c_canvas.width;
	    for(var i=0;i<this.objectNames.length;i++){
            this.objects[this.objectNames[i]].redraw();
	    }    },

}


function rx3d(canvasId)
{

    // canvas
	this.c_canvas = document.getElementById(canvasId);
	this.context = this.c_canvas.getContext("2d");




	this.wPad=this.c_canvas.width;
	this.hPad=this.c_canvas.height;

	// координаты canvas
    var top=0, left=0;
    while(this.c_canvas) {
        top = top + parseFloat(this.c_canvas.offsetTop);
        left = left + parseFloat(this.c_canvas.offsetLeft);
        this.c_canvas = this.c_canvas.offsetParent;
    }
    this.xPad=Math.round(left);
	this.yPad=Math.round(top);
	this.c_canvas = document.getElementById(canvasId);

	// начальная точка по умолчанию в canvas
	this.baseX=Math.round(this.wPad/2);
	this.baseY=Math.round(this.hPad/2);
	// смещение начальной точки
	this.baseX2=0;
	this.baseY2=0;

	// смещение всех точек
	this.offset=[0,0,0];

    // цвета линий и кружков по умолчанию
	this.lineColor="#555";
	this.circleColor="#1E93E1";

	// текущее положение
	this.currentX=0;
	this.currentY=0;
	this.currentZ=0;

	// просчет сдвига мышью
	this.mouseFrom=[0,0];
	this.mouseMoved=[0,0];
	this.mouseBase=[this.baseX,this.baseY];
	this.mouseDwn=false;
	this.mouseBtn=0;

	// очищать canvas при перерисовке
	this.clear=true;

    // массив точек
	this.arrPoints=[];/*[
	   [100,100,0],
	   [500,100,0],
	   [300,400,200],
	   [100,100,400],
	   [500,100,400]
	]; */
    // дубликат прошлого массива.
	this.arrPointsReal=[];/*[
	   [100,100,0],
	   [500,100,0],
	   [300,400,200],
	   [100,100,400],
	   [500,100,400]
	]; */
		;

	this.arrLines=[];/*[
	   [pX, pY, color],    линия от точки  pX до точки pY   (pX значит this.arrPoints[x])
	   [pX, pY, color],
	   [pX, pY, color],
	   [pX, pY, color],
	   [pX, pY, color]
	]; */

	this.arrCircle=[];/*[
	   [point, color],    кружек   (point из массива this.arrPoints)
	   [point, color],
	   [point, color],
	   [point, color],
	   [point, color]
	]; */


    var self=this;
    var listener1 = this.addListener(this.c_canvas, "mousewheel", function(event) {
		self.mouseAction(event, 'wheel');
	});
    var listener2 = this.addListener(this.c_canvas, "mousemove", function(event) {
		self.mouseAction(event, 'move');
	});
    var listener3 = this.addListener(this.c_canvas, "mousedown", function(event) {
		self.mouseAction(event, 'down');
	});
    var listener4 = this.addListener(this.c_canvas, "mouseup", function(event) {
		self.mouseAction(event, 'up');
	});

};

rx3d.prototype = {
	// начертить линию от текущей позиции до указанной
	arc3D: function(coord, radius, a1, a2, clc){
	    var x=coord[0]+this.offset[0];
	    var y=coord[1]+this.offset[1];
	    var z=coord[2]+this.offset[2];
	    // расчитать координаты
		var xTo=this.baseX+x;
		var yTo=this.baseY+y;
	    // нарисовать линию на 2D холсте
	    this.context.moveTo(xTo, yTo);
		this.context.arc(xTo, yTo, radius, a1, a2, clc);
        // запомнить текущую позицию
		this.currentX=x;
		this.currentY=y;
		this.currentZ=z;

	},

    // начертить линию от текущей позиции до указанной
	lineTo3D: function(coord){
	    var x=coord[0]+this.offset[0];
	    var y=coord[1]+this.offset[1];
	    var z=coord[2]+this.offset[2];
	    // расчитать координаты линии
		var xFrom=this.baseX+this.currentX;//
		var yFrom=this.baseY+this.currentY;//
		var xTo=this.baseX+x;//
		var yTo=this.baseY+y;//
	    // нарисовать линию на 2D холсте
		this.context.moveTo(xFrom, yFrom);
		this.context.lineTo(xTo, yTo);
        // запомнить текущую позицию
		this.currentX=x;
		this.currentY=y;
		this.currentZ=z;
	},

    // передвинуть текущую позицию
	moveTo3D: function (coord)	{
		this.currentX=coord[0]+this.offset[0];
		this.currentY=coord[1]+this.offset[1];
		this.currentZ=coord[2]+this.offset[2];
	},

	// обработка событий мыши
	mouseAction: function (event, action){
        //alert(action);
	    // кнопка мыши
	    if (event.which == null) this.mouseBtn= (event.button < 2) ? "LEFT" : ((event.button == 4) ? "MIDDLE" : "RIGHT"); // >
	    else this.mouseBtn=(event.which < 2) ? "LEFT" : ((event.which == 2) ? "MIDDLE" : "RIGHT");  //  >

	    var xmouse=event.clientX-this.xPad;
	    var ymouse=event.clientY-this.yPad;


	    // обработка событий
	    if(action=='down'){
	    	this.mouseFrom=[xmouse, ymouse];
	    	this.mouseDwn=true;
	    	this.mouseBase=[this.baseX,this.baseY];

	        this.baseX2=xmouse-this.baseX;
	        this.baseY2=ymouse-this.baseY;

	    }
	    else if(action=='up'){
	    	this.mouseDwn=false;
	    }
	    else if(this.mouseDwn && (action=='move')){
	        this.mouseMoved=[xmouse-this.mouseFrom[0], ymouse-this.mouseFrom[1]];

	        if(this.mouseBtn=="LEFT"){// перемещение правой кнопкой мыши
		    	this.baseX=this.mouseBase[0]+this.mouseMoved[0];
		    	this.baseY=this.mouseBase[1]+this.mouseMoved[1];
	        }
	        else{
				 if(this.mouseBtn=="RIGHT"){				 	 this.arrPoints=this.turn3D(this.arrPoints, [this.mouseMoved[1],this.mouseMoved[0],0]);
				 	 this.arrPointsReal=this.turn3D(this.arrPointsReal, [this.mouseMoved[1],this.mouseMoved[0],0]);				 }
				 else{				 	 this.arrPoints=this.turn3D(this.arrPoints, [0,0,this.mouseMoved[1]]);
				 	 this.arrPointsReal=this.turn3D(this.arrPointsReal, [0,0,this.mouseMoved[1]]);				 }
				 this.mouseFrom[0]=xmouse;
				 this.mouseFrom[1]=ymouse;
	        }
	    }
	    else if(action=='wheel'){
	    	this.arrPoints=this.scale3D(this.arrPoints, 1+(event.deltaY/1000));
	    	this.arrPointsReal=this.scale3D(this.arrPointsReal, 1+(event.deltaY/1000));
	    }

	    // document.getElementById("zz").value=mouseMoved[0]+" x "+mouseMoved[1]+", ["+baseX+" x "+baseY+"]";
        this.redraw();
	},
    // перерисовка
    redraw: function(){
	    // рисуем линии
		if(this.clear)this.c_canvas.width = this.c_canvas.width;// очистка холста
	    // this.drawGrid();
		this.draw();
    },
    draw: function(){
	    // рисуем линии
	    for(var i=0;i<this.arrLines.length;i++){
        	this.context.beginPath();
            this.moveTo3D(this.arrPoints[this.arrLines[i][0]]);
			this.lineTo3D(this.arrPoints[this.arrLines[i][1]]);
			this.context.strokeStyle = (this.arrLines[i][2])?this.arrLines[i][2]:this.lineColor;
			this.context.stroke();
   			this.context.closePath();
	    }
        // рисуем кружки
	    for(var i=0;i<this.arrCircle.length;i++){
        	this.context.beginPath();

        	this.arc3D(this.arrPoints[this.arrCircle[i][0]], 3, 0, Math.PI * 2, false);
			this.context.strokeStyle = (this.arrCircle[i][1]!=undefined)?this.arrCircle[i][1]:this.circleColor;
			this.context.stroke();
   			this.context.closePath();
	    }
    },

	// вращение массива точек вокруг центра (0,0,0)
	turn3D: function (pointArr, angle)
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
	},
    // сдвинуть все точки объекта относительно их общего центра
	toCentre: function (pointArr){
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
	},
	// сдвинуть все точки объекта так, что указанная точка будет в координатах [0,0,0]
	setZero: function (pointArr, point){
		for (var i=0; i < pointArr.length; i++)
		{
		    pointArr[i][0]-=point[0];
		    pointArr[i][1]-=point[1];
		    pointArr[i][2]-=point[2];
		}
		return pointArr;
	},
	// сдвинуть все точки объекта
	moveZero: function (pointArr, point){
		for (var i=0; i < pointArr.length; i++)
		{
		    pointArr[i][0]+=point[0];
		    pointArr[i][1]+=point[1];
		    pointArr[i][2]+=point[2];
		}
		return pointArr;
	},
    // масштабирование
	scale3D: function (pointArr, mod){
		for (var i=0; i < pointArr.length; i++)
		{
			pointArr[i][0] = pointArr[i][0]*mod;
			pointArr[i][1] = pointArr[i][1]*mod;
			pointArr[i][2] = pointArr[i][2]*mod;
		}

		return pointArr;
	},
	// перевернуть точки
	mirror: function (pointArr){
		pointArr=this.turn3D(pointArr, [180,0,0]);
		return pointArr;
	},

	// регистрация слушателя события
	addListener:function (instance, eventName, listener) {
	    var listenerFn = listener;
	    if (instance.addEventListener){
	    //alert(instance);	    	instance.addEventListener(eventName, listenerFn, false);
		}
	    else if (instance.attachEvent) {
	        listenerFn = function() {listener(window.event);}
	        instance.attachEvent("on" + eventName, listenerFn);
	    }
	    //else throw new Error("Event registration not supported");
	    return {instance: instance, name: eventName, listener: listenerFn};
	},

	// удаление слушателя
	removeListenerfunction (event) {
	    var instance = event.instance;
	    if (instance.removeEventListener) instance.removeEventListener(event.name, event.listener, false);
	    else if (instance.detachEvent) instance.detachEvent("on" + event.name, event.listener);
	},

    // нарисовать сетку
	drawGrid: function (){

		this.context.beginPath();
		// сетка x20
		for (var x = 0; x <= this.wPad; x += 20) {
			this.context.moveTo(x, 0);
			this.context.lineTo(x, this.hPad);
		}
		for (var y = 0; y <= this.hPad; y += 20) {
			this.context.moveTo(0, y);
			this.context.lineTo(this.wPad, y);
		}
		this.context.strokeStyle = "#eee";
		this.context.stroke();

		this.context.beginPath();
		// сетка x100
		for (var x = 0; x <= this.wPad; x += 100) {
			this.context.moveTo(x, 0);
			this.context.lineTo(x, this.hPad);
		}
		for (var y = 0; y <= this.hPad; y += 100) {
			this.context.moveTo(0, y);
			this.context.lineTo(this.wPad, y);
		}
		this.context.strokeStyle = "#eee";
		this.context.stroke();

		// контур
		this.context.beginPath();
		this.context.moveTo(0, 0);
		this.context.lineTo(this.wPad, 0);
		this.context.lineTo(this.wPad, this.hPad);
		this.context.lineTo(0, this.hPad);
		this.context.lineTo(0, 0);
		this.context.strokeStyle = "#000";
		this.context.stroke();
		this.context.closePath();



	},

};
