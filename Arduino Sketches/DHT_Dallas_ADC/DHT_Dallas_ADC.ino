#include <Wire.h>
#include <Adafruit_Sensor.h>

#include <SPIFFSReadServer.h>
#include <SPIFFSIniFile.h>

#include <BearSSLHelpers.h>
#include <CertStoreBearSSL.h>
#include <ESP8266WiFi.h>
#include <ESP8266WiFiAP.h>
#include <ESP8266WiFiGeneric.h>
#include <ESP8266WiFiMulti.h>
#include <ESP8266WiFiScan.h>
#include <ESP8266WiFiSTA.h>F
#include <ESP8266WiFiType.h>
#include <WiFiClient.h>
#include <WiFiClientSecure.h>
#include <WiFiClientSecureAxTLS.h>
#include <WiFiClientSecureBearSSL.h>
#include <WiFiServer.h>
#include <WiFiServerSecure.h>
#include <WiFiUdp.h>
#include <ESP8266HTTPClient.h>

#include <DNSServer.h>
#include <ESP8266WebServer.h>
#include <ESP8266mDNS.h>
#include <EEPROM.h>
#include <FS.h>

#include <ArduinoJson.h>

#include <OneWire.h>
#include <DallasTemperature.h>

#include <Adafruit_ADS1015.h>

#include <DHT.h>;
#define DHTPIN 13  // what pin we're connected to
#define DHTTYPE DHT22   // DHT 22  (AM2302)
DHT dht(DHTPIN, DHTTYPE);

OneWire oneWire(0);
OneWire testWire(0);
DallasTemperature sensors(0);

//Web bits
const char fingerprint[] PROGMEM = "60 ee 15 1b ee 99 4d 6c a8 26 a6 9a bc e1 e7 24 17 37 21 ca";
const char *host = "klh17101990.asuscomm.com";
const int httpsPort = 443; 
uint8_t pin_led = LED_BUILTIN;
char* ssid = "";
char* password = "";
char* mySsid = "ESP8266-KLH";
ESP8266WebServer server;
IPAddress local_ip(192,168,1,254);
IPAddress gateway(192,168,1,1);
IPAddress netmask(255,255,255,0);

char resetPage[] PROGMEM = R"=====(
<html>
   <head>
  </head>
  <body>
    <h1>Reset Device</h1>
    <form action="/resetDevice" method="post">
    <button action="submit" value="submit">Submit</button>
    </form>
  </body>
  </html>
  )=====";

  
