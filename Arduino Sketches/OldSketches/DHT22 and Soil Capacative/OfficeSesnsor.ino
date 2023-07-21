#include <Wire.h>
#include <Adafruit_Sensor.h>

#include <Servo.h>

#include <SPIFFSReadServer.h>

#include <SPIFFSIniFile.h>

#include <BearSSLHelpers.h>
#include <CertStoreBearSSL.h>
#include <ESP8266WiFi.h>
#include <ESP8266WiFiAP.h>
#include <ESP8266WiFiGeneric.h>
#include <ESP8266WiFiMulti.h>
#include <ESP8266WiFiScan.h>
#include <ESP8266WiFiSTA.h>
#include <ESP8266WiFiType.h>
#include <WiFiClient.h>
#include <WiFiClientSecure.h>
#include <WiFiClientSecureAxTLS.h>
#include <WiFiClientSecureBearSSL.h>
#include <WiFiServer.h>
#include <WiFiServerSecure.h>
#include <WiFiServerSecureAxTLS.h>
#include <WiFiServerSecureBearSSL.h>
#include <WiFiUdp.h>
#include <ESP8266HTTPClient.h>

#include <DNSServer.h>
#include <ESP8266WebServer.h>
#include <ESP8266mDNS.h>
#include <EEPROM.h>
#include <FS.h>
#include <ArduinoJson.h>
#include <OneWire.h>

#include <Adafruit_ADS1015.h>

#include <DHT.h>;

#define DHTPIN D8     // what pin we're connected to
#define DHTTYPE DHT22   // DHT 22  (AM2302)
DHT dht(DHTPIN, DHTTYPE); //// Initialize DHT sensor for normal 16mhz Arduino

Adafruit_ADS1115 ads;



String room = "Office";
int userIDtwo = 1;

String rTemp;
String rHumid;
String potOne;
String potTwo;
String potThree;
String potFour;

char buf[15];

const char fingerprint[] PROGMEM = "60 ee 15 1b ee 99 4d 6c a8 26 a6 9a bc e1 e7 24 17 37 21 ca";

const char *host = "klh17101990.asuscomm.com";
const int httpsPort = 443;  //HTTPS= 443 and HTTP = 80

int resetTimer = 0;

uint8_t pin_led = LED_BUILTIN;
char* ssid = "";
char* password = "ESP8266";
char* mySsid = "ESP8266-KL";

String postData;

ESP8266WebServer server;

IPAddress local_ip(192,168,1,254);
IPAddress gateway(192,168,1,1);
IPAddress netmask(255,255,255,0);

char webpage[] PROGMEM = R"=====(
<html>
  <head>
  </head>
 <body>
  <div class="container">
    <form>
      <div class="Form-style">
        <label for="ssid">SSID</label>
        <br>
        <input type="text" value="" id="ssid" placeholder= "Enter Network SSID"/>
      </div>
        <div class="Form-style">
          <label for="password">Password</label>
          <br>
          <input type="text" value="" type="password" id="password" placeholder="Enter Network Password"/>
        </div>
        <div class="button-holder">
          <button class="button" onclick="myFunction()"> Save </button>
       </div>
     </form>
    <h4 class="h4">Created by KurtisHill</h4>
  </div>

<Style>
.Form-style{
  font-size: 3em;
  box-sizing: 50px;
  text-align: center;
  box-sizing: border-box;
}
.button {
  text-align: center;
}
input[type=text] {
  width: 60%;
  padding: 20px 20px;
  margin: 10px 0;
  box-sizing: border-box;
  font-size: 0.4em;
}
.button {
  background-color: white;
  color: black;
/*  border: 2px solid #4CAF50;*/
  border-radius: 10px;
  padding: 20px 256px;
  text-align: center;

}
.button:hover {
  background-color: #4CAF50;
  color: white;

  background-color: #4CAF50; /* Green */
  border: none;
  color: white;
  padding: 26px 268px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  -webkit-transition-duration: 0.4s; /* Safari */
  transition-duration: 0.4s;
  cursor: pointer;
}
.button-holder {
  text-align: center;
}

.h4 {
  text-align: center;
}
.container {
  background-image: linear-gradient(#F6EF2E, #BCBBA5);
  height: 100%;
  font-family: verdana;
}
</Style>

  
  <script>
    function myFunction()
    {
      console.log("button was clicked");
  
      var ssid = document.getElementById("ssid").value;
      var password = document.getElementById("password").value;
      var data = {ssid:ssid, password:password};

      var xhr = new XMLHttpRequest();
      var url = "/settings";
    

      xhr.onreadystatechange = function() {
        if (this.onreadyState == 4 && this.status == 200)
        {
          console.log(xhr.responseText);
        }        
      };
      xhr.open("POST", url, true);
      xhr.send(JSON.stringify(data));    
    };
  </script>
 </body>
</html>
)=====";




void setup()
{
  //REMEBER TO TRY THE ESP-01 AT 115200
  //Serial.begin(115200, SERIAL_8N1,SERIAL_TX_ONLY);
  Serial.begin(9600);

  server.on("/",[](){server.send_P(200,"text/html",webpage);});
  server.on("/toggle",toggleLED);
  server.on("/settings", HTTP_POST, handleSettingsUpdate);
  server.begin();
  
  WiFi.begin(ssid,password);
  SPIFFS.begin();
  
  Wire.begin(4,5);
  dht.begin();



  wifiConnect();
}



void loop()
{

  potValues();
  tempValues();
  server.handleClient();
  if(WiFi.status()== WL_CONNECTED){
    wifiPost();
  }
  delay(6000);
}







