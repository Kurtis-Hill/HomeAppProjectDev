#include <Wire.h>
#include <Adafruit_Sensor.h>

#include <SPIFFSReadServer.h>
#include <SPIFFSIniFile.h>

#include <ESP8266WiFi.h>
#include <ESP8266WiFiAP.h>
#include <ESP8266WiFiGeneric.h>
#include <ESP8266WiFiMulti.h>
#include <ESP8266WiFiScan.h>
#include <ESP8266WiFiSTA.h>
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

#include <DHT.h>;

#define MICRO_ESP_SERIAL 115200
#define NODE_MCU_SERIAL 9600

// ESP8266-01
//#define DEVICE_SERIAL MICRO_ESP_SERIAL
#define DEVICE_SERIAL NODE_MCU_SERIAL


//Web bits
// Test
//#define HOMEAPP_HOST "https://192.168.1.172"
#define HOMEAPP_HOST "https://192.168.1.158"
// Prod
//#define HOMEAPP_HOST "https://klh19901017.asuscomm.com"
#define HOMEAPP_URL "HomeApp"
#define HOMEAPP_PORT "8101"

#define HOMEAPP_LOGIN "api/device/login_check"
#define HOMEAPP_REFRESH_TOKEN "api/device/token/refresh"
#define HOMEAPP_IP_UPDATE "api/device/ipupdate"
#define HOME_APP_CURRENT_READING "api/device/esp/update/current-reading"
#define HOME_APP_REGISTER_DEVICE "api/device/register"

#define EXTERNAL_IP_URL "http://api.ipify.org/?format=json"

const char fingerprint[] PROGMEM = "60ee151bee994d6ca826a69abce1e724173721ca";

String ipAddress;
String publicIpAddress;
int publicIpAddressRequestAttempts = 0;
String token;
String refreshToken;
bool deviceRegistered = false;

int wifiRetryTimer = 0;
int wifiRetryCounter = 0;

bool deviceLoggedIn = false;

// Access ponint network bits
#define ACCESSPOINT_SSID "HomeApp-D-A-D-AP"
#define ACCESSPOINT_PASSWORD "HomeApp1234"

ESP8266WiFiMulti WiFiMulti;
ESP8266WebServer server;
IPAddress local_ip(192, 168, 1, 254);
IPAddress gateway(192, 168, 1, 1);
IPAddress netmask(255, 255, 255, 0);

#define WIFI_OFF_LED_PIN 5
#define DEVICE_ON_LED_PIN 16

//LEDS
int ledPins[4] = {WIFI_OFF_LED_PIN, DEVICE_ON_LED_PIN, 4, 0};

// DHT
#define DHTNAME "Dht"
#define DHTS_ASSINGED_TO_DEVICE 4
#define DHTPIN 2
#define DHTTYPE DHT22
//DHT dht(DHTPIN, DHTTYPE);
DHT* dhtSensors[DHTS_ASSINGED_TO_DEVICE];

struct DhtSensor {
  char sensorName[DHTS_ASSINGED_TO_DEVICE][25];
  float tempReading[DHTS_ASSINGED_TO_DEVICE];
  float humidReading[DHTS_ASSINGED_TO_DEVICE];
  int interval[DHTS_ASSINGED_TO_DEVICE];
  int sendNextReadingAt[DHTS_ASSINGED_TO_DEVICE];
  int pinNumber[DHTS_ASSINGED_TO_DEVICE];
  int sensorCount = 0;
  bool activeSensor = false;
  bool valuesAreSet = false;
  bool settingsJsonExists = false;
};
DhtSensor dhtSensor;

// Dallas
//#define ACTIVE_START_PIN 2
//#define LAST_ACTIVE_PIN 2
#define DALLASNNAME "Dallas"
#define MAX_DALLAS_SENSORS 3

OneWire oneWire(0);
DallasTemperature sensors(&oneWire);

//Dallas Bus Temperature
struct DallasTempData {
  char sensorName[MAX_DALLAS_SENSORS][25];
  float tempReading[MAX_DALLAS_SENSORS];
  int sendNextReadingAt[MAX_DALLAS_SENSORS];
  int interval[MAX_DALLAS_SENSORS];
  int sensorCount = 0;
  int pinNumber;
  bool activeSensor = false;
  bool valuesAreSet = false;
  bool settingsJsonExists = false;
};
DallasTempData dallasTempData;

//Relay
#define RELAYNAME "GenericRelay"
#define MAX_RELAYS 5
struct RelayData {
  char sensorName[MAX_RELAYS][25];
  bool currentReading[MAX_RELAYS];
  int pinNumber[MAX_RELAYS];
  int sendNextReadingAt[MAX_RELAYS];
  int interval[MAX_RELAYS];
  int sensorCount = 0;
  bool activeSensor = false;
  bool valuesAreSet = false;
  bool settingsJsonExists = false;
};

RelayData relayData;

