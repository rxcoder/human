
/*

var tri = new rx3d("c");

var arrPoints=[// ������ �����
   [100,100,0],
   [500,100,0],
   [300,400,200],
   [100,100,400],
   [500,100,400]
];
var arrLines=[// ������ �����, ����������� �����
   [0, 1, "#FF5555"],
   [0, 3],
   [1, 2, "#5555FF"],
   [2, 0, "#000000"],
   [2, 4],
   [3, 2],
   [4, 3],
   [4, 1]
];
var arrCircle=[// ������ �������, ����������� �� ����������� �����
   [0],
   [1],
   [2,"#FF0000"],
   [3],
   [4]
];


//arrPoints=tri.toCentre(arrPoints);// ���������� �����
arrPoints=tri.setZero(arrPoints, [300,400,200]);// ���������� ����
arrPoints=tri.mirror(arrPoints);// ��������� �� ���������, ����� Y ��������� �����
// ��������� ��������
tri.arrPoints=arrPoints;
tri.arrLines=arrLines;
tri.arrCircle=arrCircle;
// ����������
tri.redraw();
*/


function rx3dObjects(canvasId, rootObject)
{
    // canvas
	this.canvasId = canvasId;
	this.c_canvas = document.getElementById(canvasId);
	this.context = this.c_canvas.getContext("2d");

	this.objects=[];// ������ �������� rx3d
    this.rootObject=rootObject;// �������� ������
	this.objectNames=[];// ����� ��������
	this.objectWeight=[];// ����� ������ �������
	this.objectLinks=[];// ������ ����� ������� �� ������ ������
	this.objectAngle=[];// ��������� ���� �������� ��������
	this.zeroPoint=[];// ������ ����� ���� ��� ���� ��������

}
rx3dObjects.prototype =
{
	//
	/*                                  [x,y,z]-����� � 3D           pN - ����� ����� � ������� �����
	name          ��� ������ �����
	points        ������ �����  [[x,y,z], [x,y,z], [x,y,z] ...]
	lines         ������ �����  [[p0,p1,color], [p1,p2], ...]       color �� �����������
	circles       ������ ������ [[p1], [p0,color], [p3], ... ];     color �� �����������
	zeroPoint     ����� ���� �� �����������  p1
	weightPoint   ����� ������ ������� � � ��� [p1, weight]
	links         ������ ����� �� �����. [[p1, name1], [p2, name2], [p3, name3] ...]
	angle         �� ������� �������� ������ ����������  [x,y,z]
	*/

	addObject: function(name, points, lines, circles, zeroPoint, weightPoint, links, angle){
        this.objects[name] = new rx3d(this.canvasId);
		points=this.objects[name].setZero(points, [points[zeroPoint][0], points[zeroPoint][1], points[zeroPoint][2]]);// ���������� ����
		points=this.objects[name].mirror(points);// ��������� �� ���������, ����� Y ��������� �����
		this.objects[name].arrPoints=points;
        this.objects[name].saveRealPoints(points);


		this.objects[name].arrLines=lines;
		this.objects[name].arrCircle=circles;
		this.zeroPoint[name]=zeroPoint;
		if(this.objectNames.length>0)this.objects[name].clear=false;// ������� ����� ������ ������ ������

		this.objects[name].redraw();

        //this.objects[name]=tri;
        this.objectNames[this.objectNames.length]=name;
		this.objectWeight[name]=weightPoint;
		this.objectAngle[name]=angle;
		this.objectLinks[name]=links;
	},
	// ����� ��������� �������� ������ �� ������� objectLinks. name-����, � �������� ���������� ������
	build: function(){
        this._build(this.rootObject);
	    this.redraw();
	},
	_build: function(name){
	    var x, y, z, point1, point2, pt;// ����� �������������
	    for(var i=0;i<this.objectLinks[name].length;i++){// this.objectLinks[name] -  ������ ����� �� �����. [[p1, name1], [p2, name2], [p3, name3] ...]
	    	pt=this.objectLinks[name][i];// pt=[p1, name1]
            point1=this.objects[name].arrPoints[pt[0]];// �����, � ������� �������������
            point2=this.objects[pt[1]].arrPoints[this.zeroPoint[pt[1]]];// ����� ������� �������������

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
	// �������� ��������
    redraw: function(){
        this.c_canvas.width = this.c_canvas.width;
	    for(var i=0;i<this.objectNames.length;i++){
            this.objects[this.objectNames[i]].redraw();
	    }
    },
	// ��������� ���� �� �������� �� ���� � �������� angle=[x,y,z]
    turn3D: function(name, angle){
	    var a, s, point, pt, o, names;
	    names=this.getChildren(name);
	    for(var i=0;i<this.objectNames.length;i++){
	        o=this.objects[this.objectNames[i]];
	        pt=o.arrPointsReal;
	        a=o.angle;
	        s=o.scale;
            // ������������ �� � ��������
            o.arrPoints=[];
		    for(var j=0;j<pt.length;j++) o.arrPoints[j]=[pt[j][0], pt[j][1], pt[j][2]];
		    // ��������� ������ ��������� ������
            o.angle=[[0,0,0]];
            o.scale=1;
            //if(this.objectNames[i]==name)o.arrPoints = o.turn3D(o.arrPoints, angle);
            if(names[this.objectNames[i]]==1)o.arrPoints = o.turn3D(o.arrPoints, angle);
            // �������� �������
            for(var j=0;j<a.length;j++) o.arrPoints = o.turn3D(o.arrPoints, a[j]);

            o.arrPoints = o.scale3D(o.arrPoints, s);
	    }
    },
	// �������� ����� ���� �������� ������� � ���� ret[name]=1
    getChildren: function(name){
	    var pt, allArr=[];
	    var arr=this._getChildren(name);
	    for(var i=0;i<arr.length;i++){	    	allArr[arr[i]]=1;
	    }
	    return allArr;
    },
    _getChildren: function(name){
	    var pt, arr=[], allArr=[];
	    allArr[allArr.length]=name;
	    for(var i=0;i<this.objectLinks[name].length;i++){// this.objectLinks[name] -  ������ ����� �� �����. [[p1, name1], [p2, name2], [p3, name3] ...]
	    	pt=this.objectLinks[name][i];// pt=[p1, name1]
	    	arr=this._getChildren(pt[1]);
	    	for(var j=0;j<arr.length;j++)allArr[allArr.length]=arr[j];
	    }
	    return allArr;
    },
    // ��������� ����������� �������� ��� ������ canvas
    fitWindow: function(prc){
	    var x1=0, y1=0, x2=0, y2=0;
	    var mn=1, w=0, h=0;
	    var baseX=0, baseY=0;
	    var o;
	    if(!prc)prc=100;
        prc=parseInt(prc)/100;
        // ����� ������� ������������ ����
	    for(var j=0;j<this.objectNames.length;j++){
	        o=this.objects[this.objectNames[j]];
		    for (var i=0; i < o.arrPoints.length; i++)
			{
		        if(o.arrPoints[i][0]>x2)x2=o.arrPoints[i][0];
		        else if(x1>o.arrPoints[i][0])x1=o.arrPoints[i][0];
		        if(o.arrPoints[i][1]>y2)y2=o.arrPoints[i][1];
		        else if(y1>o.arrPoints[i][1])y1=o.arrPoints[i][1];
			}
        }
        // alert(x1+", "+y1+", "+z1+" - "+x2+", "+y2+", "+z2);
        w = parseInt(this.c_canvas.width)/(Math.abs(x1)+Math.abs(x2));
        h = parseInt(this.c_canvas.height)/(Math.abs(y1)+Math.abs(y2));
        mn=(w<h)?w:h;
        mn*=prc;
        // ���������������
        for(var j=0;j<this.objectNames.length;j++){
	        o=this.objects[this.objectNames[j]];
		    for (var i=0; i < o.arrPoints.length; i++)
			{
		        o.arrPoints[i][0]*=mn;
		        o.arrPoints[i][1]*=mn;
		        o.arrPoints[i][2]*=mn;
			}
        }

        // ����������� � ������
        baseX=this.objects[this.rootObject].baseX;
        baseY=this.objects[this.rootObject].baseY;

        baseX = (((Math.abs(x1)+Math.abs(x2))*mn)/2 - Math.abs(x1) + baseX) - parseInt(this.c_canvas.width)/2;
        baseY = (((Math.abs(y1)+Math.abs(y2))*mn)/2 - Math.abs(y1) + baseY) - parseInt(this.c_canvas.height)/2;

        for(var j=0;j<this.objectNames.length;j++){
	        this.objects[this.objectNames[j]].baseX-=baseX;
	        this.objects[this.objectNames[j]].baseY-=baseY;
        }


    },

}


function rx3d(canvasId)
{

    // canvas
	this.c_canvas = document.getElementById(canvasId);
	this.context = this.c_canvas.getContext("2d");

    this.angle=[];
    this.scale=1;


	this.wPad=this.c_canvas.width;
	this.hPad=this.c_canvas.height;

	// ���������� canvas
    var top=0, left=0;
    while(this.c_canvas) {
        top = top + parseFloat(this.c_canvas.offsetTop);
        left = left + parseFloat(this.c_canvas.offsetLeft);
        this.c_canvas = this.c_canvas.offsetParent;
    }
    this.xPad=Math.round(left);
	this.yPad=Math.round(top);
	this.c_canvas = document.getElementById(canvasId);

	// ��������� ����� �� ��������� � canvas
	this.baseX=Math.round(this.wPad/2);
	this.baseY=Math.round(this.hPad/2);
	// �������� ��������� �����
	this.baseX2=0;
	this.baseY2=0;

	// �������� ���� �����
	this.offset=[0,0,0];

    // ����� ����� � ������� �� ���������
	this.lineColor="#555";
	this.circleColor="#1E93E1";

	// ������� ���������
	this.currentX=0;
	this.currentY=0;
	this.currentZ=0;

	// ������� ������ �����
	this.mouseFrom=[0,0];
	this.mouseMoved=[0,0];
	this.mouseBase=[this.baseX,this.baseY];
	this.mouseDwn=false;
	this.mouseBtn=0;

	// ������� canvas ��� �����������
	this.clear=true;

    // ������ �����
	this.arrPoints=[];/*[
	   [100,100,0],
	   [500,100,0],
	   [300,400,200],
	   [100,100,400],
	   [500,100,400]
	]; */
    // �������� �������� �������.
	this.arrPointsReal=[];/*[
	   [100,100,0],
	   [500,100,0],
	   [300,400,200],
	   [100,100,400],
	   [500,100,400]
	]; */
		;

	this.arrLines=[];/*[
	   [pX, pY, color],    ����� �� �����  pX �� ����� pY   (pX ������ this.arrPoints[x])
	   [pX, pY, color],
	   [pX, pY, color],
	   [pX, pY, color],
	   [pX, pY, color]
	]; */

	this.arrCircle=[];/*[
	   [point, color],    ������   (point �� ������� this.arrPoints)
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
	// ��������� ����� �� ������� ������� �� ���������
	arc3D: function(coord, radius, a1, a2, clc){
	    var x=coord[0]+this.offset[0];
	    var y=coord[1]+this.offset[1];
	    var z=coord[2]+this.offset[2];
	    // ��������� ����������
		var xTo=this.baseX+x;
		var yTo=this.baseY+y;
	    // ���������� ����� �� 2D ������
	    this.context.moveTo(xTo, yTo);
		this.context.arc(xTo, yTo, radius, a1, a2, clc);
        // ��������� ������� �������
		this.currentX=x;
		this.currentY=y;
		this.currentZ=z;

	},

    // ��������� ����� �� ������� ������� �� ���������
	lineTo3D: function(coord){
	    var x=coord[0]+this.offset[0];
	    var y=coord[1]+this.offset[1];
	    var z=coord[2]+this.offset[2];
	    // ��������� ���������� �����
		var xFrom=this.baseX+this.currentX;//
		var yFrom=this.baseY+this.currentY;//
		var xTo=this.baseX+x;//
		var yTo=this.baseY+y;//
	    // ���������� ����� �� 2D ������
		this.context.moveTo(xFrom, yFrom);
		this.context.lineTo(xTo, yTo);
        // ��������� ������� �������
		this.currentX=x;
		this.currentY=y;
		this.currentZ=z;
	},

    // ����������� ������� �������
	moveTo3D: function (coord)	{
		this.currentX=coord[0]+this.offset[0];
		this.currentY=coord[1]+this.offset[1];
		this.currentZ=coord[2]+this.offset[2];
	},

    //
	saveRealPoints: function (points){
	    this.arrPointsReal=[];
	    for(var i=0;i<points.length;i++){
	    	this.arrPointsReal[i]=[points[i][0], points[i][1], points[i][2]];	    }
	},


	// ��������� ������� ����
	mouseAction: function (event, action){
        //alert(action);
	    // ������ ����
	    if (event.which == null) this.mouseBtn= (event.button < 2) ? "LEFT" : ((event.button == 4) ? "MIDDLE" : "RIGHT"); // >
	    else this.mouseBtn=(event.which < 2) ? "LEFT" : ((event.which == 2) ? "MIDDLE" : "RIGHT");  //  >

	    var xmouse=event.clientX-this.xPad;
	    var ymouse=event.clientY-this.yPad;


	    // ��������� �������
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

	        if(this.mouseBtn=="LEFT"){// ����������� ������ ������� ����
		    	this.baseX=this.mouseBase[0]+this.mouseMoved[0];
		    	this.baseY=this.mouseBase[1]+this.mouseMoved[1];
	        }
	        else{
				 if(this.mouseBtn=="RIGHT"){				 	 this.arrPoints=this.turn3D(this.arrPoints, [this.mouseMoved[1],this.mouseMoved[0],0]);
				 	 //this.arrPointsReal=this.turn3D(this.arrPointsReal, [this.mouseMoved[1],this.mouseMoved[0],0]);				 }
				 else{				 	 this.arrPoints=this.turn3D(this.arrPoints, [0,0,this.mouseMoved[1]]);
				 	 //this.arrPointsReal=this.turn3D(this.arrPointsReal, [0,0,this.mouseMoved[1]]);				 }
				 this.mouseFrom[0]=xmouse;
				 this.mouseFrom[1]=ymouse;
	        }
	    }
	    else if(action=='wheel'){
	    	this.arrPoints=this.scale3D(this.arrPoints, 1+(event.deltaY/1000));
	    	//this.arrPointsReal=this.scale3D(this.arrPointsReal, 1+(event.deltaY/1000));
	    }

	    // document.getElementById("zz").value=mouseMoved[0]+" x "+mouseMoved[1]+", ["+baseX+" x "+baseY+"]";
        this.redraw();
	},
    // �����������
    redraw: function(){
	    // ������ �����
		if(this.clear)this.c_canvas.width = this.c_canvas.width;// ������� ������
	    // this.drawGrid();
		this.draw();
    },
    draw: function(){
	    // ������ �����
	    for(var i=0;i<this.arrLines.length;i++){
        	this.context.beginPath();
            this.moveTo3D(this.arrPoints[this.arrLines[i][0]]);
			this.lineTo3D(this.arrPoints[this.arrLines[i][1]]);
			this.context.strokeStyle = (this.arrLines[i][2])?this.arrLines[i][2]:this.lineColor;
			this.context.stroke();
   			this.context.closePath();
	    }
        // ������ ������
	    for(var i=0;i<this.arrCircle.length;i++){
        	this.context.beginPath();

        	this.arc3D(this.arrPoints[this.arrCircle[i][0]], 3, 0, Math.PI * 2, false);
			this.context.strokeStyle = (this.arrCircle[i][1]!=undefined)?this.arrCircle[i][1]:this.circleColor;
			this.context.stroke();
   			this.context.closePath();
	    }
    },

	// �������� ������� ����� ������ ������ (0,0,0)
	turn3D: function (pointArr, angle)
	{
		var x, y, z;
		this.angle[this.angle.length]=[angle[0], angle[1], angle[2]];
		//this.angle[0]+=angle[0];
		//this.angle[1]+=angle[1];
		//this.angle[2]+=angle[2];

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


    // �������� ������� ����� ������ ������ (0,0,0) ������� �� ��������� �������
    rotate3D: function (angle){
		var x, y, z;
		// ������ ��������
        //x=-this.angle[0];
        //y=this.angle[1]* -1;
        //z=-this.angle[2];
        //alert(this.arrPoints[0]);
        alert(x+"x"+y+"x"+z);
        this.arrPoints=this.turn3D(this.arrPoints, [0,y,0]);
        //alert(this.arrPoints[0]);
        //this.arrPointsReal=turn3D(this.arrPointsReal, [x,y,z]);

        /*
        x=angle[0];
        y=angle[1];
        z=angle[2];
		this.angle[0]+=angle[0];
		this.angle[1]+=angle[1];
		this.angle[2]+=angle[2];
		*/    },
    // �������� ��� ����� ������� ������������ �� ������ ������
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
	// �������� ��� ����� ������� ���, ��� ��������� ����� ����� � ����������� [0,0,0]
	setZero: function (pointArr, point){
		for (var i=0; i < pointArr.length; i++)
		{
		    pointArr[i][0]-=point[0];
		    pointArr[i][1]-=point[1];
		    pointArr[i][2]-=point[2];
		}
		return pointArr;
	},
	// �������� ��� ����� �������
	moveZero: function (pointArr, point){
		for (var i=0; i < pointArr.length; i++)
		{
		    pointArr[i][0]+=point[0];
		    pointArr[i][1]+=point[1];
		    pointArr[i][2]+=point[2];
		}
		return pointArr;
	},
    // ���������������
	scale3D: function (pointArr, mod){
		for (var i=0; i < pointArr.length; i++)
		{
			pointArr[i][0] = pointArr[i][0]*mod;
			pointArr[i][1] = pointArr[i][1]*mod;
			pointArr[i][2] = pointArr[i][2]*mod;
		}
        this.scale*=mod;
		return pointArr;
	},
	// ����������� �����
	mirror: function (pointArr){
		pointArr=this.turn3D(pointArr, [180,0,0]);
		this.angle[0]=[0,0,0];
		return pointArr;
	},

	// ����������� ��������� �������
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

	// �������� ���������
	removeListenerfunction (event) {
	    var instance = event.instance;
	    if (instance.removeEventListener) instance.removeEventListener(event.name, event.listener, false);
	    else if (instance.detachEvent) instance.detachEvent("on" + event.name, event.listener);
	},

    // ���������� �����
	drawGrid: function (){

		this.context.beginPath();
		// ����� x20
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
		// ����� x100
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

		// ������
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
