#include <Wire.h>
#include <Adafruit_Sensor.h>

#include <SPIFFSReadServer.h>
#include <SPIFFSIniFile.h>

#include <ESP8266WiFi.h>
#include <ESP8266WiFiAP.h>
#include <ESP8266WiFiGeneric.h>
#include <ESP8266WiFiMulti.h>
#include <ESP8266WiFiScan.h>
#include <ESP8266WiFiSTA.h>F
#include <ESP8266WiFiType.h>
#include <WiFiClient.h>
#include <WiFiServer.h>
#include <WiFiServerSecure.h>
#include <ESP8266HTTPClient.h>

#include <ESP8266WebServer.h>
#include <EEPROM.h>
#include <FS.h>

#include <ArduinoJson.h>

#include <OneWire.h>
#include <DallasTemperature.h>

//#include <Adafruit_ADS1015.h>

#include <DHT.h>;


#define NODE_MCU_SERIAL 115200
#define MICRO_ESP_SERIAL 9600


//Web bits
// Test
#define HOMEAPP_HOST "https://192.168.1.172"
// Prod
//#define HOMEAPP_HOST "https://klh17101990.asuscomm.com"
#define HOMEAPP_URL "HomeApp"
#define HOMEAPP_PORT "8101"
#define HOME_APP_CURRENT_READING "api/device/esp/update/current-reading"

#define HOMEAPP_LOGIN "api/device/login_check"
#define HOMEAPP_REFRESH_TOKEN "api/token/refresh"
#define HOMEAPP_IP_UPDATE "api/device/ipupdate"

const char fingerprint[] PROGMEM = "60ee151bee994d6ca826a69abce1e724173721ca";

String ipAddress;
String token;
String refreshToken;


// Access ponint network bits
#define ACCESSPOINT_SSID "HomeApp-D-A-D-AP"
#define ACCESSPOINT_PASSWORD "HomeApp1234"

ESP8266WiFiMulti WiFiMulti;
ESP8266WebServer server;
IPAddress local_ip(192,168,1,254);
IPAddress gateway(192,168,1,1);
IPAddress netmask(255,255,255,0);


//Sensor sepcific settings
#define AMOUNT_OF_USABLE_PINS 15
#define ACTIVE_START_PIN 2
#define LAST_ACTIVE_PIN 4

// DHT
#define DHTPIN 2
#define DHTTYPE DHT22 
DHT dht(DHTPIN, DHTTYPE);

struct DhtSensor {
  char sensorName[25];
  float tempReading;
  float humidReading;
  bool activeSensor = false;
};
DhtSensor dhtSensor;


// Dallas
OneWire oneWire(0);
DallasTemperature sensors(&oneWire);

//Dallas Bus Temperature
struct DallasTempData {
  char sensorName[8][25];
  float tempReading[8];
  int sensorCount;
  bool sensorActive = false;
};
DallasTempData dallasTempData;