const char* deviceSpiffs[3][10] = {"dallas", "dht", "relay"};

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
        <div class="Form-style" style="display: none">
          <h3>Enter Device Information</h3>
          <label for="groupName">Device Name</label>
          <br>
          <input value="" type="text" id="deviceName" placeholder="Enter Your Account GroupName"/>
        </div>

        <div class="Form-style" style="display: none">
          <label for="sensorName">Device Password</label>
          <br>
            <input value="" type="password" id="deviceSecret" placeholder="Enter The Secret Given To You By The App"/>
        </div>
        <br>
        <div class="Form-style" style="display: none">
          <h2>Enter Sensor Information</h2>
          <label class="heading">Temperature and Humidity Sensor</label>
          <br>
          <input type="radio" class="checkmark" name="tempHumidRadio" value="Yes" onchange="hiddenDisplay('tempDisplay')">Yes<br></input>
          <input type="radio" class="checkmark" name="tempHumidRadio" value="No" onchange="hiddenDisplay('tempDisplay')" checked>No<br></input>
        </div>
        <div class="Form-style" id="tempDisplay" style="display: none;">
          <input value="" type="number"  id="dhtSensorInterval" placeholder="Enter The interval for the reading to be taken in seconds"/>
          <br><br>
          <input value="" type="text" id="dhtSensor" placeholder="Enter The Name of the Sensor"/>
          <br>
        </div>


        <div class="Form-style" style="display: none">
          <label class="heading">Temperature Bus Sensor</label>
          <br>
            <input type="radio" class="checkmark" name="busTempRadio" value="Yes" onchange="hiddenDisplay('other')">Yes</input><br>
            <input type="radio" class="checkmark" name="busTempRadio" value="No" onchange="hiddenDisplay('other')" checked>No</input>
        </div>
        <div class="Form-style" id="other" style="display: none;">
          <input value="" type="number"  id="busTempPinNumber" placeholder="Enter The Pin number for the sensor"/>
          <br><br>
          <input value="" type="number"  id="busTempInterval" placeholder="Enter The interval for the reading to be taken in seconds"/>
          <br><br>
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

        <div class="Form-style" style="display: none">
          <label for="Analog">Analog Sensor</label>
          <br>
            <input type="radio" class="checkmark" name="AnalogCheck" value="Yes" onchange="hiddenDisplay('Analog')">Yes<br>
            <input type="radio" class="checkmark" name="AnalogCheck" value="No" checked onchange="hiddenDisplay('Analog')">No<br>
        </div>
        <div class="Form-style" id="Analog" style="display: none;">
          <label for="AnalogSensorNames" class="heading">Analog Sensors Names</label>
          <br>
          <input value="" type="number"  id="analogPinNumber" placeholder="Enter The Pin number for the analog"/>
          <br><br>
          <input value="" type="number"  id="analogInterval" placeholder="Enter The interval for the reading to be taken in seconds"/>
          <br><br>
          <input value="" type="text" id="AnalogName1" placeholder="Enter The First Sensor Name"/>
          <br><br>
          <input value="" type="text" id="AnalogName2" placeholder="Enter The Second Sensor Name"/>
          <br><br>
          <input value="" type="text" id="AnalogName3" placeholder="Enter The Third Sensor Name"/>
          <br><br>
          <input value="" type="text" id="AnalogName4" placeholder="Enter The Forth Sensor Name"/>
          <br><br>
        </div>

        <div class="Form-style" style="display: none">
            <label for="Relay">Relay Sensor</label>
            <br>
              <input type="radio" class="checkmark" name="AnalogCheck" value="Yes" onchange="hiddenDisplay('Relay')">Yes<br>
              <input type="radio" class="checkmark" name="AnalogCheck" value="No" checked onchange="hiddenDisplay('Relay')">No<br>
        </div>
        <div class="Form-style" id="Relay" style="display: none;">
            <label for="RelaySensorNames" class="heading">Relay Sensors Names</label>
            <br>
            <input value="" type="text" id="RelayName1" placeholder="Enter The First Sensor Name"/>
            <input value="" type="number"  id="RelayName1PinNumber" placeholder="Enter The Pin number for the sensor"/>
            <br><br>
            <input value="" type="text" id="RelayName2" placeholder="Enter The Second Sensor Name"/>
            <input value="" type="number"  id="RelayName2PinNumber" placeholder="Enter The Pin number for the sensor"/>
            <br><br>
            <input value="" type="text" id="RelayName3" placeholder="Enter The Third Sensor Name"/>
            <input value="" type="number"  id="RelayName3PinNumber" placeholder="Enter The Pin number for the sensor"/>
            <br><br>
            <input value="" type="text" id="RelayName4" placeholder="Enter The Forth Sensor Name"/>
            <input value="" type="number"  id="RelayName4PinNumber" placeholder="Enter The Pin number for the sensor"/>
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
      var jsonData = {};
      
      if (ssid) {
        jsonData.wifi = {'ssid': ssid, 'password': password};
      }
      var sensorData = {};
      function buildBusSensorObject(sensorCount, sensorNames, pinNumber, readingInterval) {
        return {
          'sensorCount': sensorCount,
          'sensorNames': sensorNames,
          'pinNumber': pinNumber,
          'readingInterval': readingInterval
        };
      }

      function buildRegularSensorObject(sensorName, readingInterval) {
        return {
          'sensorName': sensorName,
          'readingInterval': readingInterval
        };
      }

      function prepareMultiSensorObject(sensorNames, pinNumbers) {
        return {
          'sensorNames': sensorNames,
          'pinNumbers': pinNumbers
        };
      }
      
      var busTempPinNumber = parseInt(document.getElementById("busTempPinNumber").value);

      if (busTempPinNumber) {
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
    
          var busTempInterval = document.getElementById("busTempInterval").value ? parseInt(document.getElementById("busTempInterval").value) : 60;
          busTempInterval = busTempInterval * 1000;

          sensorData.dallas = buildBusSensorObject(busTempCount, busTempNameArray, busTempPinNumber, busTempInterval);
      }

      // var data = {'dallas': {sensorCount: busTempCount, sensorNames: busTempNameArray, pinNumber: busTempPinNumber, readingInterval: busTempInterval}};

      var analogPinNumber = parseInt(document.getElementById("analogPinNumber").value);
      if (analogPinNumber) {
        // ADC Sensor Names
        var analogName1 =  document.getElementById("AnalogName1").value;
        var analogName2 =  document.getElementById("AnalogName2").value;
        var analogName3 =  document.getElementById("AnalogName3").value;
        var analogName4 =  document.getElementById("AnalogName4").value;
        var analogNames = [analogName1, analogName2, analogName3, analogName4];
  
        analogNamesArray = analogNames.filter(Boolean);
        var analogCount = analogNamesArray.length;
  
        var analogInterval = document.getElementById("analogInterval").value ? parseInt(document.getElementById("analogInterval").value) : 60;
        analogInterval = analogInterval * 1000;
  
        // sensorData.analog = {'analogNames': analogNamesArray, 'analogCount': analogCount, 'analogPinNumber': analogPinNumber, 'analogInterval': analogInterval};
        sensorData.soil = buildBusSensorObject(analogCount, analogNamesArray, analogPinNumber, analogInterval);
      }

      

      // DHT Sensor
      var dhtSensor = document.getElementById("dhtSensor").value;
      if (dhtSensor) {
        var dhtSensorInterval = document.getElementById("dhtSensorInterval").value ? parseInt(document.getElementById("dhtSensorInterval").value) : 60;
        dhtSensorInterval = dhtSensorInterval * 1000;
        // data.dhtSensor = {'sensorName' : dhtSensor, 'interval': dhtSensorInterval};
        sensorData.dht = buildRegularSensorObject(dhtSensor, dhtSensorInterval);
      }

      var relaySensorPinNumber1 = parseInt(document.getElementById("RelayName1PinNumber").value);
      if (relaySensorPinNumber1) {    
        var relaySensorName1 = document.getElementById("RelayName1").value;
        var relaySensorName2 = document.getElementById("RelayName2").value;
        var relaySensorName3 = document.getElementById("RelayName3").value;
        var relaySensorName4 = document.getElementById("RelayName4").value;
        var relaySensorNames = [relaySensorName1, relaySensorName2, relaySensorName3, relaySensorName4];
        
        var relaySensorPinNumber2 = parseInt(document.getElementById("RelayName2PinNumber").value);
        var relaySensorPinNumber3 = parseInt(document.getElementById("RelayName3PinNumber").value);
        var relaySensorPinNumber4 = parseInt(document.getElementById("RelayName4PinNumber").value);
        var relaySensorPinNumbers = [relaySensorPinNumber1, relaySensorPinNumber2, relaySensorPinNumber3, relaySensorPinNumber4],
        
        relaySensorNamesArray = relaySensorNames.filter(Boolean);
        relaySensorPinNumbersArray = relaySensorPinNumbers.filter(Boolean);
        var relayCount = relaySensorNamesArray.length;

        sensorData.relay = [];
        for (var i = 0; i < relayCount; i++) {
          sensorData.relay.push(buildRegularSensorObject(relaySensorNamesArray[i], relaySensorPinNumbersArray[i]));
        }
      }

      if (sensorData.soil || sensorData.dallas || sensorData.dht || sensorData.relay) {
        jsonData.sensorData = sensorData;
      }

      // var jsonData = {'wifi': wifi, 'sensorData': sensorData, 'deviceCredentials': deviceCredentials};

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

