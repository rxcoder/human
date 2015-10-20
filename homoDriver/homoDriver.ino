// s0250;04130;03130;1150;09130;10130

#include <SPI.h>
#include <SD.h>

#include <Servo.h>


const int chipSelect = 53;// контакт CS для SPI

// define global variables
int sp=1500;
int dr=0;
// define servos


Servo s30;
Servo s31;
Servo s32;
Servo s33;
Servo s34;
Servo s35;
Servo s36;
Servo s37;
Servo s38;
Servo s39;
Servo s40;
Servo s41;

//
String inputString = "";         // a string to hold incoming data
boolean stringComplete = false;  // whether the string is complete
// Serial 2
String inputString2 = "";         // a string to hold incoming data
boolean stringComplete2 = false;  // whether the string is complete

// модели
long filePosition = 0;             // позиция считывания из файла
boolean fileReading = false;      // происходит считывание из файла модели 
String currentModel="";// текущая обрабатываемая модель
String reserveModel="";// очередь моделей через точку с запятой ";"
unsigned long pause=0;// замена для delay

void setup()
{
  
  
  s30.attach(30);
  s31.attach(31);
  s32.attach(32);
  s33.attach(33);
  s34.attach(34);
  s35.attach(35);
  s36.attach(36);
  s37.attach(37);
  s38.attach(38);
  s39.attach(39);
  s40.attach(40);
  s41.attach(41);

  
  
  s30.write(88);// стопа левая - 15(внутрь на ребре стопы)...90...130(наружу)
  s31.write(90);// голеностоп левый  - 0(на пятке)...90...180(на носочке)
  s32.write(48);// колено левое - 45...180(согнуто)
  s33.write(90);// бедро левое - 180(вперед)...90...0(назад)
  s34.write(93);// тазобедренный сустав левый
    // 86 - граница, чтобы не мешать другой ноге 
    // 70 - центр корпуса
    // 180 - нога поднята влево (провисает градусов на 10)
  s35.write(90);// таз - 118(внутрь)...90...40(пятки вместе носки в розь)
  
  s36.write(89);// таз - 60(внутрь)...90...145(пятки вместе носки в розь)
  s37.write(92);// тазобедренный сустав правый
    // 98 - граница, чтобы не мешать другой ноге 
    // 117 - центр корпуса
    // 0 - нога поднята вправо (провисает градусов на 5)
  s38.write(97);// бедро правое - 180(вперед)...90...0(назад)
  s39.write(50);// колено правое - 45...180(согнуто)
  s40.write(90);// голеностоп правый  - 0(на пятке)...90...180(на носочке)
  s41.write(94);// стопа правая - 170(внутрь на ребре стопы)...90...55(наружу)
  
  

  Serial.begin(9600);// initialize serial:
  inputString.reserve(200);// reserve 200 bytes for the inputString:
  
  Serial2.begin(9600);// initialize serial 2: 57600
  inputString2.reserve(200);// reserve 200 bytes for the inputString:

  Serial.println(SD_CHIP_SELECT_PIN); //53
  Serial.println(SPI_MOSI_PIN);       //51
  Serial.println(SPI_MISO_PIN);       //50
  Serial.println(SPI_SCK_PIN);        //52
  //Serial.println(MEGA_SOFT_SPI);        //

  pinMode(chipSelect, OUTPUT);

  // see if the card is present and can be initialized:
  if (!SD.begin(chipSelect)) {
    Serial.println("Card failed, or not present");
    // don't do anything more:
    return;
  }
  Serial.println("card initialized.");
  
}

void loop()
{
  
  serialEvent();

  if (stringComplete) {
    serialDataProcess(inputString);
    inputString = "";
    stringComplete = false;
  }
  
  if (stringComplete2) {
    Serial2.println("next: "+inputString2);
    serialDataProcess(inputString2);
    inputString2 = "";
    stringComplete2 = false;
  }

  if(pause<millis())goModel();// если не пауза - запустить модель
  
}



void serialEvent() {
  while (Serial.available()) {// получить команду через провод
    char inChar = (char)Serial.read();
    if (inChar == '\n') stringComplete = true;
    else inputString += inChar;
  }
  while (Serial2.available()) {// получить команду по воздуху
    // get the new byte:
    char inChar2 = (char)Serial2.read();
    if (inChar2 == '\n') stringComplete2 = true;
    else inputString2 += inChar2;
  }
}

// получить строку из файла
int getLine(String filename) {//   
  int ret=0;
  File dataFile = SD.open(filename);
  if (dataFile){
    dataFile.seek(filePosition);// перейти к последней точке считывания
    fileReading=true;
    //while (dataFile.available()) 
    do{
      char inChar = (char)dataFile.read();
      Serial.write(inChar);
      if (inChar == '\n') {
        stringComplete = true;
        fileReading=false;
      }
      else inputString += inChar;
    }
    while(fileReading);
    filePosition=dataFile.position();
    ret=dataFile.available();
    dataFile.close();
  }
  else {
    Serial.println("error opening "+filename);
  }

  return ret;
}