// Webpages
char webpage[] PROGMEM = R"=====(
<html>
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
        <label for="ssid">SSID/Network Name</label>
        <br>
        <input type="text" value="" id="ssid" placeholder= "Enter Network SSID"/>
      </div>
        <div class="Form-style">
          <label for="password">Password</label>
          <br>
          <input value="" type="password" id="password" placeholder="Enter Network Password"/>
        </div>
        <div class="Form-style">
          <h3>Enter Device Information</h3>
          <label for="groupName">Device Name</label>
          <br>
          <input value="" type="text" id="deviceName" placeholder="Enter Your Account GroupName"/>
        </div>

        <div class="Form-style">
          <label for="sensorName">Device Secret</label>
          <br>
            <input value="" type="text" id="deviceSecret" placeholder="Enter The Secret Given To You By The App"/>
        </div>
        <br>
        <div class="Form-style">
          <h2>Enter Sensor Information</h2>
          <label class="heading">Temperature and Humidity Sensor</label>
          <br>
          <input type="radio" class="checkmark" name="tempHumidRadio" value="Yes" onchange="hiddenDisplay('tempDisplay')">Yes<br></input>
          <input type="radio" class="checkmark" name="tempHumidRadio" value="No" onchange="hiddenDisplay('tempDisplay')" checked>No<br></input>
        </div>
        <div class="Form-style" id="tempDisplay" style="display: none;">
          <input value="" type="text" id="dhtSensor" placeholder="Enter The Name of the Sensor"/>
          <br>
        </div>


        <div class="Form-style">
          <label class="heading">Temperature Bus Sensor</label>
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
          <label for="Analog">Analog Sensor</label>
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
          <button class="button" onclick="saveDataToSpiff()"> Save </button>
       </div>
    </form>
    <div class="button-holder">
      <button class="button-reset" href="/reset-device"> Reset Device </button>
    </div>
    <h4 class="h4">Created by Kurtis Hill</h4>
  </body>
  <script name="config" src="config"></script>
  <script>
    function hiddenDisplay(name) {
        var displayItem = document.getElementById(name);
        if (displayItem.style.display === "none") {
            displayItem.style.display = "block";
        } else {
          displayItem.style.display = "none";
        }
    }

    function saveDataToSpiff() {
      console.log("save button was clicked");
        //Vital Info
      var ssid = document.getElementById("ssid").value;
      var password = document.getElementById("password").value;
      var deviceName = document.getElementById("deviceName").value;
      var deviceSecret = document.getElementById("deviceSecret").value;

      var wifi = {
        'ssid':ssid,
        'password':password
      };

      var deviceCredentials = {
        'username':deviceName, 
        'password':deviceSecret
      };

      //Dallas Bus Sensor Names
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

      var busTempCount = busTempNameArray.length;

      var data = {'dallas': {busTempCount: busTempCount, busTempNameArray: busTempNameArray}};

      // data.busTempNames = busTempNameArray;
      // data.busTemp = busTempCount;

      // ADC Sensor Names
      var analogName1 =  document.getElementById("AnalogName1").value;
      var analogName2 =  document.getElementById("AnalogName2").value;
      var analogName3 =  document.getElementById("AnalogName3").value;
      var analogName4 =  document.getElementById("AnalogName4").value;
      var analogNames = [analogName1, analogName2, analogName3, analogName4],
      
      analogNamesArray = analogNames.filter(Boolean);
      data.analogNames = analogNamesArray;
      
      var analogCount = analogNamesArray.length;
      data.analogCount = analogCount;

      // DHT Sensor
      var dhtSensor = document.getElementById("dhtSensor").value;
      data.dhtSensor = {'sensorName' : dhtSensor};

      var jsonData = {'wifi': wifi, 'sensorData':data, 'deviceCredentials': deviceCredentials};

      // jsonData = JSON.stringify(jsonData)

      // console.log(jsonData);

      var xhr = new XMLHttpRequest();
      var url = "/settings";

      xhr.onreadystatechange = function() {
        if (this.onreadyState == 4 && this.status == 200) {
          alert('Saved device settings')
            console.log(xhr.responseText);
        }
      }

      console.log(JSON.stringify(jsonData));

      xhr.open("POST", url, true);
      xhr.send(JSON.stringify(jsonData));
    }

  </script>
  <style>
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
  .button-reset {
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
  .button-reset {
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

  .button-reset:hover {
    background-color: #1cc88a;
    color: white;

    background-color: #e93313;
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
  </style>
</html>


)=====";


// Need to decode this json string (data) and place different parts in different spiffs, wifi and sensor data
void handleSettingsUpdate(){
  String data = server.arg("plain");
  char jsonData[sizeof(data)];

  strcpy(jsonData, data.c_str());
  
  Serial.println("full payload"); // @DEV
  Serial.println(data);
  Serial.println("json char array");
  Serial.println(jsonData);

  DynamicJsonDocument doc(1024);
  deserializeJson(doc, jsonData);

  bool success = true;
  if (!saveWifiCredentals(doc["wifi"])) {   
    delay(500);
    Serial.println("failed to save spiffs");
    success = false;
  }
  if (!saveSensorDataToSpiff(doc["sensorData"])) {
    delay(500);
    Serial.println("failed to save sensor data spiffs");
    success = false;
  }
  if (!saveDeviceUserSettings(doc["deviceCredentials"])) {
    delay(500);
    Serial.println("failed to save device data spiffs");
    success = false;
  }

  
  Serial.println("Finished saving credentials");
  success == true 
    ? server.send(200, "application/json", "{\"status\":\"ok\"}") 
    : server.send(500, "application/json", "{\"status\":\"device data spiff error\"}");   
  
  delay(500);
  Serial.println("Restarting device");
  ESP.restart();
}


bool saveWifiCredentals(DynamicJsonDocument doc) {
  const char* ssid = doc["ssid"];
  const char* password = doc["password"];

  if (
    ssid == NULL 
    || ssid == ""
    || ssid[0] == '\0' 
    || password == NULL 
    || password == "" 
    || password[0] == '\0'
    ) {
    Serial.println("Security is not trying to be set, no value");
    return true;
  }
  
  Serial.println("Security values are being set");
  DynamicJsonDocument wifiDoc(100);
  
  wifiDoc["ssid"] = ssid;
  wifiDoc["password"] = password;

  File configFile = SPIFFS.open("/wifi.json", "w");

  if(serializeJson(wifiDoc, configFile)) {
    Serial.println("Wifi serialization save success");
  } else {
    Serial.println("Serialization failure");
    return false;
  }
  
  configFile.close();
  Serial.println("Wifi spiff file saved & closed");
  return true;
}

// Start of device user settings
bool saveDeviceUserSettings(DynamicJsonDocument doc) {
  Serial.println("Setting device user setting now");
  String deviceName = doc["username"].as<String>();
  String secret = doc["password"].as<String>();

//  Serial.println("deviceName"); // @DEV
//  Serial.println(deviceName);
//  Serial.println("secret");
//  Serial.println(secret);

  if (
    deviceName == NULL 
    || deviceName == "" 
    || deviceName == "\0" 
    || deviceName == "null"
    || secret == NULL
    || secret == "" 
    || secret == "\0" 
    || secret == "null"
    ) {
    Serial.println("No device user name sent in payload, not setting any values and using defaults");
    delay(500);
    return true;
  }
  
  File deviceSettingsSPIFF = SPIFFS.open("/device.json", "w");

  if(serializeJson(doc, deviceSettingsSPIFF)) {
    Serial.println("Device settings serialization save success");
  } else {
    Serial.println("Device settings Serialization failure");
    deviceSettingsSPIFF.close();
    return false;
  }
  
  deviceSettingsSPIFF.close();
  Serial.println("Device settings SPIFF close, sucess");

  return true; 
}


// Sensor Functions
bool saveSensorDataToSpiff(DynamicJsonDocument doc) {
  if (!saveDallasSensorData(doc["dallas"])) {
    Serial.println("failed to set Dallas Spiff");
  }
  if (!saveDhtSensorData(doc["dhtSensor"])) {
    Serial.println("failed to set Dallas Spiff");
  }
  return true;
}

// Dht functions
bool saveDhtSensorData(DynamicJsonDocument dhtData) {
    if (
    dhtData["sensorName"] == NULL 
    || dhtData["sensorName"] == "" 
    || dhtData["sensorName"] == "\0" 
    || dhtData["sensorName"] == "null"
    ) {
      Serial.println("dht sensor not sent, wont save any data");
      return true;
    }
         
    String nameCheck = dhtData["sensorName"].as<String>();
    Serial.println("Dht sensor name");
    Serial.println(nameCheck);
    
    Serial.println("dht sensor data being saved");
    File dhtSPIFF = SPIFFS.open("/dht.json", "w");
  
    if(serializeJson(dhtData, dhtSPIFF)) {
      Serial.println("Dht serialization save success");
    } else {
      Serial.println("Dht Serialization failure");
      return false;
    }
    
    dhtSPIFF.close();

    Serial.println("Dht SPIFF close, sucess");

    return true;  
  
    Serial.println("No Dht sensor data sent");
  
    return true;
}


bool setDhtValues() {
  Serial.println("Checking to see if dht values are set");
  File dhtSensorSpiff = SPIFFS.open("/dht.json", "r");
  if (dhtSensorSpiff) {
    Serial.println("dht json found");
    StaticJsonDocument<150> dhtDoc;
    DeserializationError error = deserializeJson(dhtDoc, dhtSensorSpiff);

    if (error) {
      Serial.println("deserialization error");
      dhtSensorSpiff.close();
      return false;
    }

    String dhtSensorName = dhtDoc["sensorName"];

    if(dhtSensorName == NULL || dhtSensorName == "" || dhtSensorName == "\0" || dhtSensorName == "null") {
      Serial.println("Name check failed skipping dht this sensor");
      dhtSensorSpiff.close();
      return true;
    }

    dhtSensor.activeSensor = true;
    strncpy(dhtSensor.sensorName, dhtDoc["sensorName"], sizeof(dhtSensor.sensorName));
//    dhtSensor.sensorName = dhtDoc["sensorName"];
    Serial.print("dht sensor name ");
    Serial.println(dhtSensor.sensorName);
    
  }
  dhtSensorSpiff.close();
 
  return true;
}

// Dallas functions
bool saveDallasSensorData(DynamicJsonDocument dallasData) {
  int dallasCount = dallasData["busTempCount"].as<int>();
  Serial.println("dallas sensor count");
  Serial.println(dallasCount);
  if (dallasCount >= 1) {
    Serial.println("dallas sensor data being set");
    File dallasSPIFF = SPIFFS.open("/dallas.json", "w");
  
    if(serializeJson(dallasData, dallasSPIFF)) {
      Serial.println("Dallas serialization save success");
    } else {
      dallasSPIFF.close();
      Serial.println("Dallas Serialization failure");
      return false;
    }
    
    dallasSPIFF.close();

    Serial.println("Dallas SPIFF close, sucess");

    Serial.println(dallasTempData.sensorCount);// @DEV
    return true;  
  }
  Serial.println("No dallas sensor data sent");
  
  return true;
}

bool setDallasValues() {
  Serial.println("Checking to see if dallas values are set");
  File dallasSensor = SPIFFS.open("/dallas.json", "r");
  if (dallasSensor) {
    Serial.println("Dallas json found");
    StaticJsonDocument<150> dallasDoc;
    DeserializationError error = deserializeJson(dallasDoc, dallasSensor);

    if (error) {
      Serial.println("deserialization error");
      dallasSensor.close();
      return false;
    }
    
    dallasTempData.sensorCount = dallasDoc["busTempCount"].as<int>();

    if (dallasTempData.sensorCount == 0) {
      Serial.println("No Sensor count not setting any values");
      dallasSensor.close();
      return true;
    }
   
    Serial.print("dallas sensor count ");
    Serial.println(dallasTempData.sensorCount);

    for (int i=0; i < dallasTempData.sensorCount; ++i) {
      String nameCheck = dallasDoc["busTempNameArray"][i].as<String>();

      if(nameCheck == NULL || nameCheck == "" || nameCheck == "\0" || nameCheck == "null") {
        Serial.println("Name check failed skipping this sensor");
        continue;
      }
      
      strncpy(dallasTempData.sensorName[i], dallasDoc["busTempNameArray"][i], sizeof(dallasTempData.sensorName[i]));
      Serial.print("dallas sensor name ");
      Serial.println(dallasTempData.sensorName[i]);
    }
  }
  dallasSensor.close();
 
  return true;
}
// End of Dallas functions


bool setupNetworkConnection(){
  Serial.println("Wifi connecting");
  WiFi.softAPdisconnect(true);
  WiFi.disconnect();

  if(SPIFFS.exists("/wifi.json")){
    Serial.println("wifi spiff extits");
    bool networkConnected = connectToNetwork();

    if (!networkConnected) {
      createAccessPoint();
      
      return false;
    }
    
    return true;
  } else {
    Serial.print("wifi.json not found in SPIFF AP mode activating...");
    return false;
  }
}

void createAccessPoint() {
  Serial.println("Setting up wireless access point");
  WiFi.mode(WIFI_AP);
  WiFi.softAPConfig(local_ip, gateway, netmask);
  WiFi.softAP(ACCESSPOINT_SSID, ACCESSPOINT_PASSWORD);
  Serial.println("AP MODE Activated");
  delay(2000);
  WiFi.printDiag(Serial); 
}
  

bool connectToNetwork() {
  File wifiCredentials = SPIFFS.open("/wifi.json", "r");
  if (wifiCredentials) {
    Serial.println("wifi file successfull");
    StaticJsonDocument<100> wifiDoc;
    DeserializationError error = deserializeJson(wifiDoc, wifiCredentials);

    wifiCredentials.close();
    
    String ssid = wifiDoc["ssid"].as<String>();
    String pass = wifiDoc["password"].as<String>();   
       
    if (
    ssid == NULL 
    || ssid == "" 
    || ssid[0] == '\0' 
    ) {
    Serial.println("No network SSID set, not attempting to connect");
    return false;
  }
    WiFi.mode(WIFI_STA);
    WiFi.begin(ssid, pass);

    int retryCounter = 0;
    
    while(WiFi.status() != WL_CONNECTED){
      ++retryCounter;
      delay(500);
      Serial.print(retryCounter);
      Serial.println("..");
      Serial.println(ssid); //@DEV
      Serial.println(pass);
      if (WiFi.status() == WL_CONNECTED){
        Serial.println("Wifi connection made");
        WiFi.printDiag(Serial); 
        Serial.print("Network IP Address: ");
        Serial.println(WiFi.localIP());
        Serial.println("saved ip address");
        ipAddress = ipToString(WiFi.localIP());
        Serial.println(ipAddress);
        return true;
      }
      if (retryCounter == 25) {
        Serial.println("Wifi timed out connection was not made");
        return false;
      }      
    }
  } else {
    Serial.println("Wifi failed");
  }
}



bool deviceLogin() {
  Serial.println("Logging device in");
  String endpoint = HOMEAPP_LOGIN;
  String url = buildHomeAppUrl(endpoint);
  String jsonData = getSerializedSpiff("/device.json");
  String payload = sendHomeAppHttpsRequest(url, jsonData, false);

  if (
    payload == ""
    || payload == NULL 
    || payload == "" 
    || payload[0] == '\0' 
    ) {
    Serial.println("payload empty returning false");
    Serial.println("Device has failed to login, cannot send any data"); 
    return false;
  }

  return saveTokensFromLogin(payload);    
}


String buildHomeAppUrl(String endpoint) {
  Serial.println("building url");
  String url = HOMEAPP_HOST;
  url += ":";
  url += HOMEAPP_PORT;
  url += "/";
  url += HOMEAPP_URL;
  url += "/";
  url += endpoint;

  Serial.print("url built: ");
  Serial.println(url);
  return url;
}


String sendHomeAppHttpsRequest(
  String url,
  String jsonData,
  bool addAuthHeader
  ) {
  Serial.println("json data to send");
  Serial.println(jsonData);
  Serial.println("Building Https connection");
  std::unique_ptr<BearSSL::WiFiClientSecure>client(new BearSSL::WiFiClientSecure);
  client->setFingerprint(fingerprint);
  HTTPClient https;

  Serial.print("[HTTPS] begin connecting to... ");
  Serial.println(url);
  
  https.begin(*client, url);
  https.addHeader("Content-Type", "application/json");

  if (addAuthHeader == true) {
    Serial.println("adding auth header with token");
    Serial.println("Authorization Bearer "+token);
    https.addHeader("Authorization", "Bearer "+token);
  }

  Serial.println("[HTTP] POST...");
  int httpCode = https.POST(jsonData);

  // httpCode will be negative on error
  if (httpCode > 0) {
    Serial.printf("[HTTP] POST... code: %d\n", httpCode);
    if (httpCode == HTTP_CODE_OK) {
      const String& payload = https.getString();
      Serial.print("received payload: ");
      Serial.println(payload);      
      return payload;
    }
    if (httpCode == 401) {
      Serial.println("server sent back a 401 attempting to refresh token");
      String urlLoopCheck = buildHomeAppUrl(HOMEAPP_REFRESH_TOKEN);
      Serial.println("url check");
      Serial.println(urlLoopCheck);
      if (url == urlLoopCheck) {
        deviceLogin();
      }
      handleRefreshTokens();
    }
  } else {
      Serial.printf("[HTTP] POST... failed, error: %s\n", https.errorToString(httpCode).c_str());
 
  }
  https.end();
  return "";
}

 void handleRefreshTokens() {
  String url = buildHomeAppUrl(HOMEAPP_REFRESH_TOKEN);
  Serial.print("refresh token url: "); //@DEV
  Serial.println(url);

  DynamicJsonDocument refreshTokenDoc(1024);

  refreshTokenDoc["refreshToken"] = refreshToken;

  String jsonData;

  serializeJson(refreshTokenDoc, jsonData);

  Serial.print("serialized refresh token data to send");//@dev
  Serial.print(jsonData);

  String tokens = sendHomeAppHttpsRequest(url, jsonData, false);

  bool saveSuccess = saveTokensFromLogin(tokens);

  if(!saveSuccess) {
    Serial.println("tokens failed to save");
    delay(2000);
    deviceLogin();
  }
}



bool saveTokensFromLogin(String payload) {  
  Serial.println("saving payload into tokens: ");
  Serial.println(payload);
  char jsonData[1000];
  strcpy(jsonData, payload.c_str());

  DynamicJsonDocument responseTokens(1024);
  DeserializationError error = deserializeJson(responseTokens, jsonData);

  if (error) {
    Serial.println("deserialization error");
    return false;
  }
  
  Serial.println("token json"); // @DEV
  Serial.println(responseTokens["token"].as<String>());
  
  token = responseTokens["token"].as<String>();
  refreshToken = responseTokens["refreshToken"].as<String>();
  
  Serial.println("Token: "); //@DEBUG
  Serial.println(token);
  Serial.println("refreshToken");
  Serial.println(refreshToken);

  return true;
}


String buildDallasReadingSensorUpdateRequest() {
  Serial.println("Building Dallas request");
  DynamicJsonDocument sensorUpdateRequest(1024);

  sensorUpdateRequest["sensorType"] = "Dallas";

  for (int i = 0; i < dallasTempData.sensorCount; ++i) {
    if (dallasTempData.tempReading[i] != -127) {
      Serial.print("sensor name:");
      Serial.println(dallasTempData.sensorName[i]);
      sensorUpdateRequest["sensorData"][i]["sensorName"] = dallasTempData.sensorName[i];
      sensorUpdateRequest["sensorData"][i]["currentReadings"]["temperatureReading"] = String(dallasTempData.tempReading[i]);
      Serial.print("temp reading:");
      Serial.println(String(dallasTempData.tempReading[i])); 
    }
  }
  String jsonData;
  serializeJson(sensorUpdateRequest, jsonData);
  Serial.print("Dallas json data to send: ");
  Serial.println(jsonData);
  
  return jsonData;
}

String buildIpAddressUpdateRequest() {
  Serial.println("Building IP update request");
  DynamicJsonDocument ipUpdateRequest(64);

  ipUpdateRequest["IPAddress"] = ipAddress;

  String jsonData;
  serializeJson(ipUpdateRequest, jsonData);

  Serial.println("serialized json data");
  Serial.println(jsonData);

  return jsonData;
}

bool updateDeviceIPAddress() {
  Serial.println("device ip address updating");
  String url = buildHomeAppUrl(HOMEAPP_IP_UPDATE);
  String payload = buildIpAddressUpdateRequest();
  String response = sendHomeAppHttpsRequest(url, payload, true);

  if (response == "") {
    Serial.println("response empty, failed to update IP Address");
    return false;
  }

  Serial.println("device update success");
  return true;
}

bool sendDallasUpdateRequest() {
  Serial.println("Begining to send Dallas request");
  String url = buildHomeAppUrl(HOME_APP_CURRENT_READING);
  String payload = buildDallasReadingSensorUpdateRequest();
  String response = sendHomeAppHttpsRequest(url, payload, true);
  Serial.println("response");
  Serial.println(response);
  return true;
}

bool sendDhtUpdateRequest() {
  Serial.println("Begining to send Dht request");
  String url = buildHomeAppUrl(HOME_APP_CURRENT_READING);
  String payload = buildDhtReadingSensorUpdateRequest();
  if (payload == "") {
    return false;
  }
  String response = sendHomeAppHttpsRequest(url, payload, true);
  Serial.println("response");
  Serial.println(response);
  return true;
}

String buildDhtReadingSensorUpdateRequest() {
  Serial.println("Building dht request");
  DynamicJsonDocument sensorUpdateRequest(1024);

  sensorUpdateRequest["sensorType"] = "Dht";

  if (!isnan(dhtSensor.tempReading) || !isnan(dhtSensor.humidReading)) {
    Serial.print("sensor name:");
    Serial.println(dhtSensor.sensorName);
    sensorUpdateRequest["sensorData"][0]["sensorName"] = dhtSensor.sensorName;
    sensorUpdateRequest["sensorData"][0]["currentReadings"]["temperatureReading"] = String(dhtSensor.tempReading);
    sensorUpdateRequest["sensorData"][0]["currentReadings"]["humidityReading"] = String(dhtSensor.humidReading);
    
    String jsonData;
    serializeJson(sensorUpdateRequest, jsonData);
    Serial.print("Dht json data to send: ");
    Serial.println(jsonData);
  
    return jsonData;
  }

  return "";
}


void resetDevice() {
  createAccessPoint(); //@DEV
  SPIFFS.remove("/device.json");
  SPIFFS.remove("/wifi.json");
  SPIFFS.remove("/dallas.json");
  server.send(200, "application/json", "{\"status\":\"device reset\"}"); 
}

void restartDevice() {
  ESP.restart;
}
/////<!---END OF NETWORK METHODS---!>//////


//<---------- SPIFF MEthods ------------------>
DynamicJsonDocument getDerserializedJson(String serializedJson, int buffSize) {
  Serial.println("Deseriazing Json");
  char jsonData[buffSize];

  strcpy(jsonData, serializedJson.c_str());

  DynamicJsonDocument deserializedJson(1024);
  DeserializationError error = deserializeJson(deserializedJson, jsonData);

  if (error) {
    Serial.println("deserialization error");
  }

  return deserializedJson;
}

String getSerializedSpiff(String spiff) {
  Serial.print("accessing spiff: ");
  Serial.println(spiff);
  File deviceFile = SPIFFS.open(spiff, "r");
  String deviceJson;
  
  while(deviceFile.available()){
    deviceJson += char(deviceFile.read());
  }
  deviceFile.close();
  Serial.println("Spiff closed successfully");
  Serial.println("whole json string from spiff:");
  Serial.println(deviceJson); //@DEBUG

  return deviceJson;
}
//<---------- END OF SPIFF MEthods ------------------>

//<------- Dallas Sensor Functions -------------->
bool findDallasSensor() {
  bool sensorSuccess = false;
  for (uint8_t pin = ACTIVE_START_PIN; pin < AMOUNT_OF_USABLE_PINS && pin < LAST_ACTIVE_PIN ; pin++) {
    Serial.println("pin");
    Serial.println(pin);
    sensorSuccess = searchPinForOneWire(pin);
    if(sensorSuccess == true) {
      Serial.println("Dallas sensor found marking it as active");
      DallasTemperature sensors(&oneWire);
      dallasTempData.sensorActive = true;
      return true;
    }
  }      
  Serial.println("Dallas sensor not found");
  return false;
}

uint8_t searchPinForOneWire(int pin){
  Serial.println("finding dallas sensor...");
  OneWire ow(pin);

  uint8_t address[8];
  uint8_t count = 0;

  if (ow.search(address)) {
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
    } while (ow.search(address));
    Serial.println("};");
    Serial.print("// nr devices found: ");
    Serial.println(count);
    oneWire = (pin);   
    return true;
  }

  return false;
}


void takeDallasTempReadings() {
  Serial.print("Requesting Bus temperatures...");
  sensors.requestTemperatures(); // Send the command to get temperatures
  Serial.println("DONE");

  for(int i = 0; i < dallasTempData.sensorCount; i++) {
    float tempReading = sensors.getTempCByIndex(i);
    Serial.print("Temp number:");
    Serial.println(i);
    Serial.print(" Tempreture is:");
    Serial.println(tempReading); 
    dallasTempData.tempReading[i] = tempReading;
 }
}

//@DEV
//void takeDallasTempReadings() {
//  for (int i = 0; i < dallasTempData.sensorCount; ++i) {
//    Serial.print("Getting Temp Reading...");
//    int rando = random(5, 45);
//    Serial.println("Temp Reading:");
//    Serial.println(rando);
//    dallasTempData.tempReading[i] = rando;
//    Serial.print("dallas sensor temp reading");
//    Serial.println(dallasTempData.tempReading[i]);
//  }
//}
/////<!---END OF Dallas Sensor Methods ---!>//////



//<!------------- DHT Functions --------------!>
void takeDhtReadings() {
  dhtSensor.tempReading = dht.readTemperature();
  dhtSensor.humidReading = dht.readHumidity();
  Serial.print("Temp is:");
  Serial.print(dhtSensor.tempReading);
  Serial.println(" Celsius");
  Serial.print("Humidity: ");
  Serial.print(dhtSensor.humidReading);
  Serial.println("%");
}

String ipToString(IPAddress ip){
  String stringIP = "";
  for (int i=0; i<4; i++)
    stringIP += i  ? "." + String(ip[i]) : String(ip[i]);
  return stringIP;
}



void setup() {
//  Serial.begin(9600);
  Serial.begin(115200); 
  Serial.println("Searial started");
  Serial.print("Starting web servers...");
  //Net
  server.on("/",[](){server.send_P(200,"text/html",webpage);});
  server.on("/settings", HTTP_POST, handleSettingsUpdate);
  
  server.on("/reset-device", HTTP_GET, resetDevice);
  server.on("/restart-device", HTTP_GET, restartDevice);
  server.begin();
  Serial.println("Servers Begun");

  delay(5000);
  SPIFFS.begin();
  Serial.println("...SPIFFS started");
  
  Serial.println("Begining device setup");

  if (setupNetworkConnection()) {
    bool loggedIn = deviceLogin();

    if (loggedIn == true) {
      if (setDallasValues()) {
        if (findDallasSensor()) {
          delay(500);
          Serial.println("Begining Dallas sensor");
          sensors.begin();
        }
      }
      if (setDhtValues()) {
        dht.begin();
      }
    }
  }
  delay(3000);
  Serial.println("End of Setup");
}


void loop() {
  Serial.println("Loop Begin");
  Serial.println("Handling Server Client...");
  server.handleClient();
  Serial.println("Server ClientHandled...");

  if(WiFi.status()== WL_CONNECTED){
    Serial.println("Connected to wifi");
    if (dallasTempData.sensorActive == true) {
      takeDallasTempReadings();
      sendDallasUpdateRequest();    
    }
    if (dhtSensor.activeSensor == true) {
      takeDhtReadings();
      sendDhtUpdateRequest();
    }
  }
  Serial.println("Loop finished");
  delay(1000);
}