char webpage[] PROGMEM = R"=====(
<html>
   <head>
  </head>
  <body>
    <div id="background-wrap">
        <div class="bubble x1"></div>
        <div class="bubble x2"></div>
        <div class="bubble x3"></div>
        <div class="bubble x4"></div>
        <div class="bubble x5"></div>
        <div class="bubble x6"></div>
        <div class="bubble x7"></div>
        <div class="bubble x8"></div>
        <div class="bubble x9"></div>
        <div class="bubble x10"></div>
    </div>
    <form>
      <div class="Form-style">
        <h3>Enter Your Wifi Credentials Remeber To Use a 2.4GHZ Signal</h3>
        <a style="font-size:0.65em;" href=""id="help">Help</a>
        <br>
        <br>
        <label for="ssid">SSID</label>
        <br>
        <input type="text" value="" id="ssid" placeholder= "Enter Network SSID"/>
      </div>
        <div class="Form-style">
          <label for="password">Password</label>
          <br>
          <input value="" type="password" id="password" placeholder="Enter Network Password"/>
        </div>
        <div class="Form-style">
          <h3>Enter Account Information</h3>
          <label for="groupName">Group Name</label>
          <br>
          <input value="" type="text" id="groupName" placeholder="Enter Your Account GroupName"/>
        </div>

        <div class="Form-style">
          <label for="room">Room</label>
          <br>
            <input value="" type="text" id="room" placeholder="Enter The Room the Sensor is to be Placed"/>
        </div>
        <br>
        <div class="Form-style">
          <h2>Enter Sensor Information</h2>
          <label for="room" class="heading">Temperature and Humidity Sensor</label>
          <br>
          <input type="radio" class="checkmark" name="tempHumidRadio" value="Yes" onchange="hiddenDisplay('tempDisplay')">Yes<br></input>
          <input type="radio" class="checkmark" name="tempHumidRadio" value="No" onchange="hiddenDisplay('tempDisplay')" checked>No<br></input>
        </div>
        <div class="Form-style" id="tempDisplay" style="display: none;">
          <input value="" type="text" id="tempHumid" placeholder="Enter The Name of the Sensor"/>
          <br>
        </div>


        <div class="Form-style">
          <label for="room" class="heading">Temperature Bus Sensor</label>
          <br>
            <input type="radio" class="checkmark" name="busTempRadio" value="Yes" onchange="hiddenDisplay('other')">Yes</input><br>
            <input type="radio" class="checkmark" name="busTempRadio" value="No" onchange="hiddenDisplay('other')" checked>No</input>
        </div>
        <div class="Form-style" id="other" style="display: none;">

          <input value="" type="text"  id="busTemp1" placeholder="Enter The First Sensor Name"/>

          <br><br>
          <input value="" type="text"  id="busTemp2" placeholder="Enter The Second Sensor Name"/>
          <br><br>
          <input value="" type="text" id="busTemp3" placeholder="Enter The Third Sensor Name"/>
          <br><br>
          <input value="" type="text"  id="busTemp4" placeholder="Enter The Fourth Sensor Name"/>
          <br><br>
          <input value="" type="text"  id="busTemp5" placeholder="Enter The Fith Sensor Name"/>
          <br><br>
          <input value="" type="text"  id="busTemp6" placeholder="Enter The Sixth Second Name"/>
          <br><br>
          <input value="" type="text"  id="busTemp7" placeholder="Enter The Seventh Sensor Name"/>
          <br><br>
          <input value="" type="text"  id="busTemp8" placeholder="Enter The Eith Sensor Name"/>
          <br><br>
        </div>
        <div class="Form-style">
          <label for="room">Analog Sensor</label>
          <br>
            <input type="radio" class="checkmark" name="AnalogCheck" value="Yes" onchange="hiddenDisplay('Analog')">Yes<br>
            <input type="radio" class="checkmark" name="AnalogCheck" value="No" checked onchange="hiddenDisplay('Analog')">No<br>
        </div>
        <div class="Form-style" id="Analog" style="display: none;">
          <label for="AnalogSensorNames" class="heading">Analog Sensors Names</label>
          <br>
          <input value="" type="text" id="AnalogName1" placeholder="Enter The First Sensor Name"/>
          <br><br>
          <input value="" type="text" id="AnalogName2" placeholder="Enter The Second Sensor Name"/>
          <br><br>
          <input value="" type="text" id="AnalogName3" placeholder="Enter The Third Sensor Name"/>
          <br><br>
          <input value="" type="text" id="AnalogName4" placeholder="Enter The Forth Sensor Name"/>
          <br><br>
        </div>
        <div class="button-holder">
          <button class="button" onclick="myFunction()"> Save </button>
       </div>
     </form>
    <h4 class="h4">Created by Kurtis Hill</h4>
      <script name="config" src="config"></script>
  <script>
      function hiddenDisplay(name) {
        var x = document.getElementById(name);
        if (x.style.display === "none") {
          x.style.display = "block";
        } else {
          x.style.display = "none";
        }
      }

      function myFunction() {
        console.log("button was clicked");
        //Vital Info
        var ssid = document.getElementById("ssid").value;
        var password = document.getElementById("password").value;
        var room = document.getElementById("room").value;
        var groupName = document.getElementById("groupName").value;
        var data = {ssid:ssid, password:password, roomID:room, groupName:groupName};
        //Bus temp
        var busTemp1 =  document.getElementById("busTemp1").value;
        var busTemp2 =  document.getElementById("busTemp2").value;
        var busTemp3 =  document.getElementById("busTemp3").value;
        var busTemp4 =  document.getElementById("busTemp4").value;
        var busTemp5 =  document.getElementById("busTemp5").value;
        var busTemp6 =  document.getElementById("busTemp6").value;
        var busTemp7 =  document.getElementById("busTemp7").value;
        var busTemp8 =  document.getElementById("busTemp8").value;

        var busTempNames = [busTemp1, busTemp2, busTemp3, busTemp4, busTemp5, busTemp6, busTemp7, busTemp8],
            busTempNameArray = busTempNames.filter(Boolean);
        var busTemp = busTempNameArray.length;

       data.busTempNames = busTempNameArray;
       data.busTemp = busTemp;

        // Soil Sensor
        var AnalogName1 =  document.getElementById("AnalogName1").value;
        var AnalogName2 =  document.getElementById("AnalogName2").value;
        var AnalogName3 =  document.getElementById("AnalogName3").value;
        var AnalogName4 =  document.getElementById("AnalogName4").value;
        var analogNames = [AnalogName1, AnalogName2, AnalogName3, AnalogName4],
            analogNamesArray = analogNames.filter(Boolean);
        var analogCount = analogNamesArray.length;
        data.analogNames = analogNamesArray;
        data.analogCount = analogCount;
        var tempHumid = document.getElementById("tempHumid").value;
        data.tempHumid = tempHumid;
        var myJSON = JSON.stringify(data);
        console.log(myJSON);
        //USED FOR SAVING DATA PASSING DATA IN JSON FORMAT
         var xhr = new XMLHttpRequest();
         var url = "/settings";
         xhr.onreadystatechange = function() {
           if (this.onreadyState == 4 && this.status == 200) {
             console.log(xhr.responseText);
           }
         };
        xhr.open("POST", url, true);
        xhr.send(JSON.stringify(data));
      };


      // Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal
btn.onclick = function() {
  modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}

    </script>
<Style>
   background-position: 97% center;
   background-repeat: no-repeat;
   border: 1px solid #AAA;
   color: #555;
   font-size: inherit;
   margin: 20px;
   overflow: hidden;
   padding: 5px 10px;
   text-overflow: ellipsis;
   white-space: nowrap;
   width: 300px;
}
select#room-color {
   color: #fff;
   background-color: #779126;
   -webkit-border-radius: 20px;
   -moz-border-radius: 20px;
   border-radius: 20px;
   padding-left: 15px;
}
.Form-style{
  font-size: 1.5em;
  text-align: center;
  box-sizing: border-box;
  position: relative;
  margin: 0;
  padding: 0;
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  box-sizing: border-box;
}

.checkmark {
  top: 0;
  left: 0;
  height: 2%;
  width: 2%;
  background-color: #eee;
}

input {
  width: 60%;
  padding: 20px 20px;
  margin: 10px 0;
  box-sizing: border-box;
  font-size: 0.4em;
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
  border-radius: 10px;
  padding: 20px 256px;
  text-align: center;
}

.button:hover {
  background-color: #1cc88a;
  color: white;

  background-color: #1cc88a;
  border: none;
  color: white;
  padding: 26px 268px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  -webkit-transition-duration: 0.4s;
  transition-duration: 0.4s;
  cursor: pointer;
}
.button-holder {
  text-align: center;
}


body {
 background: #00b4ff;
 color: #333;
 font: 100% Lato, Arial, Sans Serif;
 height: 100vh;
 margin: 0;
 padding: 0;
 overflow-x: hidden;

  height: 100%;
  font-family: Trebuchet MS;
}

.container {
background-color: white;
  border-radius: 25px;
border: 1px solid black;
padding: 20px;
  width: 60%;
margin-left: 20%;
margin-right: 20%;
}

#background-wrap {
    bottom: 0;
  left: 0;
  position: fixed;
  right: 0;
  top: 0;
  z-index: -1;
}

/* KEYFRAMES */

@-webkit-keyframes animateBubble {
    0% {
        margin-top: 1000px;
    }
    100% {
        margin-top: -100%;
    }
}

@-moz-keyframes animateBubble {
    0% {
        margin-top: 1000px;
    }
    100% {
        margin-top: -100%;
    }
}

@keyframes animateBubble {
    0% {
        margin-top: 1000px;
    }
    100% {
        margin-top: -100%;
    }
}

@-webkit-keyframes sideWays {
    0% {
        margin-left:0px;
    }
    100% {
        margin-left:50px;
    }
}