bool setupNetworkConnection() {
  Serial.println("Wifi connecting");
  WiFi.softAPdisconnect(true);
  WiFi.disconnect();
  
  if (connectToNetwork()) {
    return true;
  }
  createAccessPoint();
  
  return false;
}

void createAccessPoint() {
  Serial.println("Setting up wireless access point");
  WiFi.mode(WIFI_AP);
  WiFi.softAPConfig(local_ip, gateway, netmask);
  WiFi.softAP(ACCESSPOINT_SSID, ACCESSPOINT_PASSWORD);
  Serial.println("AP MODE Activated");
  WiFi.printDiag(Serial);
}


bool connectToNetwork() {
  if(!SPIFFS.exists("/wifi.json")){
    Serial.print("wifi.json not found");  
    return false;
  }
  Serial.println("wifi spiff extits");
  
  String wifiCredentials = getSerializedSpiff("/wifi.json");
  if (!wifiCredentials) {
    Serial.println("no WIFI spiff data");

    return false;
  }
    
  DynamicJsonDocument wifiDoc = getDeserializedJson(wifiCredentials, 1024);

  String ssid = wifiDoc["ssid"].as<String>();
  String pass = wifiDoc["password"].as<String>();
  if (wifiDoc["ssid"].isNull()) {
    Serial.println("No network SSID set, not attempting to connect");
    return false;
  }
  
  Serial.println(ssid); //@DEV
  Serial.println(pass);
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, pass);

  int timeout = millis() + 35000;
  Serial.printf("Connecting to wifi with a %d millisecond timeout\n", timeout);
  while(WiFi.status() != WL_CONNECTED){
    int currentTime = millis();
    // leave serial in when taken out exceptions get throws, whos knows
    Serial.print(".");
    if (timeout - currentTime < 0) {
      Serial.println("Failed to connect to wifi network");
      break;
    }
    if (WiFi.status() == WL_CONNECTED){
      Serial.println("Wifi connection made");
      WiFi.printDiag(Serial);
      Serial.print("Network IP Address: ");
      Serial.println(WiFi.localIP());
      Serial.println("saved ip address");
      ipAddress = ipToString(WiFi.localIP());
      Serial.println(ipAddress);
      
      digitalWrite(WIFI_OFF_LED_PIN, LOW);      
           
      return true;
    }
  }
  Serial.print("wasnt able to connect");

  digitalWrite(WIFI_OFF_LED_PIN, HIGH);
  
  
  return false;
}

void handleWifiReconnectionAttempt() {
  if (!SPIFFS.exists("/wifi.json")) {
    return;
  }
  int currentTime = millis();
  if (wifiRetryTimer - currentTime <= 0) {
    Serial.println("handling wifi re connection");
    bool connectionSuccess = setupNetworkConnection();

    if (connectionSuccess == false) {
      wifiRetryTimer = millis() + 60000;
    }    
  }
}


// decode this json string (data) and place different parts in different spiffs, wifi and sensor data
void handleSettingsUpdate(){
  Serial.println("Handling settings update!!!!");
  String data = server.arg("plain");

  Serial.println("Getting derialized json data from post server args");
  DynamicJsonDocument doc = getDeserializedJson(data, 2568);

  if (doc == NULL) {
    server.send(500, "application/json", "{\"status\":\"failed to deserialize json\"}");
    return;
  }
  
  bool wifiSuccess = true;
  bool deviceCredentialsSuccess = true;
  bool sensorDataSuccess = true;

  bool wifiChanged = false;
  bool deviceCredenatialsChanged = false;
  bool sensorDataChanged = false;

  if (!doc["wifi"].isNull()) {
    Serial.println("Wifi credentials found in json setting values");
    wifiChanged = true;
    if (!saveWifiCredentials(doc["wifi"])) {
      Serial.println("No wifi crednetials saved");
      wifiSuccess = false;
    }      
  }

  if (!doc["sensorData"].isNull()) {
    sensorDataChanged = true;
    Serial.println("Sensor data found in json attempting to save data");
    if (!saveSensorDataToSpiff(doc["sensorData"])) {
      Serial.println("failed to save sensor data spiffs");
      sensorDataSuccess = false;
    }
  }

  if (!doc["deviceCredentials"].isNull()) {
    deviceCredenatialsChanged = true;
    if (!saveDeviceUserSettings(doc["deviceCredentials"])) {
      deviceCredentialsSuccess = false;
    }  
  }
  
  Serial.println("Finished saving credentials");
  if (wifiSuccess == true && deviceCredentialsSuccess == true && sensorDataSuccess == true) {
    Serial.println("All SPIFFS saved successfully");
    server.send(200, "application/json", "{\"status\":\"ok\"}");
  } else if(wifiSuccess == false && deviceCredentialsSuccess == false && sensorDataSuccess == false) {
    Serial.println("Errors detected while saving all SPIFFS");
    server.send(500, "application/json", "{\"status\":\"all updates failed\"}");
  } else {
    server.send(500, "application/json", "{\"status\":\"some updates failed\"}");
  }

  if (wifiChanged == true && wifiSuccess == true) {
    publicIpAddressRequestAttempts = 0;
//    if (setupNetworkConnection()) {
//      Serial.print("Getting external IP... ");
//      getExternalIP();
//      deviceLoggedIn = deviceLogin();
//    }
  }  

  if (sensorDataChanged == true) {
    checkSensorSPIFFSExist();
  }
}

