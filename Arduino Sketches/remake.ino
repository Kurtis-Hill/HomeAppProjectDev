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
//#include <WiFiClientSecureAxTLS.h>
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

//#include <Adafruit_ADS1015.h>

#include <DHT.h>;


#define DHTPIN 13  // what pin we're connected to
#define DHTTYPE DHT22   // DHT 22  (AM2302)
DHT dht(DHTPIN, DHTTYPE);

OneWire oneWire(0);
OneWire testWire(0);
DallasTemperature sensors(0);

//Web bits
const char *host = "klh19901017.asuscomm.com";
const int httpsPort = 443; 
const char fingerprint[] PROGMEM = "3704ac7ac54ee4adc8c983d119951491359f494e";


char* accessPointSsid = "ESP8266-HomeAppDeviceAccessPoint";
char* accessPointPassword = "HomeApp1234";

ESP8266WebServer server;
IPAddress local_ip(192,168,1,254);
IPAddress gateway(192,168,1,1);
IPAddress netmask(255,255,255,0);

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
          <input value="" type="text" id="tempHumid" placeholder="Enter The Name of the Sensor"/>
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
      var tempHumid = document.getElementById("tempHumid").value;
      data.tempHumid = tempHumid;

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

//Dallas Bus Temperature
struct DallasBusTempData {
  char sensorName[8][25];
  int sensorCount;
};
DallasBusTempData dallasBusTempData;

//DHT Sensor
struct DHTTempHumid {
  char DHTTempHumidSensorName[1][25];
  String tempPostData;
  String humidPostData;
//  String sensorPostData;
};
DHTTempHumid dhtTempHumid;

//ADC Sensor
struct AnalogSensors {
  char sensorNames[4][25];
  String analogPostData;
  String sensorPostData;
  int sensorCount;
};
AnalogSensors analogSensors;
//Adafruit_ADS1115 ads;

//char deviceName[20];
//char deviceSecret[32];

//need to double check this will hold refresh token
String token;
String refreshToken;

String homeAppLocalUrl = "192.168.0.39";
String homeAppUrl = "klh17101990.asuscomm.com";


String homeAppLogin = "/HomeApp/api/device/login_check";
String homeAppPort = "8101";
//const char *login = "/api/device/login_check";



// Need to decode this json string (data) and place different parts in different spiffs, wifi and sensor data
void handleSettingsUpdate(){
  String data = server.arg("plain");
  char jsonData[sizeof(data)];

  strcpy(jsonData, data.c_str());
  
  Serial.println("full payload");
  Serial.println(data);
  Serial.println("json char");
  Serial.println(jsonData);

  DynamicJsonDocument doc(1024);
  deserializeJson(doc, jsonData);

  
  if (!saveWifiCredentals(doc["wifi"]) || !saveSensorDataToSpiff(doc["sensorData"]) || saveDeviceUserSettings(doc["deviceCredentials"])) {   
    Serial.println("failed to save spiffs");
    server.send(500, "application/json", "{\"status\":\"Spiff error\"}"); 
  } else {
    server.send(200, "application/json", "{\"status\":\"ok\"}");
  }
  
  delay(500);
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
  Serial.println("setting device user setting now");
  String deviceName = doc["username"].as<String>();
  String secret = doc["password"].as<String>();

  Serial.println("deviceName");
  Serial.println(deviceName);
  Serial.println("secret");
  Serial.println(secret);

  if (
    deviceName == NULL 
    || deviceName == "" 
    || deviceName == "\0" 
    || deviceName == "null"
    ) {
    Serial.println("No device user name set, not setting any values");
    return true;
  }
  
  File deviceSettingsSPIFF = SPIFFS.open("/device.json", "w");

  if(serializeJson(doc, deviceSettingsSPIFF)) {
    Serial.println("Device settings serialization save success");
  } else {
    Serial.println("Device settings Serialization failure");
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
    return false;
  }

  return true;
}