@-moz-keyframes sideWays {
    0% {
        margin-left:0px;
    }
    100% {
        margin-left:50px;
    }
}

@keyframes sideWays {
    0% {
        margin-left:0px;
    }
    100% {
        margin-left:50px;
    }
}



.x1 {
    -webkit-animation: animateBubble 25s linear infinite, sideWays 2s ease-in-out infinite alternate;
  -moz-animation: animateBubble 25s linear infinite, sideWays 2s ease-in-out infinite alternate;
  animation: animateBubble 25s linear infinite, sideWays 2s ease-in-out infinite alternate;

  left: -5%;
  top: 5%;

  -webkit-transform: scale(0.6);
  -moz-transform: scale(0.6);
  transform: scale(0.6);
}

.x2 {
    -webkit-animation: animateBubble 20s linear infinite, sideWays 4s ease-in-out infinite alternate;
  -moz-animation: animateBubble 20s linear infinite, sideWays 4s ease-in-out infinite alternate;
  animation: animateBubble 20s linear infinite, sideWays 4s ease-in-out infinite alternate;

  left: 5%;
  top: 80%;

  -webkit-transform: scale(0.4);
  -moz-transform: scale(0.4);
  transform: scale(0.4);
}

.x3 {
    -webkit-animation: animateBubble 28s linear infinite, sideWays 2s ease-in-out infinite alternate;
  -moz-animation: animateBubble 28s linear infinite, sideWays 2s ease-in-out infinite alternate;
  animation: animateBubble 28s linear infinite, sideWays 2s ease-in-out infinite alternate;

  left: 10%;
  top: 40%;

  -webkit-transform: scale(0.7);
  -moz-transform: scale(0.7);
  transform: scale(0.7);
}

.x4 {
    -webkit-animation: animateBubble 22s linear infinite, sideWays 3s ease-in-out infinite alternate;
  -moz-animation: animateBubble 22s linear infinite, sideWays 3s ease-in-out infinite alternate;
  animation: animateBubble 22s linear infinite, sideWays 3s ease-in-out infinite alternate;

  left: 20%;
  top: 0;

  -webkit-transform: scale(0.3);
  -moz-transform: scale(0.3);
  transform: scale(0.3);
}

.x5 {
    -webkit-animation: animateBubble 29s linear infinite, sideWays 4s ease-in-out infinite alternate;
  -moz-animation: animateBubble 29s linear infinite, sideWays 4s ease-in-out infinite alternate;
  animation: animateBubble 29s linear infinite, sideWays 4s ease-in-out infinite alternate;

  left: 30%;
  top: 50%;

  -webkit-transform: scale(0.5);
  -moz-transform: scale(0.5);
  transform: scale(0.5);
}

.x6 {
    -webkit-animation: animateBubble 21s linear infinite, sideWays 2s ease-in-out infinite alternate;
  -moz-animation: animateBubble 21s linear infinite, sideWays 2s ease-in-out infinite alternate;
  animation: animateBubble 21s linear infinite, sideWays 2s ease-in-out infinite alternate;

  left: 50%;
  top: 0;

  -webkit-transform: scale(0.8);
  -moz-transform: scale(0.8);
  transform: scale(0.8);
}

.x7 {
    -webkit-animation: animateBubble 20s linear infinite, sideWays 2s ease-in-out infinite alternate;
  -moz-animation: animateBubble 20s linear infinite, sideWays 2s ease-in-out infinite alternate;
  animation: animateBubble 20s linear infinite, sideWays 2s ease-in-out infinite alternate;

  left: 65%;
  top: 70%;

  -webkit-transform: scale(0.4);
  -moz-transform: scale(0.4);
  transform: scale(0.4);
}

.x8 {
    -webkit-animation: animateBubble 22s linear infinite, sideWays 3s ease-in-out infinite alternate;
  -moz-animation: animateBubble 22s linear infinite, sideWays 3s ease-in-out infinite alternate;
  animation: animateBubble 22s linear infinite, sideWays 3s ease-in-out infinite alternate;

  left: 80%;
  top: 10%;

  -webkit-transform: scale(0.3);
  -moz-transform: scale(0.3);
  transform: scale(0.3);
}