bool saveWifiCredentials(DynamicJsonDocument doc) {
  if (doc["ssid"].isNull()) {
    Serial.println("Security is not trying to be set, empty values");
    return false;
  }
  
  const char* ssid = doc["ssid"];
  const char* password = doc["password"];
  if (SPIFFS.exists("/wifi.json")) {
    Serial.println("wifi json was found removing current json");
    SPIFFS.remove("/wifi.json");
  }
  Serial.println("Security values are being set");
  DynamicJsonDocument wifiDoc(128);
  
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

bool saveDeviceUserSettings(DynamicJsonDocument doc) {
  Serial.println("Setting device user setting now");
  String deviceName = doc["username"].as<String>();
  String secret = doc["password"].as<String>();

  if (doc["username"].isNull()) {
    Serial.println("No device user name sent in payload, not setting any values and using defaults");
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

// Wrapper for saving sensor data for each sensor in different SPIFFS
bool saveSensorDataToSpiff(DynamicJsonDocument doc) {
  if (!saveDallasSensorData(doc["dallas"])) {
    Serial.println("failed to set Dallas Spiff");
  }
  
  if (!saveDhtSensorData(doc["dht"])) {
    Serial.println("No Dht Spiff");
  }
  
  if (!saveRelaySensorData(doc["relay"])) {
    Serial.println("No relay Spiff");
  }
  
  return true;
}

bool saveRelaySensorData(DynamicJsonDocument relayDoc) {
  for (int i = 0; i <= MAX_RELAYS; ++i) {
    if (relayDoc[i]["sensorName"].isNull()) {
      if (i == 0) {
        Serial.print("Sensor data for relay was not recieved correctly sensor name is not set");  
        return false;
      }
      continue;
    }
    Serial.print("Relay sensor data recieved sensor name:");
    Serial.print(relayDoc[i]["sensorName"].as<String>());
  }
  if (SPIFFS.exists("/relay.json")) {
    Serial.print("Relay spiff existed removing before creating");
    SPIFFS.remove("/relay.json");
  }

  Serial.println("Opening relay SPIFF for writing");
  File relaySPIFF = SPIFFS.open("/relay.json", "w");

  if(serializeJson(relayDoc, relaySPIFF)) {
    Serial.println("Relay serialization save success");
  } else {
    relaySPIFF.close();
    Serial.println("Relay Serialization failure");
    
    return false;
  }
  
  relaySPIFF.close();
  Serial.println("Relay SPIFF close, sucess");

  return true;
}

bool setRelayValues() {
  Serial.println("Attempting to set relay values");
  if (!SPIFFS.exists("/relay.json")) {
    Serial.print("No relay json found");
    SPIFFS.remove("/relay.json");
  }

  String relaySensorData = getSerializedSpiff("/relay.json");

  DynamicJsonDocument relayDoc = getDeserializedJson(relaySensorData, 1024);

  relayData.sensorCount = 0;
  for(int i = 0; i < MAX_RELAYS; ++i) {
    if (relayDoc[i]["sensorName"].isNull()) {
      if (i == 0) {
        Serial.println("Name check failed on first relay failed to set relay");    
        return false;           
      }
      Serial.println("Name check failed skipping relay this sensor");          
      continue;
    }

    String relaySensorName = relayDoc[i]["sensorName"];
    int pinNumber = relayDoc[i]["pinNumber"].as<int>();

    int readingInterval = relayDoc[i]["readingInterval"].as<int>();
    if (readingInterval) {
      relayData.interval[i] = readingInterval;  
    } else {
      relayData.interval[i] = 6000;
    }

    strncpy(relayData.sensorName[i], relayDoc[i]["sensorName"], sizeof(relayData.sensorName[i]));  
    Serial.print("relay sensor name: ");
    Serial.println(relayData.sensorName[i]);      

    relayData.pinNumber[i] = pinNumber;
    Serial.printf("relay pin is: %d\n", relayData.pinNumber[i]);
    pinMode(pinNumber, OUTPUT);

    relayData.interval[i] = readingInterval;
    Serial.printf("relay interval is: %d\n", relayData.interval[i]);
    
    relayData.sensorCount++;
    Serial.print("Marking relays as set and active: ");
    relayData.valuesAreSet = true;
    Serial.println(relayData.activeSensor);
    Serial.println(relayData.valuesAreSet);
  }

  return true;
}

bool saveDhtSensorData(DynamicJsonDocument dhtData) {
  for (int i = 0; i <= MAX_DALLAS_SENSORS; ++i) {
    if (dhtData[i]["sensorName"].isNull()) {          
      if (i == 0) {
        Serial.print("Sensor data for dht was not recieved sensor name is not set");  
        return false;
      }
      continue;
    }
    Serial.print("Dht sensor data recieved sensor name:");
    Serial.println(dhtData[i]["sensorName"].as<String>());
  }
  if (SPIFFS.exists("/dht.json")) {
    Serial.print("Dht spiff exists removing before building new entry");  
    SPIFFS.remove("/dht.json");
  }
  
  Serial.println("Opening dht SPIFF for writing");
  File dhtSPIFF = SPIFFS.open("/dht.json", "w");

  if(serializeJson(dhtData, dhtSPIFF)) {
    Serial.println("Dht serialization save success");
  } else {
    dhtSPIFF.close();
    Serial.println("Dht Serialization failure");
    return false;
  }
  
  dhtSPIFF.close();
  Serial.println("Dht SPIFF close, sucess");

  return true;
}

bool saveDallasSensorData(DynamicJsonDocument dallasData) {
  for (int i = 0; i <= MAX_DALLAS_SENSORS; ++i) {
    if (dallasData[i].isNull()) {
      if (i == 0) {
        Serial.print("Sensor data for dallas was not recieved correctly sensor name is not set");  
        return false;
      }
      continue;
    }
  }

  if (SPIFFS.exists("dallas.json")) {
    Serial.print("Dallas spiff exists removing before building new entry");  
    SPIFFS.remove("dallas.json");
  }


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

  return true;    
}



String ipToString(IPAddress ip){
  String stringIP = "";
  for (int i=0; i<4; i++) {
    stringIP += i  ? "." + String(ip[i]) : String(ip[i]);
  }

  return stringIP;
}

void getExternalIP() {
  if (publicIpAddressRequestAttempts >= 5) {
    Serial.printf("Public ip request attemps has exceeded maximum allowed of %d \n", publicIpAddressRequestAttempts);
    return;
  }
  
  WiFiClient client;
  HTTPClient https;
  Serial.print("[HTTP] begin connecting to... ");
  Serial.println(EXTERNAL_IP_URL);

  if (https.begin(client, EXTERNAL_IP_URL)) {
    Serial.print("[HTTP] GET...\n");
    int httpCode = https.GET();
    ++publicIpAddressRequestAttempts;
    if (httpCode > 0) {
      Serial.printf("[HTTP] GET... code: %d\n", httpCode);
      if (httpCode == HTTP_CODE_OK || httpCode == HTTP_CODE_MOVED_PERMANENTLY) {
        String payload = https.getString();
        Serial.println("External IP Payload: ");
        Serial.println(payload);

        Serial.println("Attempting to deserialize externalIP");
        DynamicJsonDocument externalIP = getDeserializedJson(payload, 512);

        publicIpAddress = externalIP["ip"].as<String>();
      } else {
        Serial.println("Not expecting response code for getting external IP");
        delay(1000);
      }
    } else {
        Serial.printf("[HTTP] GET... failed, error: %s\n", https.errorToString(httpCode).c_str());
        delay(1000);
    }
    https.end();
  } else {    
    Serial.printf("[HTTP} Unable to connect\n");
    delay(1000);
  }
}


String buildHomeAppUrl(String endpoint) {
  Serial.println("building url");
//  String url = sprintf(
//    "%s:%s/%s/%s",
//    HOMEAPP_HOST,
//    HOMEAPP_PORT,
//    HOMEAPP_URL,
//    endpoint
//  );
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
//    Serial.println("Authorization Bearer "+token);
    https.addHeader("Authorization", "Bearer "+token);
  }

  Serial.println("[HTTP] POST...");
  int httpCode = https.POST(jsonData);

  // cannnot try to re-login on 401 here because of a bug that is in the library so have to do it on next loop
  if (httpCode > 0) {
    Serial.printf("[HTTP] POST... code: %d\n", httpCode);
    if (httpCode == HTTP_CODE_OK) {
      const String payload = https.getString();
      Serial.print("received payload: ");
      Serial.println(payload);
      
      return payload;
    }
    if (httpCode == 401) {
      Serial.println("device has failed to authenticate");
      deviceLoggedIn = false;
      const String payload = https.getString();
      Serial.print("received payload: ");
      Serial.println(payload);
    }
  } else {
      Serial.printf("[HTTP] POST... failed, error: %s%d\n", https.errorToString(httpCode).c_str(), httpCode);
  }
  https.end();
  
  return "";
}

bool registerDevice() {
  Serial.println("Begining To Register Device");

  DynamicJsonDocument registerDoc(54);
  registerDoc["ipAddress"] = ipAddress;

  if (registerDoc["ipAddress"].isNull()) {
    Serial.println("ip address is null not sending request");
    return false;
  }

  String jsonData;

  serializeJson(registerDoc, jsonData);

  Serial.print("serialized refresh token data to send");//@dev
  Serial.print(jsonData);

  String url = buildHomeAppUrl(HOME_APP_REGISTER_DEVICE);
  Serial.print("refresh token url: "); //@DEV
  Serial.println(url);

  String response = sendHomeAppHttpsRequest(url, jsonData, false);
 if (response == "" || response == "null") {
    return false;
  }

  deviceRegistered = true;
  
  return true;
}


bool deviceLogin() {
  Serial.println("Logging device in");

  bool refreshTokenSuccess = handleRefreshTokens();
  if (refreshTokenSuccess == true) {
    return true;
  }
  
  if (!SPIFFS.exists("/device.json")) {    
    Serial.println("Device json does not exist no longer attempting to login");
    
    return false;
  }
  
  String deviceData = getSerializedSpiff("/device.json");

  DynamicJsonDocument loginDoc = getDeserializedJson(deviceData, 512);

  if (loginDoc["username"].isNull()) {
    Serial.println("device json username is empty failing login");
    return false;
  }
  
  loginDoc["ipAddress"] = ipAddress;

  if(publicIpAddress != NULL && publicIpAddress != "" && !publicIpAddress) {
    Serial.print("addinng external ip to request... ");
    Serial.println(publicIpAddress);
    loginDoc["externalIpAddress"] = publicIpAddress;
  }

  String jsonData;
  serializeJson(loginDoc, jsonData);

  String url = buildHomeAppUrl(HOMEAPP_LOGIN);
  String payload = sendHomeAppHttpsRequest(url, jsonData, false);

  if (payload == "" || payload == "null") {
    Serial.println("payload empty device has failed to login");
    return false;
  }
  
  return saveTokensFromLogin(payload);
}

 bool handleRefreshTokens() {
  if (refreshToken.length() > 1 || !refreshToken || refreshToken == NULL ) {    
    Serial.println("refresh token was empty not attempting to refresh");
    return false;
  }

  DynamicJsonDocument refreshTokenDoc(1024);
  refreshTokenDoc["refreshToken"] = refreshToken;

  if (refreshTokenDoc["refreshToken"].isNull()) {
    Serial.println("Refresh token to be sent it null");
    
    return false;
  }
  
  String jsonData;
  serializeJson(refreshTokenDoc, jsonData);

  Serial.print("serialized refresh token data to send");//@dev
  Serial.print(jsonData);

  String url = buildHomeAppUrl(HOMEAPP_REFRESH_TOKEN);
  Serial.print("refresh token url: "); //@DEV
  Serial.println(url);
  
  String tokens = sendHomeAppHttpsRequest(url, jsonData, false);

  if (tokens == "" || tokens == "null") {
    return false;
  }
  
  return saveTokensFromLogin(tokens);
}


bool saveTokensFromLogin(String payload) {  
  Serial.println("saving payload into tokens: ");

  DynamicJsonDocument responseTokens = getDeserializedJson(payload, 2048);
  Serial.println("token json"); // @DEV
  Serial.println(responseTokens["token"].as<String>());
  
  token = responseTokens["token"].as<String>();
  refreshToken = responseTokens["refreshToken"].as<String>();

  if (token == "null" || token == "" || refreshToken == "null" || refreshToken == "") {
    return false;
  }
  Serial.println("Token: "); //@DEBUG
  Serial.println(token);
  Serial.println("refreshToken");
  Serial.println(refreshToken);

  return true;
}

String buildIpAddressUpdateRequest() {
  Serial.println("Building IP update request");
  DynamicJsonDocument ipUpdateRequest(64);

  ipUpdateRequest["ipAddress"] = ipAddress;

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


//<!------------- DHT Functions --------------!>
bool setDhtValues() {
  Serial.println("Checking to see if dht values are set");
  if (!SPIFFS.exists("/dht.json")) {
    Serial.println("No dht json found");
    return false;
  }
  
  String dhtSensorSpiff = getSerializedSpiff("/dht.json");
  
  Serial.println("Dht SPIFF found");
  DynamicJsonDocument dhtDoc = getDeserializedJson(dhtSensorSpiff, 1024);

  dhtSensor.sensorCount = 0;
  for(int i = 0; i < DHTS_ASSINGED_TO_DEVICE; ++i) {
    String dhtSensorName = dhtDoc[i]["sensorName"];  
    int pinNumber = dhtDoc[i]["pinNumber"].as<int>();
    int readingInterval = dhtDoc[i]["readingInterval"].as<int>();

    if(
      dhtSensorName == "null"
      || dhtDoc[i]["sensorName"].isNull()
    ) {        
      if (i == 0) {
        Serial.println("Name check failed on first dht failed to set dht");    
        return false;           
      }
      Serial.println("Name check failed skipping dht this sensor");          
      continue;
    }

    if (readingInterval) {
      dhtSensor.interval[i] = readingInterval;  
    } else {
      dhtSensor.interval[i] = 6000;
    }

    strncpy(dhtSensor.sensorName[i], dhtDoc[i]["sensorName"], sizeof(dhtSensor.sensorName));  
    Serial.print("dht sensor name ");
    Serial.println(dhtSensor.sensorName[i]);      

    dhtSensor.pinNumber[i] = pinNumber;
    Serial.printf("Dht pin is: %d\n", dhtSensor.pinNumber[i]);

    dhtSensor.interval[i] = readingInterval;
    Serial.printf("Dht interval is: %d\n", dhtSensor.interval[i]);

    dhtSensors[i] = new DHT(pinNumber, DHTTYPE);
    dhtSensors[i]->begin();

    dhtSensor.sensorCount++;
    dhtSensor.activeSensor = true;
  }
    Serial.printf("Total amount of dht sensor found: %d\n", dhtSensor.sensorCount);
    
    Serial.println("marking DHT as active");
    dhtSensor.valuesAreSet = true;
    
    return true;
}



void takeDhtReadings() {
  Serial.println("Taking Dht readings");
  for (int i = 0; i < dhtSensor.sensorCount; ++i) {
    dhtSensor.tempReading[i] = dhtSensors[i]->readTemperature();
    dhtSensor.humidReading[i] = dhtSensors[i]->readHumidity();
    Serial.print("Temp is:");
    Serial.print(dhtSensor.tempReading[i]);
    Serial.println(" Celsius");
    Serial.print("Humidity: ");
    Serial.print(dhtSensor.humidReading[i]);
    Serial.println("%");      
  }
}

String buildDhtReadingSensorUpdateRequest(bool force = false) {
  Serial.println("Building dht request");  
  DynamicJsonDocument sensorUpdateRequest(1024);
  int currentTime = millis();
  Serial.printf("current time: %d\n", currentTime);
  int jsonPositionTracker = 0;
  for (int i = 0; i < dhtSensor.sensorCount; ++i) {
    Serial.printf("Dht sensor next reading at value is %d\n", dhtSensor.sendNextReadingAt[i]);  
    Serial.printf("next reading at minus the current time is %d milli seconds left to send data \n", (dhtSensor.sendNextReadingAt[i] - currentTime));
    if ((dhtSensor.sendNextReadingAt[i] - currentTime) < 0 || force == true) {          
      if (!isnan(dhtSensor.tempReading[i]) || !isnan(dhtSensor.humidReading[i])) {
        Serial.print("sensor name:");
        Serial.println(dhtSensor.sensorName[i]);
        sensorUpdateRequest["sensorData"][jsonPositionTracker]["sensorType"] = DHTNAME;
        sensorUpdateRequest["sensorData"][jsonPositionTracker]["sensorName"] = dhtSensor.sensorName[i];
        sensorUpdateRequest["sensorData"][jsonPositionTracker]["currentReadings"]["temperature"] = String(dhtSensor.tempReading[i]);
        sensorUpdateRequest["sensorData"][jsonPositionTracker]["currentReadings"]["humidity"] = String(dhtSensor.humidReading[i]);   
  
        dhtSensor.sendNextReadingAt[i] = currentTime + dhtSensor.interval[i];
        ++jsonPositionTracker;
      }
    }
  }
  
  String jsonData;
  serializeJson(sensorUpdateRequest, jsonData);
  Serial.print("Dht json data to send: ");
  Serial.println(jsonData);    

  return jsonData;  
}

bool sendDhtUpdateRequest(bool force = false) {
  Serial.println("building Dht readings request");
  String payload = buildDhtReadingSensorUpdateRequest(force);
  if (payload == "null") {
    Serial.println("Aborting request");
    return false;
  }
  String url = buildHomeAppUrl(HOME_APP_CURRENT_READING);

  String response = sendHomeAppHttpsRequest(url, payload, true);
  Serial.println("response");
  Serial.println(response);
  return true;
}

/////<!---END OF NETWORK METHODS---!>//////



//<------- Dallas Sensor Functions -------------->
bool setDallasValues() {
  Serial.println("Checking to see if dallas values are set");
  if (!SPIFFS.exists("/dallas.json")) {
    Serial.println("No dallas spiff found no longer setting dallas values");
    return false;
  }
  
  String dallasSensorSpiff = getSerializedSpiff("/dallas.json");
  
  if (!dallasSensorSpiff) {
    return false;
  }
  
  Serial.println("Deserialzing dallas json");
  DynamicJsonDocument dallasDoc = getDeserializedJson(dallasSensorSpiff, 1256);

  Serial.printf("Max dallas sensors allowed %d\n", MAX_DALLAS_SENSORS);
  for (int i = 0; i < MAX_DALLAS_SENSORS; ++i) {
    if (dallasDoc[i]["sensorName"].isNull()) {     
      if (i == 0) {
        Serial.println("Name check failed on first Dallas failed to set");    
        return false;
      }
      Serial.printf("Name check failed on %d Dallas sensor \n", i);
      continue;
    }
    Serial.printf("Name check passed name is %s", dallasDoc[i]["sensorName"]);
    strncpy(dallasTempData.sensorName[i], dallasDoc[i]["sensorName"], sizeof(dallasTempData.sensorName[i]));

    int readingInterval = dallasDoc[i]["readingInterval"].as<int>();
    if (readingInterval) {
     dallasTempData.interval[i] = readingInterval;  
    } else {
     dallasTempData.interval[i] = 6000;
    }
    
    Serial.printf("bus temp temperature send interval: %d\n", dallasTempData.interval[i]);
  }

  int dallasSensorPinNumber = dallasDoc[0]["pinNumber"].as<int>();
  dallasTempData.pinNumber = dallasSensorPinNumber;

  Serial.printf("Dallas temp pin number is %d\n", dallasTempData.pinNumber);

  dallasTempData.valuesAreSet = true;  
  
  return true;
}

bool findDallasSensor() {
  bool sensorSuccess = false;
  for (uint8_t pin = dallasTempData.pinNumber; pin <= dallasTempData.pinNumber ; pin++) {
    Serial.print("pin ");
    Serial.println(pin);
    sensorSuccess = searchPinForOneWire(pin);
    if(sensorSuccess == true) {
      Serial.println("Dallas sensor found creating reference");
      DallasTemperature sensors(&oneWire);
      return true;
    }
  }      
  Serial.println("Dallas sensor not found");
  return false;
}

uint8_t searchPinForOneWire(int pin) {
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
    Serial.print("Number devices found: ");
    dallasTempData.sensorCount = count;
    Serial.println(count);
    oneWire = (pin);

    return true;
  }

  return false;
}

void takeDallasTempReadings() {
  Serial.print("Requesting Bus temperatures...");
  sensors.requestTemperatures(); // Send the command to get temperatures

  for(int i = 0; i < dallasTempData.sensorCount; i++) {
    float tempReading = sensors.getTempCByIndex(i);
    Serial.print("Temp number:");
    Serial.print(i);
    Serial.print(" Tempreture is:");
    Serial.println(tempReading); 
    dallasTempData.tempReading[i] = tempReading;
 }
}

String buildDallasReadingSensorUpdateRequest(bool force = false) {
  Serial.println("Building Dallas request");
  DynamicJsonDocument sensorUpdateRequest(1024);

  int currentTime = millis();
  Serial.printf("current time: %d\n", currentTime);
  int jsonArrayIndex = 0;
  for (int i = 0; i < dallasTempData.sensorCount; ++i) {
    Serial.printf("Dallas sensor next reading at value is %d\n", dallasTempData.sendNextReadingAt[i]);  
    Serial.printf("next reading at minus the current time is %d milli seconds left to send data \n", (dallasTempData.sendNextReadingAt[i] - currentTime));
    if ((dallasTempData.sendNextReadingAt[i] - currentTime) < 0 || force == true) {
      if (dallasTempData.tempReading[i] != -127 || !isnan(dallasTempData.tempReading[i])) {
        Serial.print("sensor name:");
        Serial.println(dallasTempData.sensorName[i]);
        sensorUpdateRequest["sensorData"][jsonArrayIndex]["sensorType"] = DALLASNNAME;
        sensorUpdateRequest["sensorData"][jsonArrayIndex]["sensorName"] = dallasTempData.sensorName[i];
        sensorUpdateRequest["sensorData"][jsonArrayIndex]["currentReadings"]["temperature"] = String(dallasTempData.tempReading[i]);
        Serial.print("temp reading:");
        Serial.println(String(dallasTempData.tempReading[i]));

        dallasTempData.sendNextReadingAt[i] = currentTime + dallasTempData.interval[i];
        ++jsonArrayIndex;
      } 
    }    
  }
  String jsonData;
  serializeJson(sensorUpdateRequest, jsonData);
  Serial.print("Dallas json data to send: ");
  Serial.println(jsonData);

  return jsonData;
}

bool sendDallasUpdateRequest(bool force = false) {
  Serial.println("Begining to send Dallas request");
  String payload = buildDallasReadingSensorUpdateRequest(force);
  if (payload == "null") {
    Serial.println("Aborting Dallas request payload empty");
    return false;
  }
  String url = buildHomeAppUrl(HOME_APP_CURRENT_READING);
  String response = sendHomeAppHttpsRequest(url, payload, true);
  Serial.println("response");
  Serial.println(response);
  
  return true;
}


//@DEV Dummie reading provider
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

//RelayFunctions
String buildRelaySensorUpdateRequest(bool force = false) {
  Serial.println("Building Relay request");
  DynamicJsonDocument sensorUpdateRequest(1024);

  int currentTime = millis();
  Serial.printf("current time: %d\n", currentTime);
  int jsonArrayIndex = 0;

  for (int i = 0; i < relayData.sensorCount; ++i) {
    Serial.printf("next reading at minus the current time is %d milli seconds left to send data \n", (relayData.sendNextReadingAt[i] - currentTime));
    if ((relayData.sendNextReadingAt[i] - currentTime) < 0 || force == true) {      
      Serial.print("sensor name:");
      Serial.println(relayData.sensorName[i]);
      sensorUpdateRequest["sensorData"][jsonArrayIndex]["sensorType"] = RELAYNAME;
      sensorUpdateRequest["sensorData"][jsonArrayIndex]["sensorName"] = relayData.sensorName[i];
      sensorUpdateRequest["sensorData"][jsonArrayIndex]["currentReadings"]["relay"] = relayData.currentReading[i];
      Serial.print("relay reading:");
      Serial.println(relayData.currentReading[i]);

      relayData.sendNextReadingAt[i] = currentTime + relayData.interval[i];
      ++jsonArrayIndex;       
    }        
  }

  String jsonData;
  serializeJson(sensorUpdateRequest, jsonData);
  Serial.print("Relay json data to send: ");
  Serial.println(jsonData);

  return jsonData;
}

bool sendRelayUpdateRequest(bool force = false) {
  Serial.println("Begining to send Relay request");
  String payload = buildRelaySensorUpdateRequest(force);
  if (payload == "null") {
    Serial.println("Aborting Dallas request payload empty");
    return false;
  }
  String url = buildHomeAppUrl(HOME_APP_CURRENT_READING);
  String response = sendHomeAppHttpsRequest(url, payload, true);
  Serial.println("response");
  Serial.println(response);
  
  return true;  
}


// Web Functions
void resetDevice() {
  SPIFFS.remove("/device.json");
  SPIFFS.remove("/wifi.json");

  SPIFFS.remove("/dallas.json");
  SPIFFS.remove("/dht.json");
  SPIFFS.remove("/relay.json");
  server.send(200, "application/json", "{\"status\":\"device reset\"}");
  ESP.restart;
}

void restartDevice() {
  server.send(200, "application/json", "{\"status\":\"ok\"}");
  ESP.restart;
}

void sendAllActiveSensorData(bool force = false) {
  if (dallasTempData.activeSensor == true) {
    sendDallasUpdateRequest(force);  
  }
  if (dhtSensor.activeSensor == true) {
    sendDhtUpdateRequest(force);
  }     
  if (relayData.activeSensor == true) {
    sendRelayUpdateRequest(force);
  }
}

void sendAllSensorData() {
  server.send(200, "application/json", "{\"pong\"}");
  sendAllActiveSensorData(true);
}

// Common Functions
DynamicJsonDocument getDeserializedJson(String serializedJson, int jsonBuffSize) {
  Serial.println("serialized json to deserialize: ");
  Serial.println(serializedJson);
  Serial.print("Buffer size: ");
  Serial.println(jsonBuffSize);

  Serial.println("Deseriazing Json");
  char jsonData[jsonBuffSize];
  strcpy(jsonData, serializedJson.c_str());

  DynamicJsonDocument deserializedJson(jsonBuffSize);
  DeserializationError error = deserializeJson(deserializedJson, jsonData);
  if (error) {
    Serial.println("deserialization error");
  }

  Serial.println("deserialization success");
  return deserializedJson;
}

//<---------- SPIFF MEthods ------------------>
String getSerializedSpiff(String spiff) {
  if (!SPIFFS.exists(spiff)) {
    Serial.printf("No spiff exists for %s", spiff);
  }
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

void checkSensorSPIFFSExist() {
  if (SPIFFS.exists("/dallas.json")) {
    dallasTempData.settingsJsonExists = true;
  }
  if (SPIFFS.exists("/dht.json")) {
    dhtSensor.settingsJsonExists = true;
  }
  if (SPIFFS.exists("/relay.json")) {
    relayData.settingsJsonExists = true;
  }
}

//<---------- END OF SPIFF MEthods ------------------>

void handleSwitchSensor() {
  Serial.println("Handling SWITCH");
  String sensorSwitchRequest = server.arg("plain");

  Serial.println("Deserialzing Request");
  DynamicJsonDocument sensorSwitchJson = getDeserializedJson(sensorSwitchRequest, 512);

  if (!sensorSwitchJson["sensorName"].isNull() && !sensorSwitchJson["pinNumber"].isNull()) {
    String sensorName = sensorSwitchJson["sensorName"].as<String>();
    int pinNumber = sensorSwitchJson["pinNumber"].as<int>();
    bool requestedReading = sensorSwitchJson["requestedReading"].as<boolean>();

    bool passed = false;
    for(int i = 0; i < MAX_RELAYS; ++i) {
      if (relayData.pinNumber[i] == pinNumber) {
        passed = true;
      }
    }
    if (passed == false) {
      server.send(401, "application/json", "{\"status\":\"pin is not configured to set set\"}");   
    }
    
    Serial.print("Sensor Name: ");
    Serial.println(sensorName);
    Serial.print("pinNumber: ");
    Serial.println(pinNumber);
    Serial.print("requested reading: ");
    Serial.println(requestedReading);
    if (requestedReading == 0) {
      Serial.println("Going Low");
      digitalWrite(pinNumber, LOW);        
    } else {
      Serial.println("Going High");
      digitalWrite(pinNumber, HIGH);  
    }    
    server.send(200, "application/json", "{\"status\":\"completed\"}"); 
  } else {
    server.send(500, "application/json", "{\"status\":\"failed to switch sensor request error\"}"); 
  }
}

void setup() {
  Serial.begin(DEVICE_SERIAL);
  Serial.println("Searial started");

  Serial.print("Starting web servers...");
  server.on("/",[](){server.send_P(200,"text/html", webpage);});
  server.on("/settings", HTTP_POST, handleSettingsUpdate);

  server.on("/switch", HTTP_POST, handleSwitchSensor);
  server.on("/ping", HTTP_GET, sendAllSensorData);
  
  server.on("/reset-device", HTTP_GET, resetDevice);
  server.on("/restart-device", HTTP_GET, restartDevice);
  server.begin();
  Serial.println("Servers Begun");

  delay(2000);
  Serial.println("SPIFFS starting...");
  if (!SPIFFS.begin()) {
    Serial.println("SPIFFS failed to start");
    ESP.restart();
  }
  Serial.println("...SPIFFS started");
  
  Serial.println("Begining device setup");

  for (int i = 0; i <= 4; i++) {
    pinMode(ledPins[i], OUTPUT); 
  }
  
  digitalWrite(WIFI_OFF_LED_PIN, HIGH);
  digitalWrite(DEVICE_ON_LED_PIN, HIGH);
  
  if (setupNetworkConnection()) {
    Serial.print("Getting external IP... ");
    getExternalIP();
    deviceLoggedIn = deviceLogin();  
  }
  
  checkSensorSPIFFSExist();
  
  Serial.println("End of Setup");
}



void loop() {
  server.handleClient();

  if (dallasTempData.activeSensor == true) {
    takeDallasTempReadings();
  }
  if (dhtSensor.activeSensor == true) {
    takeDhtReadings();
  }

  if(WiFi.status() == WL_CONNECTED) {
    if (deviceLoggedIn == true) {
      sendAllActiveSensorData();
    } else {
      Serial.println("Device not loged in attempting to refresh token");
      deviceLoggedIn = deviceLogin();
      if (deviceLoggedIn == false && deviceRegistered == false) {
        registerDevice();
      }
    }
    if (publicIpAddress == NULL || publicIpAddress == "" || !publicIpAddress) {
      getExternalIP();
    }
  } else {    
    handleWifiReconnectionAttempt();  
  }


  if (dallasTempData.settingsJsonExists == true && dallasTempData.valuesAreSet == false) {
    setDallasValues();
    if (dallasTempData.valuesAreSet == true) {
      bool sensorActive = findDallasSensor();
      if (sensorActive == true) {
        dallasTempData.activeSensor = true;
        Serial.println("Starting Dallas sensor");
        sensors.begin();  
        delay(500);
      }
    } 
  }

  if (dhtSensor.settingsJsonExists == true && dhtSensor.valuesAreSet == false) {
    setDhtValues();
    if (dhtSensor.valuesAreSet == true) {
      Serial.println("Starting Dht");
      dhtSensor.activeSensor = true;
      delay(500);
    }     
  }

  if (relayData.settingsJsonExists == true && relayData.valuesAreSet == false) {
    setRelayValues();
    Serial.println("Starting Relay");
    relayData.activeSensor = true;
  }
//  Serial.println("Loop finished");
}
