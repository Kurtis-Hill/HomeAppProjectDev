#include <Wire.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_BME280.h>
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

#include <Adafruit_Sensor.h>
#include <DNSServer.h>
#include <ESP8266WebServer.h>
#include <ESP8266mDNS.h>
#include <EEPROM.h>
#include <FS.h>
#include <ArduinoJson.h>
#include <OneWire.h>
#include <DallasTemperature.h>

#define ONE_WIRE_BUS 3


OneWire oneWire(ONE_WIRE_BUS);

DallasTemperature sensors(&oneWire);


DeviceAddress sensor1 = { 0x28, 0xAA, 0xC9, 0x44, 0x48, 0x14, 0x1, 0x2F, };
DeviceAddress sensor2 = { 0x28, 0xAA, 0xBE, 0xE8, 0x47, 0x14, 0x1, 0xF7 };

#define SEALEVELPRESSURE_HPA (1013.25)

Adafruit_BME280 bme;

String fishTemp;
String turtleTemp;

String rTemp;
String rHumid;

String sensor3 = "LRoomTemp"; 

const char fingerprint[] PROGMEM = "60 ee 15 1b ee 99 4d 6c a8 26 a6 9a bc e1 e7 24 17 37 21 ca";

const char *host = "klh17101990.asuscomm.com";
const int httpsPort = 443;  //HTTPS= 443 and HTTP = 80



uint8_t pin_led = LED_BUILTIN;
char* ssid = "";
char* password = "";
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





void setup() {
  
  Serial.begin(115200, SERIAL_8N1,SERIAL_TX_ONLY);
  
  server.on("/",[](){server.send_P(200,"text/html",webpage);});
  server.on("/toggle",toggleLED);
  server.on("/settings", HTTP_POST, handleSettingsUpdate);
  server.begin();
  
  WiFi.begin(ssid,password);
  SPIFFS.begin();
  Wire.begin(0,2);

//  if (!bme.begin(0x76)) {
//    Serial.println("Could not find a valid BME280 sensor, check wiring!");
//    while (1);
//  }
  sensors.begin();

  //Keep this at the end of the setup
  wifiConnect();

}

void loop() {

  Serial.println("hey");   
  //tempValues();
  //busTemp();
  
  server.handleClient();
  if(WiFi.status()== WL_CONNECTED){
   wifiPost();
   }
 delay(6000);
}


void tempValues(){
  Serial.print("Room Temperature = ");
  Serial.print(bme.readTemperature());
  Serial.println("*C");

  Serial.print("Room Pressure = ");
  Serial.print(bme.readPressure() / 100.0F);
  Serial.println("hPa");

  Serial.print("Approx. Altitude = ");
  Serial.print(bme.readAltitude(SEALEVELPRESSURE_HPA));
  Serial.println("m");

  Serial.print("Room Humidity = ");
  Serial.print(bme.readHumidity());
  Serial.println("%");

  Serial.println();

  rTemp = String(bme.readTemperature());
  rHumid = String(bme.readHumidity());
}



void busTemp() {

 Serial.print("Requesting Bus temperatures...");
 sensors.requestTemperatures(); // Send the command to get temperatures
 Serial.println("DONE");
  
 Serial.println("Turtle Tank:(*C): ");
 Serial.println(sensors.getTempC(sensor1)); 
  
 
 Serial.println("Fish Tank:(*C): ");
 Serial.println(sensors.getTempC(sensor2)); 

 fishTemp = String(sensors.getTempC(sensor2));
 turtleTemp = String(sensors.getTempC(sensor1));
 Serial.println("");
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
               "Temp3="+rTemp+"&Humid1="+rHumid+"&GroupName="+"KGroup"+"&RoomID="+"LivingRoom"+"&Bus="+3+"&Sensor1="+"TurtleTank"+"&Sensor2="+"FishTank"+"&Temp1="+turtleTemp+"&Temp2="+fishTemp+"&HSensor1="+sensor3+"&Sensor3="+sensor3+"&HBus="+1+"\r\n"+
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

void toggleLED(){
  digitalWrite(pin_led,!digitalRead(pin_led));
  server.send_P(200,"text/html", webpage);
}