.x9 {
    -webkit-animation: animateBubble 29s linear infinite, sideWays 4s ease-in-out infinite alternate;
  -moz-animation: animateBubble 29s linear infinite, sideWays 4s ease-in-out infinite alternate;
  animation: animateBubble 29s linear infinite, sideWays 4s ease-in-out infinite alternate;

  left: 90%;
  top: 50%;

  -webkit-transform: scale(0.6);
  -moz-transform: scale(0.6);
  transform: scale(0.6);
}

.x10 {
    -webkit-animation: animateBubble 26s linear infinite, sideWays 2s ease-in-out infinite alternate;
  -moz-animation: animateBubble 26s linear infinite, sideWays 2s ease-in-out infinite alternate;
  animation: animateBubble 26s linear infinite, sideWays 2s ease-in-out infinite alternate;

  left: 80%;
  top: 80%;

  -webkit-transform: scale(0.3);
  -moz-transform: scale(0.3);
  transform: scale(0.3);
}



.bubble {
    -webkit-border-radius: 50%;
  -moz-border-radius: 50%;
  border-radius: 50%;

    -webkit-box-shadow: 0 20px 30px rgba(0, 0, 0, 0.2), inset 0px 10px 30px 5px rgba(255, 255, 255, 1);
  -moz-box-shadow: 0 20px 30px rgba(0, 0, 0, 0.2), inset 0px 10px 30px 5px rgba(255, 255, 255, 1);
  box-shadow: 0 20px 30px rgba(0, 0, 0, 0.2), inset 0px 10px 30px 5px rgba(255, 255, 255, 1);

    height: 200px;
  position: absolute;
  width: 200px;
}

.bubble:after {
    background: -moz-radial-gradient(center, ellipse cover,  rgba(255,255,255,0.5) 0%, rgba(255,255,255,0) 70%);
    background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,rgba(255,255,255,0.5)), color-stop(70%,rgba(255,255,255,0)));
    background: -webkit-radial-gradient(center, ellipse cover,  rgba(255,255,255,0.5) 0%,rgba(255,255,255,0) 70%);
    background: -o-radial-gradient(center, ellipse cover,  rgba(255,255,255,0.5) 0%,rgba(255,255,255,0) 70%);
    background: -ms-radial-gradient(center, ellipse cover,  rgba(255,255,255,0.5) 0%,rgba(255,255,255,0) 70%);
    background: radial-gradient(ellipse at center,  rgba(255,255,255,0.5) 0%,rgba(255,255,255,0) 70%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#80ffffff', endColorstr='#00ffffff',GradientType=1 );

  -webkit-border-radius: 50%;
  -moz-border-radius: 50%;
  border-radius: 50%;

    -webkit-box-shadow: inset 0 20px 30px rgba(255, 255, 255, 0.3);
  -moz-box-shadow: inset 0 20px 30px rgba(255, 255, 255, 0.3);
  box-shadow: inset 0 20px 30px rgba(255, 255, 255, 0.3);

  content: "";
    height: 180px;
  left: 10px;
  position: absolute;
  width: 180px;
}
</Style>


)=====";

//BUS TEMP BITS
struct BusTempData {
  char tempName[8][25];
  int busNumber;
  String busPostData;
//  String sensorPostData;
};
BusTempData busTempData;

struct TempHumid {
  char tempHumidSensorName[1][25];
  String tempPostData;
  String humidPostData;
//  String sensorPostData;
};
TempHumid tempHumid;

struct AnalogSensors {
  char sensorNames[4][25];
  String analogPostData;
  String sensorPostData;
  int sensorCount;
};
AnalogSensors analogSensors;
Adafruit_ADS1115 ads;

char groupName[20];
char roomID[20];
const String SECRET = "4584536547";
String vitalPostData = "Secret=";



void handleSettingsUpdate(){
  String data = server.arg("plain");
  DynamicJsonBuffer jBuffer;
  JsonObject& jObject = jBuffer.parseObject(data);

  File configFile = SPIFFS.open("/config.json", "w");
  jObject.printTo(configFile);
  configFile.close();

  server.send(200, "application/json", "{\"status\":\"ok\"}");
  delay(500);
  ESP.restart();
}