void serialDataProcess(String data) {
  data.trim();
  Serial.println("Getted data: "+data);
  if (data.substring(0,1) == "s") {// управление сервами   "s0250;04130;03130;1150;09130;10130"
    data=data.substring(1);
    Serial.println("next: "+data);

    int firstClosingBracket = data.indexOf(';');
    String data1="";
    String data2="";
    String data3="";
    int srv=0;
    int a=0;
    
    if(firstClosingBracket>=0){
      do
      {
        data1=data.substring(0,firstClosingBracket);
        data2=data1.substring(0,2);
        data3=data1.substring(2);
        // повернуть серву
        setServo(data2.toInt(), data3.toInt());
        
        data=data.substring(firstClosingBracket+1);
      
        firstClosingBracket = data.indexOf(';', firstClosingBracket);
      } while (firstClosingBracket>=0);
    }
    data1=data.substring(0,firstClosingBracket);
    data2=data1.substring(0,2);
    data3=data1.substring(2);
    // повернуть серву
    setServo(data2.toInt(), data3.toInt());
    
Serial.println("");
    
    
  }
  else if (data.substring(0,1) == "m") {// запуск "модели" движения серв       "m modelName opt"
    /*
    m modelName opt
      m - указание на действие - загрузка модели
      modelName - имя модели
      opt - как запустить модель:
        0 - добавить модель в массив для последовательного выполнения (когда работает последовательно несколько моделей)
        1 - удалить старые д-я(очистить массив моделей) и приступить к выполнению текущей modelName
    */
    data=data.substring(2); 

    int sep = data.indexOf(' ');
    String data1=data.substring(0,sep);
    String data2=data.substring(sep+1);
    
    Serial.println("model: '"+data1+"', starting mode: '"+data2+"'");

    // запуск модели
    addModel(data1, data2);
  }
  else if (data.substring(0,1) == "p") {// пауза в миллисекундах         "p100;"
    data=data.substring(1); 
    int sep = data.indexOf(';');
    if(sep > 0)data=data.substring(0,sep);

    long ms = data.toInt();// узнали кол-во миллисекунд для паузы
    pause=millis()+ms;
    Serial.println("pause: "+data+"ms");

  }
  else if (data.substring(0,1) == "c") {// запуск "модели" движения серв       "c stop"
    data=data.substring(2); 
    if(data=="stop"){
      reserveModel="";
      currentModel="";
    }
    Serial.println("command: "+data);

  }


}

void addModel(String modelName, String opt){
  /*
    modelName - имя модели
    opt - как запустить модель:
      0 - добавить модель в массив для последовательного выполнения (когда работает последовательно несколько моделей)
      1 - удалить старые д-я(очистить массив моделей) и приступить к выполнению текущей modelName
  */
  if(currentModel!=""){// сейчас происходит считывание из модели
    if(opt=="0")reserveModel+=modelName+";";
    else if(opt=="1"){
      currentModel=modelName;
      reserveModel="";
    }
  }
  else{
    currentModel=modelName;
    reserveModel="";
  }
  fileReading=true;




  
}

void goModel(){// если есть модель - считать и выполнить строчку
  /*
  String currentModel="";// текущая обрабатываемая модель
  String reserveModel="";// очередь моделей через точку с запятой ";"
  long filePosition = 0;// позиция считывания из файла
  boolean fileReading = false;// происходит считывание из файла модели 
  
  available = getLine(String filename) {
  */
  // if(pause>=millis())return;// сейчас ещё работает пауза
  
  if(currentModel!=""){// происходит считывание из файла
    int avbl = getLine("models/"+currentModel+".m");
    if(avbl<=0){// файл модели считан
      if(reserveModel!=""){// есть ещё модели в очереди
        int pos=reserveModel.indexOf(';');
        currentModel=reserveModel.substring(0,pos);
        reserveModel=reserveModel.substring(pos+1);
      }
      else{
        currentModel="";
      }
    }
  }
 
}


boolean setServo(int num, int angle){
  if((num<=0)||(num>30))return false;

        Serial.print("Servo ");
        Serial.print(num);
        Serial.print(" at ");
        Serial.print(angle);
        Serial.print(", ");

  if(num==1){//90
    if((angle<15)||(angle>130))return false;
    s30.write(angle);// стопа левая - 15(внутрь на ребре стопы)...90...130(наружу)
    
  }
  else if(num==2){//90
    if((angle<0)||(angle>180))return false;
    s31.write(angle);// голеностоп левый  - 46(на пятке)...90...180(на носочке)
  } 
  else if(num==3){//45
    if((angle<0)||(angle>180))return false;
    s32.write(angle);// колено левое - 45...180(согнуто)
  } 
  else if(num==4){//90
    if((angle<0)||(angle>180))return false;
    s33.write(angle);// бедро левое - 180(вперед)...90...0(назад)
  } 
  else if(num==5){//90
    if((angle<45)||(angle>180))return false;
    s34.write(angle);// тазобедренный сустав левый
      // 86 - граница, чтобы не мешать другой ноге 
      // 70 - центр корпуса
      // 180 - нога поднята влево (провисает градусов на 10)
  } 
  else if(num==6){//90
    if((angle<40)||(angle>118))return false;
    s35.write(angle);// таз - 118(внутрь)...90...40(пятки вместе носки в розь)
    
  } 
  else if(num==7){//90
    if((angle<60)||(angle>145))return false;
    s36.write(angle);// таз - 60(внутрь)...90...145(пятки вместе носки в розь)
    
  } 
  else if(num==8){//90
    if((angle<0)||(angle>135))return false;
    s37.write(angle);// тазобедренный сустав правый
      // 98 - граница, чтобы не мешать другой ноге 
      // 117 - центр корпуса
      // 0 - нога поднята вправо (провисает градусов на 5)
  }   
  else if(num==9){//90
    if((angle<0)||(angle>180))return false;
    s38.write(angle);// бедро правое - 180(вперед)...90...0(назад)
  }   
  else if(num==10){//45
    if((angle<0)||(angle>180))return false;
    s39.write(angle);// колено правое - 45...180(согнуто)
  }   
  else if(num==11){//90
    if((angle<0)||(angle>180))return false;
    s40.write(angle);// голеностоп правый  - 44(на пятке)...90...180(на носочке)
  }   
  else if(num==12){//90
    if((angle<55)||(angle>170))return false;
    s41.write(angle);// стопа правая - 170(внутрь на ребре стопы)...90...55(наружу)
  }      

}