void potValues(){

potOne = String(analogRead(A0));
  
  int16_t adc0, adc1, adc2, adc3;
  potOne = String(ads.readADC_SingleEnded(0));
  potTwo = String(ads.readADC_SingleEnded(1));
  potThree = String(ads.readADC_SingleEnded(2));
  potFour = String(ads.readADC_SingleEnded(3));


  Serial.println("PotOne is:");
  Serial.println(potOne);
  Serial.println("  ");
  Serial.println("PotTwo is:");
  Serial.println(potTwo);
  Serial.println("  ");
  Serial.println("PotThree is:");
  Serial.println(potThree);
  Serial.println("  ");
  Serial.println("PotFour is:");
  Serial.println(potFour);
  Serial.println("  ");
  Serial.println("  ");
  Serial.println("  ");

}




void tempValues(){

  
  

  rTemp = String(dht.readTemperature());
  rHumid = String(dht.readHumidity());
  Serial.print("Humidity: ");
  Serial.print(rHumid);
  Serial.print(" %, Temp: ");
  Serial.print(rTemp);
  Serial.println(" Celsius");
}




void handleSettingsUpdate()
{
  String data = server.arg("plain");
  DynamicJsonBuffer jBuffer;
  JsonObject& jObject = jBuffer.parseObject(data);

  File configFile = SPIFFS.open("/config.json", "w");
  jObject.printTo(configFile);
  configFile.close();

  server.send(200, "application/json", "{\"status\":\"ok\"}");
  delay(500);

  wifiConnect();
}




void wifiConnect(){
  WiFi.softAPdisconnect(true);
  WiFi.disconnect();
  delay(1000);

  if(SPIFFS.exists("/config.json"))
  {
    const char * _ssid = "", *_pass = "";
    File configFile = SPIFFS.open("/config.json", "r");
    if(configFile)
    {
      size_t size = configFile.size();
      std::unique_ptr<char[]> buf(new char[size]);
      configFile.readBytes(buf.get(), size);
      configFile.close();

      DynamicJsonBuffer jsonBuffer;
      JsonObject& jObject = jsonBuffer.parseObject(buf.get());
      if (jObject.success())
      {
        _ssid = jObject["ssid"];
        _pass = jObject["password"];
        WiFi.mode(WIFI_STA);
        WiFi.begin(_ssid, _pass);
        unsigned long startTime = millis();
        while(WiFi.status() != WL_CONNECTED)
        {
         delay(500);
         Serial.print(".");
         Serial.println(".");
         digitalWrite(pin_led, !digitalRead(pin_led));
        }

      }
    }
    if (!configFile) ("File creation failed");
  }


  if (WiFi.status() == WL_CONNECTED)
  {
    
    Serial.print("Connected");

  }
  else
  {
    WiFi.mode(WIFI_AP);
    WiFi.softAPConfig(local_ip, gateway, netmask);
    WiFi.softAP(mySsid, password);
    
    Serial.println("AP MODEK");
  }
  Serial.println("");
  Serial.println("");
  WiFi.printDiag(Serial); 
}






void wifiPost() {

  WiFiClientSecure httpsClient;    //Declare object of class WiFiClient

  Serial.println(host);

  Serial.printf("Using fingerprint '%s'\n", fingerprint);
  httpsClient.setFingerprint(fingerprint);
  httpsClient.setTimeout(15000); // 15 Seconds
  delay(1000);
  
  Serial.print("HTTPS Connecting");
  int r=0; //retry counter
  while((!httpsClient.connect(host, httpsPort)) && (r < 30)){
      delay(100);
      Serial.print(".");
      r++;
  }
  if(r==30) {
    Serial.println("Connection failed");
  }
  else {
    Serial.println("Connected to web");
  }
  
  String getData, Link;
  
  //POST Data
  Link = "/HomeApp/Tempreture.php";

  Serial.print("requesting URL: ");
  Serial.println(host);

 

  httpsClient.print(String("POST ") + Link + " HTTP/1.1\r\n" +
               "Host: " + host + "\r\n" +
               "Content-Type: application/x-www-form-urlencoded"+ "\r\n" +
               "Content-Length: 250" + "\r\n\r\n" +
            //   "&GroupName="+"KGroup"+"&RoomID="+"Office"+"&Bus="+0+"&HSensor1="+"BlueMystic"+"&HSensor2="+"UtopiaHaze"+"&HBus="+2+"&Humid1="+potTwo+"&Humid2="+potThree+"\r\n"+//
               "Temp1="+rTemp+"&Humid1="+rHumid+"&GroupName="+"KGroup"+"&RoomID="+"Office"+"&Bus="+1+"&HSensor2="+"BlueMystic"+"&HSensor3="+"UtopiaHaze"+"&Sensor1="+"Plant"+"&HBus="+3+"&HSensor1="+"Plant"+"&Humid2="+potThree+"&Humid3="+potFour+"\r\n"+  
               "Connection: close\r\n\r\n");

  Serial.println("request sent");
  Serial.println(host + Link);
                  
  while (httpsClient.connected()) {
    String line = httpsClient.readStringUntil('\n');
    if (line == "\r") {
      Serial.println("headers received");
      break;
    }
  }

  Serial.println("reply was:");
  Serial.println("==========");
  String line;
  while(httpsClient.available()){        
    line = httpsClient.readStringUntil('\n');  //Read Line by Line
    Serial.println(line); //Print response
  }
  Serial.println("==========");
  Serial.println("closing connection");
}



void toggleLED()
{
  digitalWrite(pin_led,!digitalRead(pin_led));
  server.send_P(200,"text/html", webpage);
}