void wifiConnect(){
  WiFi.softAPdisconnect(true);
  WiFi.disconnect();
  if(SPIFFS.exists("/config.json")){
    const char * _ssid = "", *_pass = "";
    File configFile = SPIFFS.open("/config.json", "r");
    if(configFile){
      size_t size = configFile.size();
      std::unique_ptr<char[]> buf(new char[size]);
      configFile.readBytes(buf.get(), size);
      configFile.close();
  
      DynamicJsonBuffer jsonBuffer;
      JsonObject& jObject = jsonBuffer.parseObject(buf.get());
      if (jObject.success()){
        _ssid = jObject["ssid"];
        _pass = jObject["password"];
        WiFi.mode(WIFI_STA);
        WiFi.begin(_ssid, _pass);
        unsigned long startTime = millis();
        while(WiFi.status() != WL_CONNECTED){
         delay(500);
         Serial.print(".");
         Serial.println(".");
         digitalWrite(pin_led, !digitalRead(pin_led));
        }
      }
    }
    if (!configFile) ("File creation failed");
  }
  if (WiFi.status() == WL_CONNECTED){
    Serial.print("Connected");
  }
  else{
    WiFi.mode(WIFI_AP);
    WiFi.softAPConfig(local_ip, gateway, netmask);
    WiFi.softAP(mySsid, password);
    Serial.println("AP MODEK");
    delay(2000);
  }
  Serial.println("");
  Serial.println("");
  WiFi.printDiag(Serial); 
}


