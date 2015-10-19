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

long filePosition = 0;             // позиция считывания из файла
boolean fileReading = false;      // происходит считывание из файла модели 

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
  
  serialEvent(); //call the function
  // print the string when a newline arrives:
  if (stringComplete) {
    serialDataProcess(inputString);
    // clear the string:
    inputString = "";
    stringComplete = false;
  }
  
  if (stringComplete2) {
    Serial2.println("next: "+inputString2);
    serialDataProcess(inputString2);
    // clear the string:
    inputString2 = "";
    stringComplete2 = false;
  }
}



void serialEvent() {
  while (Serial.available()) {// получить команду через провод
    // get the new byte:
    char inChar = (char)Serial.read();
    // add it to the inputString:
    inputString += inChar;
    // if the incoming character is a newline, set a flag
    // so the main loop can do something about it:
    if (inChar == '\n') {
      stringComplete = true;
    }
  }
  while (Serial2.available()) {// получить команду по воздуху
    // get the new byte:
    char inChar2 = (char)Serial2.read();
    // add it to the inputString:
    inputString2 += inChar2;
    // if the incoming character is a newline, set a flag
    // so the main loop can do something about it:
    if (inChar2 == '\n') {
      stringComplete2 = true;
    }
  }
}

// получить строку из файла
void getLine(String filename) {//   
  File dataFile = SD.open(filename);
  if (dataFile){
    dataFile.seek(filePosition);// перейти к последней точке считывания
    fileReading=true;
    //while (dataFile.available()) 
    do{
      char inChar = (char)dataFile.read();
      Serial.write(inChar);
      inputString += inChar;
      if (inChar == '\n') {
        stringComplete = true;
        fileReading=false;
      }
    }
    while(fileReading);
    filePosition=dataFile.position();
    dataFile.close();
  }
  else {
    Serial.println("error opening "+filename);
  }

  
}

void serialDataProcess(String data) {
  data.trim();
  Serial.println("Getted data: "+data);
  if (data.substring(0,1) == "s") {// управление сервами
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








