<?php

// управление несколькими сервами распределённое по времени.
// "модель поведения" - на какой угол, в какое время и какая серва встает. Массив ключевых точек.
// далее на таймлайне распределяем модели.


// распределение скорости между фреймами
define ("STL_SPEED_NORMAL", 0);// нормальная скорость
define ("STL_SPEED_ACCELERATION", 1);// ускорение
define ("STL_SPEED_DECELERATION", 2);// замедление


class servo_modelBlank // чистая модель поведения
{
	var $servos=array(// 12 servos
    	"s1"=>array(),
    	"s2"=>array(),
    	"s3"=>array(),
    	"s4"=>array(),
    	"s5"=>array(),
    	"s6"=>array(),
    	"s7"=>array(),
    	"s8"=>array(),
    	"s9"=>array(),
    	"s10"=>array(),
    	"s11"=>array(),
    	"s12"=>array()
    );

    var $timeWork=0;// общее время работы скрипта timeLine  миллисекунды
    var $timeFrame=0;// длительность одного кадра           миллисекунды
    var $repeats=0;// количество повторов

    public function addKeyFrame($frmPack){
        // раскидываем полученные фреймы по местам
		foreach($this->servos as $name=>$data){
            if(is_array($frmPack[$name])){
                for($i=0;$i<sizeof($frmPack[$name]);$i++){
                    $this->servos[$name][]=$frmPack[$name][$i];
                }
            }
		}
    }
}



class rxServoTimeLine // перемещение по временной шкале используя модель поведения
{

	public $behaviorModels = array();// модель поведения
	public $arrServos=array("s1","s2","s3","s4","s5","s6","s7","s8","s9","s10","s11","s12");
	public $servoKeys=array();// массив времени исполнения кадра
	public $servoTimeline=array();// позиция серв в определённый момент времени.




	public function addModel($model)
	{
	    //$model=$this->calculateTimeLine($model);
        $this->behaviorModels[]=$model;

	}

    // посчитать позицию для всех кадров во всех моделях
	public function calculateTimeLine()
	{
        $globTime=0;
        $total=array();
        $this->servoKeys=array();
	    for($j=0;$j<sizeof($this->behaviorModels);$j++){// цикл по моделям
	    	$model=$this->behaviorModels[$j];
	    	$modelRepeat=$model->repeats;

	    	do{// когда 1 модель повторяется неск. раз

	    	    for($k=0;$k<sizeof($this->arrServos);$k++){// перебираем сервы
                    $name=$this->arrServos[$k];
		            if(sizeof($model->servos[$name])>0){// если для данного сервы есть модель поведения...
		                $frames=$model->servos[$name];// массив кадров одной сервы
                        for($i=0;$i<=$model->timeWork;$i+=$model->timeFrame){
                            $angle=$this->calcFrame($i, $frames);
                            $tm=$globTime+$i;
                            $total[$tm][$name]=$angle;
                            if(!in_array($tm, $this->servoKeys))$this->servoKeys[]=$tm;
                        }
		            }
		        }
		        $modelRepeat--;
            	$globTime+=$model->timeWork;
	        }while($modelRepeat>=0);

        }

        $this->servoTimeline=$total;
	}

    public function calcFrame($time, $arrKeyFrame)
	{
	    $from=0;
	    $to=999999999;
	    $fromCnt=0;
	    $toCnt=0;
	    for($i=0;$i<sizeof($arrKeyFrame);$i++){
            if(($arrKeyFrame[$i]["time"]<=$time)&&($arrKeyFrame[$i]["time"]>=$from)){
            	$from=$arrKeyFrame[$i]["time"];
            	$fromCnt=$i;
            }
            if(($arrKeyFrame[$i]["time"]>=$time)&&($arrKeyFrame[$i]["time"]<=$to)){
            	$to=$arrKeyFrame[$i]["time"];
            	$toCnt=$i;
            }
		}
		if($to==0){// после последнего фрейма
		    $to=$from;
		}

        if($from == $to)return $arrKeyFrame[$fromCnt]["angle"];

        if($arrKeyFrame[$fromCnt]["speed"]==STL_SPEED_NORMAL){// обычная скорость без ускорений/замедлений
        	$a=$arrKeyFrame[$fromCnt]["angle"] + ($time-$from) * ($arrKeyFrame[$toCnt]["angle"]-$arrKeyFrame[$fromCnt]["angle"]) / ($to-$from);
        }
        else if($arrKeyFrame[$fromCnt]["speed"]==STL_SPEED_ACCELERATION){// ускорение
            $b=($time-$from)/($to-$from);// множитель линейно
            $b=1+cos( PI()+($b*(PI()/2)));// множитель
            $a=($arrKeyFrame[$toCnt]["angle"]-$arrKeyFrame[$fromCnt]["angle"]);
            $a=$arrKeyFrame[$fromCnt]["angle"] +  $a*$b;
        }
        else if($arrKeyFrame[$fromCnt]["speed"]==STL_SPEED_DECELERATION){// замедление
            $b=($time-$from)/($to-$from);// множитель линейно
            $b=sin($b*(PI()/2));// множитель
            $a=($arrKeyFrame[$toCnt]["angle"]-$arrKeyFrame[$fromCnt]["angle"]);
            $a=$arrKeyFrame[$fromCnt]["angle"] +  $a*$b;
        }
        return  "$a";
    }
}





?>