// Dallas functions
bool saveDallasSensorData(DynamicJsonDocument dallasData) {
  int dallasCount = dallasData["busTempCount"].as<int>();
  Serial.println("dallas count");
  Serial.println(dallasCount);
  if (dallasCount >= 1) {
    Serial.println("dallas sensor data being set");
    File dallasSPIFF = SPIFFS.open("/dallas.json", "w");
  
    if(serializeJson(dallasData, dallasSPIFF)) {
      Serial.println("Dallas serialization save success");
    } else {
      Serial.println("Dallas Serialization failure");
      return false;
    }
    
    dallasSPIFF.close();

    Serial.println("Dallas SPIFF close, sucess");

    Serial.println(dallasBusTempData.sensorCount);
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
      Serial.println("serialization error");
      return false;
    }
    
    dallasBusTempData.sensorCount = dallasDoc["busTempCount"].as<int>();

    if (dallasBusTempData.sensorCount == 0) {
      Serial.println("No Sensor count not setting any values");
      return true;
    }
   
    Serial.print("dallas sensor count ");
    Serial.println(dallasBusTempData.sensorCount);

    int x = 1;
    for (int i=0; i < dallasBusTempData.sensorCount; ++i) {
      String nameCheck = dallasDoc["busTempNameArray"][i].as<String>();

      if(nameCheck == NULL || nameCheck == "" || nameCheck == "\0" || nameCheck == "null") {
        Serial.println("Name check failed skipping this sensor");
        continue;
      }
      
      strncpy(dallasBusTempData.sensorName[i], dallasDoc["busTempNameArray"][i], sizeof(dallasBusTempData.sensorName[i]));
      Serial.print("dallas sensor name ");
      Serial.println(dallasBusTempData.sensorName[i]);
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
  WiFi.softAP(accessPointSsid, accessPointPassword);
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
      Serial.println(ssid);
      Serial.println(pass);
      if (WiFi.status() == WL_CONNECTED){
        Serial.println("Wifi connection made");
        WiFi.printDiag(Serial); 
        Serial.println("");
        Serial.print("Network IP Address ");
        Serial.println(WiFi.localIP());
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





//Production version
bool deviceLogin() {
  WiFiClientSecure httpsClient;

  Serial.printf("Using fingerprint '%s'\n", fingerprint);
  httpsClient.setFingerprint(fingerprint);
  httpsClient.setTimeout(15000); // 15 Seconds
  delay(1000);
  
  Serial.print("Connecting via HTTPS to :");
  Serial.print(host);

  int r=0;
  while((!httpsClient.connect(host, httpsPort))){
      delay(100);
      Serial.print(".");
      r++;
  }
  if(r==30) {
    Serial.println("Connection failed");
    return false;
  }

  httpsClient.print(String("POST ")+ homeAppUrl + homeAppLogin + " HTTP/1.1\r\n" +
               "Host: " + host + "\r\n" +
               "Content-Type: application/json"+ "\r\n" +
               "Content-Length: 250" + "\r\n\r\n" +
               ""+
               "Connection: close\r\n\r\n"
  );

  Serial.println("Request sent to");
  Serial.println(host + homeAppLogin);
}



DynamicJsonDocument sendPostRequestReturnPayload(String url, DynamicJsonDocument requestPayload) {
  
}

DynamicJsonDocument returnJsonRequestPayload(String payload) {
  
}


DynamicJsonDocument sendPostRequestReturnPayloadDev(String url, DynamicJsonDocument requestPayload, String jsonData) {
  WiFiClient client;
  HTTPClient http;

  Serial.print("[HTTP] begin connecting to... ");
  Serial.println(url);
  
  http.begin(client, url);
  http.addHeader("Content-Type", "application/json");

  Serial.println("[HTTP] POST...");
  int httpCode = http.POST(jsonData);

  // httpCode will be negative on error
  if (httpCode > 0) {
    Serial.printf("[HTTP] POST... code: %d\n", httpCode);
    if (httpCode == HTTP_CODE_OK) {
      const String& payload = http.getString();
      Serial.print("received payload: ");
      Serial.println(payload);
      
      return returnJsonRequestPayloadDev(payload);
    }
  } else {
      Serial.printf("[HTTP] POST... failed, error: %s\n", http.errorToString(httpCode).c_str());
 
  }
  http.end();
}

DynamicJsonDocument returnJsonRequestPayloadDev(String payload) {
  
}


bool deviceLoginDev() {
  Serial.print("Begining to log in");
  WiFiClient client;
  HTTPClient http;

  Serial.print("[HTTP] begin connecting to... ");
  Serial.println("http://"+homeAppLocalUrl+":"+homeAppPort+homeAppLogin);
  
  http.begin(client, "http://"+homeAppLocalUrl+":"+homeAppPort+homeAppLogin);
  http.addHeader("Content-Type", "application/json");

  Serial.print("Getting device json data from spiff");
  String jsonData = getSerializedDeviceNameSecret();
  
  Serial.println("[HTTP] POST...");
  int httpCode = http.POST(jsonData);

  // httpCode will be negative on error
  if (httpCode > 0) {
    Serial.printf("[HTTP] POST... code: %d\n", httpCode);
    if (httpCode == HTTP_CODE_OK) {
      const String& payload = http.getString();
      Serial.print("received payload: ");
      Serial.println(payload);
      
      return saveTokensFromLogin(payload);
    }
  } else {
      Serial.printf("[HTTP] POST... failed, error: %s\n", http.errorToString(httpCode).c_str());
      return false;
  }
  http.end();
  
  return true;
}

bool saveTokensFromLogin(String payload) {  
  char jsonData[1000];

  strcpy(jsonData, payload.c_str());

  DynamicJsonDocument responseTokens(1024);
  DeserializationError error = deserializeJson(responseTokens, jsonData);

  if (error) {
    Serial.println("deserialization error");
    return false;
  }

  Serial.println("token json");
  Serial.println(responseTokens["token"].as<String>());
  
  token = responseTokens["token"].as<String>();
  refreshToken = responseTokens["refreshToken"].as<String>();
  
  Serial.println("Token: "); //@DEBUG
  Serial.println(token);
  Serial.println("refreshToken");
  Serial.println(refreshToken);

  return true;
}

bool sendUpdateSensorData() {
  
}
/////<!---END OF NETWORK METHODS---!>//////
void resetDevice() {
  createAccessPoint();
  //@TODO delete all the spiffs too
}

void restartDevice() {
  ESP.restart;
}


String getSerializedDeviceNameSecret() {
  File deviceFile = SPIFFS.open("/device.json", "r");
  
  String deviceJson;
//  uint16_t i = 0;
  while(deviceFile.available()){
    deviceJson += char(deviceFile.read());
//    i++;
  }
  Serial.println("whole json string from spiff:");
  Serial.println(deviceJson); //@DEBUG
  deviceFile.close();

  return deviceJson;
}




void setup() {
//  Serial.begin(9600);
  Serial.begin(115200);
  Serial.println("Searial started");

  Serial.print("Starting web servers...");
  //Web
  server.on("/",[](){server.send_P(200,"text/html",webpage);});
  server.on("/settings", HTTP_POST, handleSettingsUpdate);
  
  server.on("/reset-device", HTTP_GET, resetDevice);
  server.on("/restart-device", HTTP_GET, restartDevice);
  Serial.println("Servers Begun");
  server.begin();

  Serial.println("...Webservers started");
  delay(5000);
  SPIFFS.begin();

  Serial.println("Begining setup");
  if (setupNetworkConnection()) {
    // change device login for production
    if (deviceLoginDev()) {
      if (!setDallasValues()) {
        Serial.println("Failed to set dallas values");
      }        
    } else {
      Serial.println("Device has failed to login, cannot send any data"); 
    }
  }

  Serial.println("End of Setup");
}

void loop() {
//  readWifiJson();
  Serial.println("Loop Begin");
  Serial.println("Handling Server Client...");
  server.handleClient();
  Serial.println("Server ClientHandled...");

  if(WiFi.status()== WL_CONNECTED){
    Serial.println("Connected to wifi");
  }
  Serial.println("Loop finished");
  delay(1000);
}

void readWifiJson() {
  File wifiCredentials = SPIFFS.open("/device.json", "r");
  if (wifiCredentials) {
    StaticJsonDocument<200> wifiDoc;
    DeserializationError error = deserializeJson(wifiDoc, wifiCredentials);
    String deviceNameCheck = wifiDoc["deviceName"].as<String>();
    String secretOne = wifiDoc["deviceSecret"].as<String>();
  
    const char* deviceName = wifiDoc["deviceName"];
    const char* deviceSecret = wifiDoc["deviceSecret"];

    Serial.println("deviceNameCheck");
    Serial.println(deviceNameCheck);
    Serial.println("secret");
    Serial.println(secretOne);
  
  
    Serial.println("deviceNameCheck22");
    Serial.println(deviceName);
    Serial.println("secret22");
    Serial.println(deviceSecret);

//    String passOne = wifiDoc["busTempNameArray"][0].as<String>();
//    Serial.println("bus temp count read wifi");
//    Serial.println(ssidOne);
//    Serial.println("sensorname one");
//    Serial.println(passOne);
  }
  wifiCredentials.close();
}