void wifiPost(String sensorNames, String postData, String Link) {
  String mergedPostData = vitalPostData+sensorNames+postData;
  Serial.println("Merged Post Data is:");
  Serial.println(mergedPostData);

  int contentLength = mergedPostData.length() +25;
  Serial.print("Content Length Calculated To:");
  Serial.println(contentLength);
    
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
    Serial.print("requesting URL: ");
    Serial.println(host);
  httpsClient.print(String("POST ") + Link + " HTTP/1.1\r\n" +
               "Host: " + host + "\r\n" +
               "Content-Type: application/x-www-form-urlencoded"+ "\r\n" +
               "Content-Length:"+String(contentLength) + "\r\n\r\n" +
               String(mergedPostData)+"\r\n"+  
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
}

void resetDevice() {
  ESP.restart();
}
/////<!---END OF NETWORK METHODS---!>//////




void setValues() {
  vitalPostData = vitalPostData+SECRET;
  File configFile = SPIFFS.open("/config.json", "r");
  if(configFile) {
    Serial.println("Config File Found: ");
    size_t size = configFile.size();
    std::unique_ptr<char[]> buf(new char[size]);
    configFile.readBytes(buf.get(), size);
    configFile.close();
    Serial.println("Config File Closed");
    DynamicJsonBuffer jsonBuffer;
    JsonObject& jObject = jsonBuffer.parseObject(buf.get()); 
    
    if (jObject.success()){
      Serial.println("Json Objects successfully found: ");
//        ////<---!GroupName!--->
      if(jObject["groupName"] != NULL || jObject["groupName"] != "") {
        Serial.print("GroupName is:");
        strcpy(groupName, jObject["groupName"]);
        Serial.println(groupName);
        vitalPostData = vitalPostData+"&GroupName=";
        vitalPostData = vitalPostData+String(groupName);
      }
//        ////<---!RoomID!--->
      if(jObject["roomID"] != NULL || jObject["roomID"] != "") {
        Serial.print("Room ID is:");
        strcpy(roomID, jObject["roomID"]);
        Serial.println(roomID);
        vitalPostData = vitalPostData+"&RoomID=";
        vitalPostData = vitalPostData+String(roomID);
      }
      Serial.println("Vital Post Data String Has Been Constructed:");
      Serial.println(vitalPostData);
//        ////<---!TempHumid Setting Values!--->
     strcpy(tempHumid.tempHumidSensorName[0], jObject["tempHumid"]);
     Serial.println(tempHumid.tempHumidSensorName[0]);
     tempHumid.tempPostData = "&TempSensor0=";
     tempHumid.tempPostData = tempHumid.tempPostData+String(tempHumid.tempHumidSensorName[0]);
     tempHumid.humidPostData = "&HumidSensor=";
     tempHumid.humidPostData = tempHumid.humidPostData+String(tempHumid.tempHumidSensorName[0]);
        
      ////<---!BusTemp Setting Values!--->
    if(jObject["busTemp"]) {
      busTempData.busNumber = atoi(jObject["busTemp"]); 
      Serial.print("busTemp Number is: ");
      Serial.println(busTempData.busNumber);
      Serial.println("jObject bus names are...");
      for(int i=0; i < busTempData.busNumber; ++i) {
        strcpy(busTempData.tempName[i], jObject["busTempNames"][i]);
        Serial.print("Sensor Name:");
        Serial.println(busTempData.tempName[i]);
        busTempData.busPostData = busTempData.busPostData+"&TempSensor";
        busTempData.busPostData = String(busTempData.busPostData+i);
        busTempData.busPostData = busTempData.busPostData+"=";
        busTempData.busPostData = busTempData.busPostData+String(busTempData.tempName[i]);
      }
      busTempData.busPostData = busTempData.busPostData+"&BusNumber=";
      busTempData.busPostData = busTempData.busPostData+String(busTempData.busNumber);
       Serial.print("Bus Post Data:");
      Serial.println(busTempData.busPostData);
    }
//    ////<---!Soil Setting Values!--->
    if(jObject["analogCount"]) {
      analogSensors.sensorCount = atoi(jObject["analogCount"]); 
      Serial.print("Analog Count Number is: ");
      Serial.println(analogSensors.sensorCount);
      Serial.println("jObject analog sensor names are...");
      for(int i=0; i < analogSensors.sensorCount; ++i) {
        strcpy(analogSensors.sensorNames[i], jObject["analogNames"][i]);
        Serial.print("Analog Sensor Name:");
        Serial.println(analogSensors.sensorNames[i]);
        analogSensors.analogPostData = analogSensors.analogPostData+"&AnalogSensor";
        analogSensors.analogPostData = String(analogSensors.analogPostData+i);
        analogSensors.analogPostData = analogSensors.analogPostData+"=";
        analogSensors.analogPostData = analogSensors.analogPostData+String(analogSensors.sensorNames[i]);
      }
      Serial.println("Analog post string:");
      Serial.println(analogSensors.analogPostData);
    }
   }
 }
 if (!configFile) ("File creation failed");
}



////  <--!BusTempreture Sensors!-->
uint8_t findBusDevice(int pin){
  testWire = (pin);
  uint8_t address[8];
  uint8_t count = 0;
  if (testWire.search(address)){
    Serial.print("\nuint8_t pin");
    Serial.print(pin, DEC);
    Serial.println("[][8] = {");
    do {
      count++;
      Serial.println("  {");
      for (uint8_t i = 0; i < 8; i++){
        Serial.print("0x");
        if (address[i] < 0x10) Serial.print("0");
        Serial.print(address[i], HEX);
        if (i < 7) Serial.print(", ");
      }
      Serial.println("  },");
    } while (testWire.search(address));
    Serial.println("};");
    Serial.print("// nr devices found: ");
    Serial.println(count);
    oneWire = (pin);   
  }
  return count;
}

void busTemp() {
 sensors.begin();
 Serial.print("Requesting Bus temperatures...");
 sensors.requestTemperatures(); // Send the command to get temperatures
 Serial.println("DONE");
 String busSensorData;
 for(int i=0; i < busTempData.busNumber; i++) {
  float temp = sensors.getTempCByIndex(i);
  Serial.print(i);
  Serial.print(" Tempreture is:");
  Serial.println(temp); 
  busSensorData = busSensorData+"&TempData";
  busSensorData = String(busSensorData+i);
  busSensorData = busSensorData+"=";
  busSensorData = busSensorData+String(temp);
 }
  Serial.println(busSensorData); 
 wifiPost(busTempData.busPostData, busSensorData, "/HomeApp/Recieve/Temperature.php");
}

////  <--!Analog Sensors!-->
void analogReading() {
  String analogReading;
  int16_t adc0, adc1, adc2, adc3;
  for(int i=0; i < analogSensors.sensorCount; i++) {
    analogReading = String(ads.readADC_SingleEnded(i));
  }
//  int16_t adc0, adc1, adc2, adc3;
//  String postData;
//  for(int i=0; i < 3; i++) {
//    analogReading = String(ads.readADC_SingleEnded(1));
//    Serial.print(i);
//    Serial.print("Reading is:");
//    Serial.println(analogOne);
//    postData = postData+"&AnalogReading";
//    postData = postData+String(i);
//    postData = postData+"=";
//    postData = postData+analogReading;
//  }
//  postData = postData+"&AnalogBusNumber=";
//  postData = postData+String(analogSensors.sensorCount);
//  Serial.println("Analog Post Data is:");
//  Serial.println(postData);
//  wifiPost(analogSensors.analogPostData, postData, "/HomeApp/Recieve/Analog.php");
//  analogOne = String(ads.readADC_SingleEnded(0));
//  analogTwo = String(ads.readADC_SingleEnded(1));
//  analogThree = String(ads.readADC_SingleEnded(2));
//  analogFour = String(ads.readADC_SingleEnded(3));
//  Serial.print("1st Reading is:");
//  Serial.println(analogOne);
//  Serial.print("2nd Reading is:");
//  Serial.println(analogTwo);
//  Serial.print("PotThree is:");
//  Serial.println(analogThree);
//  Serial.print("PotFour is:");
//  Serial.println(analogFour);
}

////  <--!TempHumid Sensors!-->
void tempHumidReading() {
  String rTemp = String(dht.readTemperature());
  String rHumid = String(dht.readHumidity());
  Serial.print("Humidity: ");
  Serial.print(rHumid);
  Serial.println("%");
  Serial.print("Temp is:");
  Serial.print(rTemp);
  Serial.println(" Celsius");
  String tempPostData = "&BusNumber=1&TempData0=";
  String humidPostData = "&HumidData=";
  tempPostData = tempPostData+rTemp;
  humidPostData = humidPostData+rTemp;
  Serial.println("Temp Post Data:");
  Serial.println(tempPostData);
  wifiPost(tempHumid.tempPostData, tempPostData, "/HomeApp/Recieve/Temperature.php");
  delay(500);
  Serial.println("Humid Post Data:");
  Serial.println(humidPostData);
  wifiPost(tempHumid.humidPostData, humidPostData, "HomeApp/Recieve/Humidity.php");
}





void setup() {
  Serial.begin(9600);
  //Serial.begin(115200);
  
  //WebBits
  Serial.println("Setup... index, settings & reset server inizilise...");
  server.on("/",[](){server.send_P(200,"text/html",webpage);});
  server.on("/settings", HTTP_POST, handleSettingsUpdate);
  server.on("/reset",[](){server.send_P(200,"text/html",resetPage);});
  server.on("/resetDevice", HTTP_POST, resetDevice);
  Serial.println("..... Servers Begun");
  server.begin();
  
  SPIFFS.begin();
  if(SPIFFS.exists("/config.json")){
  setValues();
  delay(3000);
  if(busTempData.busNumber >= 1) {
    Serial.println("//\n// Start oneWireSearch \n//");
    for (uint8_t pin = 12; pin < 15; pin++){
      findBusDevice(pin);
    }
    DallasTemperature sensors(&oneWire);
    Serial.println("\n//\n// End oneWireSearch \n//");
  }
  ads.begin();
  dht.begin();
  }
  
  wifiConnect();
}

void loop() {
  Serial.println("Loop Begin");
  Serial.println("Handling Server Client...");
  server.handleClient();
  Serial.println("Server ClientHandled...");

  if(WiFi.status()== WL_CONNECTED){
    Serial.println("Connected");
    if(busTempData.busNumber >= 1) {
      busTemp();
    }
    if(analogSensors.sensorCount >= 1) {
    //  analogReading();
    }
    if(tempHumid.tempHumidSensorName[0] != "" || tempHumid.tempHumidSensorName[0] != NULL) {
      tempHumidReading();
    }
  }
  delay(1000);
}
