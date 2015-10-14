<?php

// тест




include_once("ServoTimeLine.cls.php");
$tl=new rxServoTimeLine();


// первая модель передвижения----------------------------------------------------------------

$frmPack=array(
	"s1"=>array(
	    array(// 1й кадр
	    	"angle"=>0,
	    	"time"=>0,
	    	"speed"=>STL_SPEED_ACCELERATION
	    ),
	    array(// 2й кадр
	    	"angle"=>45,
	    	"time"=>1000,
	    	"speed"=>STL_SPEED_NORMAL
	    ),
	    array(// 3й кадр
	    	"angle"=>135,
	    	"time"=>4000,
	    	"speed"=>STL_SPEED_DECELERATION
	    ),
	    array(// 4й кадр
	    	"angle"=>180,
	    	"time"=>5000,
	    	"speed"=>STL_SPEED_NORMAL
	    )
	),
	"s2"=>array(
	    array(// 1й кадр
	    	"angle"=>0,
	    	"time"=>0,
	    	"speed"=>STL_SPEED_NORMAL
	    ),
	    array(// 4й кадр
	    	"angle"=>180,
	    	"time"=>5000,
	    	"speed"=>STL_SPEED_NORMAL
	    )
	)
);

// создать модель поведения на основе созданных кадров
$model = new servo_modelBlank();
$model->timeWork=5000;
$model->timeFrame=200;
$model->repeats=1;
$model->addKeyFrame($frmPack);
$tl->addModel($model);



// вторая модель передвижения----------------------------------------------------------------

$frmPack2=array(
	"s2"=>array(
	    array(// 1й кадр
	    	"angle"=>0,
	    	"time"=>0,
	    	"speed"=>STL_SPEED_NORMAL
	    ),
	    array(// 4й кадр
	    	"angle"=>180,
	    	"time"=>2000,
	    	"speed"=>STL_SPEED_NORMAL
	    )
	)
);
$model2 = new servo_modelBlank();
$model2->timeWork=2000;
$model2->timeFrame=100;
$model2->repeats=0;
$model2->addKeyFrame($frmPack2);
$tl->addModel($model2);



// подсчитать модели - разложить их по таймлайну.
$tl->calculateTimeLine();

$time=0;
$time2=0;

for($i=0;$i<sizeof($tl->servoKeys);$i++){
    // пауза между фреймами
    $time=$tl->servoKeys[$i];
    if($time2>0)usleep((int) ($time-$time2 * 1000));
    $time2=$time;
    $arrPos=$tl->$servoTimeline[$tl->servoKeys[$i]];

    if($arrPos["s1"]!=''){}// $arrPos["s1"]=угол
    if($arrPos["s2"]!=''){}// $arrPos["s2"]=угол
    //...
    if($arrPos["s12"]!=''){}// $arrPos["s12"]=угол


}



?>
