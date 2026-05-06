#include <Arduino.h>
#include <Adafruit_Sensor.h>
#include <FS.h>

#include <ESP8266WiFi.h>
#include <WiFiClient.h>
#include <ESP8266HTTPClient.h>
#include <ESP8266WebServer.h>

#include <ArduinoJson.h>

#include <OneWire.h>
#include <DallasTemperature.h>

#include <DHT.h>

#include <Wire.h>

#include "Adafruit_SHT31.h"

#define MICRO_ESP_SERIAL 115200
#define NODE_MCU_SERIAL 9600

// ESP8266-01
//#define DEVICE_SERIAL MICRO_ESP_SERIAL
#define DEVICE_SERIAL NODE_MCU_SERIAL


//Web bits
// Test
//#define HOMEAPP_HOST "https://192.168.1.158"
//#define HOMEAPP_HOST "https://192.168.1.230"
// Prod
#define HOMEAPP_HOST "https://klh19901017.asuscomm.com"
#define HOMEAPP_URL "HomeApp"
#define HOMEAPP_PORT "8101"

#define HOMEAPP_LOGIN "api/device/login_check"
#define HOMEAPP_REFRESH_TOKEN "api/device/token/refresh"
#define HOMEAPP_IP_UPDATE "api/device/ipupdate"
#define HOME_APP_CURRENT_READING "api/device/esp/update/current-reading"
#define HOME_APP_REGISTER_DEVICE "api/device/register"

#define EXTERNAL_IP_URL "http://api.ipify.org/?format=json"

const char fingerprint[] PROGMEM = "60ee151bee994d6ca826a69abce1e724173721ca";

char ipAddress[16];
char publicIpAddress[16] = "";
int publicIpAddressRequestAttempts = 0;
char token[512];
char refreshToken[512];
bool deviceRegistered = false;

unsigned long wifiRetryTimer = 0;
unsigned long ipAddressNextUpdate = 0;

bool deviceLoggedIn = false;

// Access ponint network bits
#define ACCESSPOINT_SSID "HomeApp-D-A-D-AP"
#define ACCESSPOINT_PASSWORD "HomeApp1234"

ESP8266WebServer server;
IPAddress local_ip(192, 168, 1, 254);
IPAddress gateway(192, 168, 1, 1);
IPAddress netmask(255, 255, 255, 0);

//LEDS
#define DEVICE_ON_LED_PIN 0
#define WIFI_OFF_LED_PIN 0 

#define DEVICE_LED_PIN_SANCTIONED 2

int ledPins[DEVICE_LED_PIN_SANCTIONED] = {WIFI_OFF_LED_PIN, DEVICE_ON_LED_PIN};

#define SENSOR_NAME_MAX_LENGTH 25

// DHT
#define DHTNAME "Dht"
#define DHTS_ASSINGED_TO_DEVICE 1
#define DHTTYPE DHT22
DHT* dhtSensors[DHTS_ASSINGED_TO_DEVICE];

struct DhtSensor {
  char sensorName[DHTS_ASSINGED_TO_DEVICE][SENSOR_NAME_MAX_LENGTH];
//  char*[DHTS_ASSINGED_TO_DEVICE] sensorName;
  float tempReading[DHTS_ASSINGED_TO_DEVICE];
  float humidReading[DHTS_ASSINGED_TO_DEVICE];
  unsigned long interval[DHTS_ASSINGED_TO_DEVICE];
  unsigned long sendNextReadingAt[DHTS_ASSINGED_TO_DEVICE];
  int pinNumber[DHTS_ASSINGED_TO_DEVICE];
  int sensorCount = 0;
  bool activeSensor = false;
  bool valuesAreSet = false;
  bool settingsJsonExists = false;
};
DhtSensor dhtSensor;

// Dallas
#define DALLASNNAME "Dallas"
#define MAX_DALLAS_SENSORS 3

OneWire oneWire(0);
DallasTemperature sensors(&oneWire);

//Dallas Bus Temperature
struct DallasTempData {
  char sensorName[MAX_DALLAS_SENSORS][SENSOR_NAME_MAX_LENGTH];
//  char sensorName;
  float tempReading[MAX_DALLAS_SENSORS];
  unsigned long sendNextReadingAt[MAX_DALLAS_SENSORS];
  unsigned long interval[MAX_DALLAS_SENSORS];
  int sensorCount = 0;
  int pinNumber;
  bool activeSensor = false;
  bool valuesAreSet = false;
  bool settingsJsonExists = false;
};
DallasTempData dallasTempData;

//Relay
#define RELAYNAME "GenericRelay"
#define MAX_RELAYS 8
struct RelayData {
  char sensorName[MAX_RELAYS][SENSOR_NAME_MAX_LENGTH];
//  char*[MAX_RELAYS] sensorName;
  bool currentReading[MAX_RELAYS];
  int pinNumber[MAX_RELAYS];
  unsigned long sendNextReadingAt[MAX_RELAYS];
  unsigned long interval[MAX_RELAYS];
  int sensorCount = 0;
  bool activeSensor = false;
  bool valuesAreSet = false;
  bool settingsJsonExists = false;
};
RelayData relayData;

#define LDRNAME "Ldr"
#define MAX_LDRS 1

struct LdrData {
  char sensorName[MAX_LDRS][SENSOR_NAME_MAX_LENGTH];
//  char*[MAX_LDRS] sensorName;
  int currentReading[MAX_LDRS];
  unsigned long sendNextReadingAt[MAX_LDRS];
  unsigned long interval[MAX_LDRS];
  int pinNumber[MAX_LDRS];
  int sensorCount = 0;
  bool activeSensor = false;
  bool valuesAreSet = false;
  bool settingsJsonExists = false;
};
LdrData ldrData;

#define SHTNAME "Sht"
#define MAX_SHTS 1
Adafruit_SHT31* sht31[MAX_SHTS];

struct ShtData {
  char sensorName[MAX_SHTS][SENSOR_NAME_MAX_LENGTH];
  float tempReading[MAX_SHTS];
  float humidReading[MAX_SHTS];
  unsigned long sendNextReadingAt[MAX_SHTS];
  unsigned long interval[MAX_SHTS];
  int sensorCount = 0;
  int pinNumber[MAX_SHTS];
  bool activeSensor = false;
  bool valuesAreSet = false;
  bool settingsJsonExists = false;  
};
ShtData shtData;

const char* deviceSpiffs[] = {"dallas", "dht", "relay", "ldr", "sht"};



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
    
  JsonDocument wifiDoc = getDeserializedJson(wifiCredentials, 1024);

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

  unsigned long timeout = millis() + 35000;
  Serial.printf("Connecting to wifi with a %lu millisecond timeout\n", timeout);
  while(WiFi.status() != WL_CONNECTED){
    unsigned long currentTime = millis();
    // leave serial in when taken out exceptions get throws, whos knows
    Serial.print(".");
    if (currentTime > timeout) {
      Serial.println("Failed to connect to wifi network");
      break;
    }
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("Wifi connection made");
    WiFi.printDiag(Serial);
    Serial.print("Network IP Address: ");
    Serial.println(WiFi.localIP());
    Serial.println("saved ip address");
    strncpy(ipAddress, ipToString(WiFi.localIP()).c_str(), sizeof(ipAddress) - 1);
    Serial.println(ipAddress);
    digitalWrite(WIFI_OFF_LED_PIN, LOW);
    return true;
  }

  Serial.print("wasnt able to connect");
  digitalWrite(WIFI_OFF_LED_PIN, HIGH);
  
  return false;
}

void handleWifiReconnectionAttempt() {
  if (!SPIFFS.exists("/wifi.json")) {
    return;
  }
  unsigned long currentTime = millis();
  if (currentTime >= wifiRetryTimer) {
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
  JsonDocument doc = getDeserializedJson(data, 2568);

  if (doc.isNull()) {
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
    if (!saveWifiCredentials(doc["wifi"].as<JsonObject>())) {
      Serial.println("No wifi crednetials saved");
      wifiSuccess = false;
    }      
  }

  if (!doc["sensorData"].isNull()) {
    Serial.println("Sensor data found in json attempting to save data");
    sensorDataChanged = true;
    if (!saveSensorDataToSpiff(doc["sensorData"].as<JsonVariant>())) {
      Serial.println("failed to save sensor data spiffs");
      sensorDataSuccess = false;
    }
  }

  if (!doc["deviceCredentials"].isNull()) {
    Serial.println("Device credentials found in json attempting to save data");
    deviceCredenatialsChanged = true;
    if (!saveDeviceUserSettings(doc["deviceCredentials"].as<JsonObject>())) {
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
  }  

  if (sensorDataChanged == true) {
    checkSensorSPIFFSExist();
  }
}

bool saveWifiCredentials(JsonObject doc) {
   if (doc["ssid"].isNull()) {
    Serial.println("Security is not trying to be set, empty values");
    return false;
  }
  
  const char* ssid = doc["ssid"].as<const char*>();
  const char* password = doc["password"].as<const char*>();
  if (SPIFFS.exists("/wifi.json")) {
    Serial.println("wifi json was found removing current json");
    SPIFFS.remove("/wifi.json");
  }
  Serial.println("Security values are being set");
  JsonDocument wifiDoc;
  
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

bool saveDeviceUserSettings(JsonObject doc) {
  Serial.println("Setting device user setting now with doc:");
  Serial.println(doc["username"].as<String>());
  if (doc["username"].isNull()) {
    Serial.println("No device user name sent in payload, not setting any values and using defaults");
    return true;
  }

  if (SPIFFS.exists("/device.json")) {
    Serial.println("device settings json was found removing current json");
    SPIFFS.remove("/device.json");
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

// Wrapper for saving sensor data for each sensor in different SPIFFS
bool saveSensorDataToSpiff(JsonVariant doc) {
  if (!doc["dallas"].isNull()) {
    if (!saveDallasSensorData(doc["dallas"])) {   
      Serial.println("failed to set Dallas Spiff");
    }  
  }

  if (!doc["dht"].isNull()) {
    if (!saveDhtSensorData(doc["dht"])) {
      Serial.println("No Dht data");
    }  
  }

  if (!doc["relay"].isNull()) {
    if (!saveRelaySensorData(doc["relay"])) {
      Serial.println("No relay data");
    }  
  }

  if (!doc["ldr"].isNull()) {
    if (!saveLdrSensorData(doc["ldr"])) {
      Serial.println("No LDR data");
    }  
  }

  if (!doc["sht"].isNull()) {
    if (!saveShtSensorData(doc["sht"])) {
      Serial.println("No SHT data");
    }    
  }
  
  return true;
}

bool saveJsonToSpiff(const char* path, JsonVariant data) {
  if (SPIFFS.exists(path)) SPIFFS.remove(path);
  File f = SPIFFS.open(path, "w");
  if (!serializeJson(data, f)) {
    f.close();
    Serial.printf("Failed to serialize to %s\n", path);
    return false;
  }
  f.close();
  Serial.printf("Saved %s\n", path);
  return true;
}

bool saveLdrSensorData(JsonVariant ldrDoc) {
  if (ldrDoc[0]["sensorName"].isNull()) {
    Serial.println("LDR: sensor name not set");
    return false;
  }
  ldrData.valuesAreSet = false;
  return saveJsonToSpiff("/ldr.json", ldrDoc);
}

bool saveRelaySensorData(JsonVariant relayDoc) {
  if (relayDoc[0]["sensorName"].isNull()) {
    Serial.println("Relay: sensor name not set");
    return false;
  }
  relayData.valuesAreSet = false;
  return saveJsonToSpiff("/relay.json", relayDoc);
}

bool setRelayValues() {
  Serial.println("Attempting to set relay values");
  if (!SPIFFS.exists("/relay.json")) {
    Serial.print("No relay json found");
    return false;
  }

  String relaySensorData = getSerializedSpiff("/relay.json");

  JsonDocument relayDoc = getDeserializedJson(relaySensorData, 1024);

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

    strncpy(relayData.sensorName[i], relayDoc[i]["sensorName"].as<const char*>(), sizeof(relayData.sensorName[i]));  
    Serial.print("relay sensor name: ");
    Serial.println(relayData.sensorName[i]);      

    relayData.pinNumber[i] = pinNumber;
    Serial.printf("relay pin is: %d\n", relayData.pinNumber[i]);
    pinMode(pinNumber, OUTPUT);

    relayData.interval[i] = readingInterval;
    Serial.printf("relay interval is: %d\n", relayData.interval[i]);
    
    relayData.sensorCount++;
    Serial.print("Is relay set and active: ");
    relayData.valuesAreSet = true;
    Serial.println(relayData.activeSensor);
  }

  return true;
}

bool saveDhtSensorData(JsonVariant dhtData) {
  if (dhtData[0]["sensorName"].isNull()) {
    Serial.println("DHT: sensor name not set");
    return false;
  }
  dhtSensor.valuesAreSet = false;
  return saveJsonToSpiff("/dht.json", dhtData);
}

bool saveDallasSensorData(JsonVariant dallasData) {
  if (dallasData[0]["sensorName"].isNull()) {
    Serial.println("Dallas: sensor name not set");
    return false;
  }
  dallasTempData.valuesAreSet = false;
  return saveJsonToSpiff("/dallas.json", dallasData);
}

bool saveShtSensorData(JsonVariant shtDoc) {
  if (shtDoc[0].isNull()) {
    Serial.println("SHT: sensor data not set");
    return false;
  }
  shtData.valuesAreSet = false;
  return saveJsonToSpiff("/sht.json", shtDoc);
}
// End of saving sensor data


String ipToString(IPAddress ip){
  String stringIP = "";
  for (int i=0; i < 4; i++) {
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
  Serial.printf(
    "[HTTP] begin connecting to: %s \n",
    EXTERNAL_IP_URL
  );
  
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
        JsonDocument externalIP = getDeserializedJson(payload, 512);

        const char* extIp = externalIP["ip"].as<const char*>();
        strncpy(publicIpAddress, extIp ? extIp : "", sizeof(publicIpAddress) - 1);
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
  Serial.print("JSON data to send: ");
  Serial.println(jsonData);
  Serial.println("Building Https conn ection");
  std::unique_ptr<BearSSL::WiFiClientSecure>client(new BearSSL::WiFiClientSecure);
  client->setFingerprint(fingerprint);
  HTTPClient https;
  Serial.print("[HTTPS] begin connecting to: ");
  Serial.println(url);
  
  https.begin(*client, url);
  https.addHeader("Content-Type", "application/json");

  if (addAuthHeader == true) {
    Serial.println("adding auth header with token");
    char authHeader[520];
    snprintf(authHeader, sizeof(authHeader), "Bearer %s", token);
    https.addHeader("Authorization", authHeader);
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

  JsonDocument registerDoc;
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

  JsonDocument loginDoc = getDeserializedJson(deviceData, 512);

  if (loginDoc["username"].isNull()) {
    Serial.println("device json username is empty failing login");
    return false;
  }
  
  loginDoc["ipAddress"] = ipAddress;

  if (publicIpAddress[0] != '\0') {
    Serial.printf("addinng external ip to request: %s \n", publicIpAddress);
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
  if (refreshToken[0] == '\0') {
    Serial.println("refresh token was empty not attempting to refresh");
    return false;
  }

  JsonDocument refreshTokenDoc;
  refreshTokenDoc["refreshToken"] = refreshToken;

  if (refreshTokenDoc["refreshToken"].isNull()) {
    Serial.println("Refresh token to be sent it null");
    
    return false;
  }
  
  String jsonData;
  serializeJson(refreshTokenDoc, jsonData);

  Serial.printf("serialized refresh token data to send: %s \n", jsonData);//@dev

  String url = buildHomeAppUrl(HOMEAPP_REFRESH_TOKEN);
  Serial.printf("refresh token url: %s \n", url); //@DEV
  
  String tokens = sendHomeAppHttpsRequest(url, jsonData, false);

  if (tokens == "" || tokens == "null") {
    return false;
  }
  
  return saveTokensFromLogin(tokens);
}


bool saveTokensFromLogin(String payload) {  
  Serial.println("saving payload into tokens: ");

  JsonDocument responseTokens = getDeserializedJson(payload, 2048);
  Serial.println("token json"); // @DEV
  Serial.println(responseTokens["token"].as<const char*>());
  
  const char* tkn = responseTokens["token"].as<const char*>();
  const char* rtkn = responseTokens["refreshToken"].as<const char*>();
  strncpy(token, tkn ? tkn : "", sizeof(token) - 1);
  strncpy(refreshToken, rtkn ? rtkn : "", sizeof(refreshToken) - 1);

  if (strcmp(token, "null") == 0 || token[0] == '\0' || strcmp(refreshToken, "null") == 0 || refreshToken[0] == '\0') {
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
  JsonDocument ipUpdateRequest;

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


bool setLdrValues() {
  Serial.println("Checking to see if ldr values are set");
  if (!SPIFFS.exists("/ldr.json")) {
    Serial.println("No ldr json found");
    return false;
  }
  
  String ldrSensorSpiff = getSerializedSpiff("/ldr.json");
  
  Serial.println("LDR SPIFF found");
  JsonDocument ldrDoc = getDeserializedJson(ldrSensorSpiff, 1024);

  ldrData.sensorCount = 0;
  for(int i = 0; i < MAX_LDRS; ++i) {
    String ldrSensorName = ldrDoc[i]["sensorName"];  
    int pinNumber = ldrDoc[i]["pinNumber"].as<int>();   
    int readingInterval = ldrDoc[i]["readingInterval"].as<int>();

    if(
      ldrSensorName == "null"
      || ldrDoc[i]["sensorName"].isNull()
    ) {        
      if (i == 0) {
        Serial.println("Name check failed on first ldr failed to set ldr");    
        return false;           
      }
      Serial.println("Name check failed skipping ldr this sensor");          
      continue;
    }

    if (readingInterval) {
      ldrData.interval[i] = readingInterval;  
    } else {
      ldrData.interval[i] = 6000;
    }

    strncpy(ldrData.sensorName[i], ldrDoc[i]["sensorName"].as<const char*>(), sizeof(ldrData.sensorName[i]));
    Serial.print("ldr sensor name ");
    Serial.println(ldrData.sensorName[i]);      

    ldrData.pinNumber[i] = pinNumber;
    Serial.printf("LDR pin is: %d\n", ldrData.pinNumber[i]);

    ldrData.interval[i] = readingInterval;
    Serial.printf("LDR interval is: %d\n", ldrData.interval[i]);   

    ++ldrData.sensorCount;
    ldrData.activeSensor = true;
  }
    Serial.printf("Total amount of ldr sensor found: %d\n", ldrData.sensorCount);
    
    Serial.println("marking LDR as active");
    ldrData.valuesAreSet = true;
    
    return true;
}

bool setShtValues() {
  Serial.println("Checking to see if sht values are set");
  if (!SPIFFS.exists("/sht.json")) {
    Serial.println("No sht json found");
    return false;
  }
  
  String shtSensorSpiff = getSerializedSpiff("/sht.json");
  
  Serial.println("Sht SPIFF found");
  JsonDocument shtDoc = getDeserializedJson(shtSensorSpiff, 1024);

  shtData.sensorCount = 0;
  for(int i = 0; i < MAX_SHTS; ++i) {
    String shtSensorName = shtDoc[i]["sensorName"];  
    int pinNumber = shtDoc[i]["pinNumber"].as<int>();
    int readingInterval = shtDoc[i]["readingInterval"].as<int>();

    if(
      shtSensorName == "null"
      || shtDoc[i]["sensorName"].isNull()
    ) {        
      if (i == 0) {
        Serial.println("Name check failed on first sht failed to set dht");    
        return false;           
      }
      Serial.println("Name check failed skipping sht this sensor");          
      continue;
    }

    if (readingInterval) {
      shtData.interval[i] = readingInterval;  
    } else {
      shtData.interval[i] = 6000;
    }

    strncpy(shtData.sensorName[i], shtDoc[i]["sensorName"], sizeof(shtData.sensorName[i]));
    Serial.print("sht sensor name ");
    Serial.println(shtData.sensorName[i]);      

    shtData.pinNumber[i] = pinNumber;
    Serial.printf("Sht pin is: %d\n", shtData.pinNumber[i]);

    shtData.interval[i] = readingInterval;
    Serial.printf("Sht interval is: %d\n", shtData.interval[i]);

    //Wire.begin(D1, D2);
    Wire.begin(5, 4);
    
    sht31[i] = new Adafruit_SHT31();
    int timeout = millis() + 35000;
    
    if (!sht31[i]->begin(0x44)) {
      Serial.println("Couldn't find SHT31");
//      delay(1);
//      int currentTime = millis();
//      if (timeout <= currentTime) {
//        break;
//      }
    } else {
      shtData.activeSensor = true;  
      shtData.sensorCount++;
      shtData.activeSensor = true;
      Serial.println("marking SHT as active");
      shtData.valuesAreSet = true;
    }
  }
    Serial.printf("Total amount of sht sensor found: %d\n", shtData.sensorCount);  
    if (shtData.sensorCount > 0 ) {
      return true;  
    }

    return false;
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
  JsonDocument dhtDoc = getDeserializedJson(dhtSensorSpiff, 1024);
//  deserializeJson(dhtDoc, dhtSensorSpiff);

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

    
    strncpy(dhtSensor.sensorName[i], dhtDoc[i]["sensorName"].as<const char*>(), sizeof(dhtSensor.sensorName));  
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

String buildLdrReadingsSensorUpdateRequest(bool force = false) {
  Serial.println("Building Ldr request");  
  JsonDocument sensorUpdateRequest;

  unsigned long currentTime = millis();
  int jsonPositionTracker = 0;
  for (int i = 0; i < ldrData.sensorCount; ++i) {
    Serial.printf("next reading due in %lu ms\n", currentTime >= ldrData.sendNextReadingAt[i] ? 0UL : ldrData.sendNextReadingAt[i] - currentTime);
    if (currentTime >= ldrData.sendNextReadingAt[i] || force == true) {
      ldrData.currentReading[i] = analogRead(ldrData.pinNumber[i]);
      Serial.println("LDR reading is: ");
      Serial.print(ldrData.currentReading[i]);
      if (!isnan(ldrData.currentReading[i])) {
        Serial.print("sensor name:");
        Serial.println(ldrData.sensorName[i]);
        sensorUpdateRequest["sensorData"][jsonPositionTracker]["sensorType"] = LDRNAME;
        sensorUpdateRequest["sensorData"][jsonPositionTracker]["sensorName"] = ldrData.sensorName[i];
        sensorUpdateRequest["sensorData"][jsonPositionTracker]["currentReadings"]["analog"] = String(ldrData.currentReading[i]);
  
        ldrData.sendNextReadingAt[i] = currentTime + ldrData.interval[i];
        ++jsonPositionTracker;
      }
    }
  }
  
  String jsonData;
  serializeJson(sensorUpdateRequest, jsonData);
  Serial.print("LDR json data to send: ");
  Serial.println(jsonData);    

  return jsonData;  
}

String buildDhtReadingSensorUpdateRequest(bool force = false) {
  Serial.println("Building dht request");  
  JsonDocument sensorUpdateRequest;
  unsigned long currentTime = millis();
  int jsonPositionTracker = 0;
  for (int i = 0; i < dhtSensor.sensorCount; ++i) {
    Serial.printf("next reading due in %lu ms\n", currentTime >= dhtSensor.sendNextReadingAt[i] ? 0UL : dhtSensor.sendNextReadingAt[i] - currentTime);
    if (currentTime >= dhtSensor.sendNextReadingAt[i] || force == true) {
      dhtSensor.tempReading[i] = dhtSensors[i]->readTemperature();
      dhtSensor.humidReading[i] = dhtSensors[i]->readHumidity();
      Serial.print("Temp is:");
      Serial.print(dhtSensor.tempReading[i]);
      Serial.println(" Celsius");
      Serial.print("Humidity: ");
      Serial.print(dhtSensor.humidReading[i]);
      Serial.println("%");     
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

bool sendSensorUpdateRequest(const String& payload, const char* label) {
  if (payload == "null" || payload.length() == 0) {
    Serial.printf("Aborting %s request, payload empty\n", label);
    return false;
  }
  String url = buildHomeAppUrl(HOME_APP_CURRENT_READING);
  String response = sendHomeAppHttpsRequest(url, payload, true);
  Serial.printf("%s response: %s\n", label, response.c_str());
  return true;
}

bool sendDhtUpdateRequest(bool force = false) {
  return sendSensorUpdateRequest(buildDhtReadingSensorUpdateRequest(force), "DHT");
}

bool sendLdrUpdateRequest(bool force = false) {
  return sendSensorUpdateRequest(buildLdrReadingsSensorUpdateRequest(force), "LDR");
}

String buildShtUpdateRequest(bool force = false) {
  Serial.println("Building Sht request");  
  JsonDocument sensorUpdateRequest;

  unsigned long currentTime = millis();
  int jsonPositionTracker = 0;
  for (int i = 0; i < shtData.sensorCount; ++i) {
    Serial.printf("next reading due in %lu ms\n", currentTime >= shtData.sendNextReadingAt[i] ? 0UL : shtData.sendNextReadingAt[i] - currentTime);
    if (currentTime >= shtData.sendNextReadingAt[i] || force == true) {
      shtData.tempReading[i] = sht31[i]->readTemperature();
      shtData.humidReading[i] = sht31[i]->readHumidity();
      Serial.print("Sht Temperature readings is: ");
      Serial.println(shtData.tempReading[i]);
      Serial.print("Sht Humidity readings is: ");
      Serial.println(shtData.humidReading[i]);
      
      if (!isnan(shtData.tempReading[i])) {
        Serial.print("sensor name:");
        Serial.println(shtData.sensorName[i]);
        sensorUpdateRequest["sensorData"][jsonPositionTracker]["sensorType"] = SHTNAME;
        sensorUpdateRequest["sensorData"][jsonPositionTracker]["sensorName"] = shtData.sensorName[i];
        sensorUpdateRequest["sensorData"][jsonPositionTracker]["currentReadings"]["temperature"] = String(shtData.tempReading[i]);
        sensorUpdateRequest["sensorData"][jsonPositionTracker]["currentReadings"]["humidity"] = String(shtData.humidReading[i]);
  
        shtData.sendNextReadingAt[i] = currentTime + shtData.interval[i];
        ++jsonPositionTracker;
      }
    }
  }
  
  String jsonData;
  serializeJson(sensorUpdateRequest, jsonData);
  Serial.print("SHT json data to send: ");
  Serial.println(jsonData);    

  return jsonData;  
}

bool sendShtUpdateRequest(bool force = false) {
  return sendSensorUpdateRequest(buildShtUpdateRequest(force), "SHT");
}

/////<!---END OF NETWORK METHODS---!>//////



//<------- Dallas Sensor Functions -------------->
bool setDallasValues() {
  Serial.println("Checking to see if dallas values are set");
  if (!SPIFFS.exists("/dallas.json")) {
    Serial.println("No dallas spiff found no longer setting dallas values");
    dallasTempData.settingsJsonExists = false;
    return false;
  }
  
  String dallasSensorSpiff = getSerializedSpiff("/dallas.json");  
  if (!dallasSensorSpiff) {
    return false;
  }
  
  Serial.println("Deserialzing dallas json");
  JsonDocument dallasDoc = getDeserializedJson(dallasSensorSpiff, 1256);

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

    strncpy(dallasTempData.sensorName[i], dallasDoc[i]["sensorName"].as<const char*>(), sizeof(dallasTempData.sensorName[i]));
    Serial.printf("Dallas sensor name check %s", dallasTempData.sensorName[i]);
    int readingInterval = dallasDoc[i]["readingInterval"].as<int>();
    dallasTempData.interval[i] = readingInterval ? readingInterval : 6000;
    Serial.printf("bus temp temperature send interval: %d\n", dallasTempData.interval[i]);
  }

  int dallasSensorPinNumber = dallasDoc[0]["pinNumber"].as<int>();
  dallasTempData.pinNumber = dallasSensorPinNumber;

  Serial.printf("Dallas temp pin number is %d\n", dallasTempData.pinNumber);

  dallasTempData.valuesAreSet = true;  
  
  return true;
}

bool findDallasSensor() {
  Serial.print("pin ");
  Serial.println(dallasTempData.pinNumber);
  if (searchPinForOneWire(dallasTempData.pinNumber)) {
    Serial.println("Dallas sensor found creating reference");
    DallasTemperature sensors(&oneWire);
    return true;
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

String buildDallasReadingSensorUpdateRequest(bool force = false) {
  Serial.println("Building Dallas request");
  JsonDocument sensorUpdateRequest;
  unsigned long currentTime = millis();
  Serial.printf("current time: %lu\n", currentTime);
  int jsonArrayIndex = 0;

  for (int i = 0; i < dallasTempData.sensorCount; ++i) {
    if (currentTime >= dallasTempData.sendNextReadingAt[i] || force == true) {
      sensors.requestTemperatures();
      break;
    }
  }
  
  for (int i = 0; i < dallasTempData.sensorCount; ++i) {
    Serial.printf("Dallas sensor next reading at value is %lu\n", dallasTempData.sendNextReadingAt[i]);
    Serial.printf("next reading due in %lu ms\n", currentTime >= dallasTempData.sendNextReadingAt[i] ? 0UL : dallasTempData.sendNextReadingAt[i] - currentTime);
    if (currentTime >= dallasTempData.sendNextReadingAt[i] || force == true) {
      float tempReading = sensors.getTempCByIndex(i);
      Serial.print("Temp number:");
      Serial.print(i);
      Serial.print(" Tempreture is:");
      Serial.println(tempReading); 
      dallasTempData.tempReading[i] = tempReading; 
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
  return sendSensorUpdateRequest(buildDallasReadingSensorUpdateRequest(force), "Dallas");
}
/////<!---END OF Dallas Sensor Methods ---!>//////

//RelayFunctions
String buildRelaySensorUpdateRequest(bool force = false) {
  Serial.println("Building Relay request");
  JsonDocument sensorUpdateRequest;

  unsigned long currentTime = millis();
  Serial.printf("current time: %lu\n", currentTime);
  int jsonArrayIndex = 0;

  for (int i = 0; i < relayData.sensorCount; ++i) {
    Serial.printf("next reading due in %lu ms\n", currentTime >= relayData.sendNextReadingAt[i] ? 0UL : relayData.sendNextReadingAt[i] - currentTime);
    if (currentTime >= relayData.sendNextReadingAt[i] || force == true) {      
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
  return sendSensorUpdateRequest(buildRelaySensorUpdateRequest(force), "Relay");
}


// Web Functions
void resetDevice() {
  const char* files[] = {"/device.json", "/wifi.json", "/dallas.json", "/dht.json", "/relay.json", "/sht.json", "/ldr.json"};
  for (const char* f : files) SPIFFS.remove(f);
  server.send(200, "application/json", "{\"status\":\"device reset\"}");
  ESP.restart();
}

void restartDevice() {
  server.send(200, "application/json", "{\"status\":\"ok\"}");
  ESP.restart();
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
  if (ldrData.activeSensor == true) {
    sendLdrUpdateRequest(force);
  }
  if (shtData.activeSensor == true) {
    sendShtUpdateRequest(force);
  }
}

void sendAllSensorData() {
  server.send(200, "application/json", "{\"pong\"}");
  sendAllActiveSensorData(true);
}

// Common Functions
JsonDocument getDeserializedJson(String serializedJson, int jsonBuffSize) {
  Serial.println("serialized json to deserialize: ");
  Serial.println(serializedJson);
  Serial.println("Deseriazing Json");
  //const char* jsonData[jsonBuffSize];
  //strcpy(jsonData, serializedJson.c_str());

  JsonDocument deserializedJson;
  DeserializationError error = deserializeJson(deserializedJson, serializedJson);
  if (error) {
    Serial.println("deserialization error");
    Serial.println(error.c_str());
  }
//  Serial.println("deserialization success");
  
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
  struct { const char* path; bool* flag; } entries[] = {
    {"/dallas.json", &dallasTempData.settingsJsonExists},
    {"/dht.json",    &dhtSensor.settingsJsonExists},
    {"/relay.json",  &relayData.settingsJsonExists},
    {"/ldr.json",    &ldrData.settingsJsonExists},
    {"/sht.json",    &shtData.settingsJsonExists}
  };
  for (auto& e : entries) {
    if (SPIFFS.exists(e.path)) *e.flag = true;
  }
}

//<---------- END OF SPIFF MEthods ------------------>

void handleSwitchSensor() {
  Serial.println("Handling SWITCH");
  String sensorSwitchRequest = server.arg("plain");

  Serial.println("Deserialzing Request");
  JsonDocument sensorSwitchJson = getDeserializedJson(sensorSwitchRequest, 512);

  if (sensorSwitchJson["sensorName"].isNull() && sensorSwitchJson["pinNumber"].isNull()) {
    server.send(500, "application/json", "{\"status\":\"failed to switch sensor request error\"}"); 
    return;
  }
  
  int pinNumber = sensorSwitchJson["pinNumber"].as<int>();
  bool requestedReading = sensorSwitchJson["requestedReading"].as<boolean>();

  bool passed = false;
  for(int i = 0; i < MAX_RELAYS; ++i) {
    if (relayData.pinNumber[i] == pinNumber && relayData.sensorName[i] == sensorSwitchJson["sensorName"]) {
      Serial.println("Sensor and pin matched marking as passed");
      passed = true;
      relayData.currentReading[i] = requestedReading;
    }          
  }
  if (passed == false) {
    Serial.println("FAILED to find correct pin for sensor name");
    server.send(401, "application/json", "{\"status\":\"pin is not configured to set set\"}");   
    return;
  }

  Serial.print("Sensor Name: ");
  Serial.println(sensorSwitchJson["sensorName"].as<String>());
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
}

void setup() {
  Serial.begin(DEVICE_SERIAL);
  Serial.println("Searial started");

  Serial.println("SPIFFS starting...");
  if (!SPIFFS.begin()) {
    Serial.println("SPIFFS failed to start");
    ESP.restart();
  }
  Serial.println("...SPIFFS started");

  if (DEVICE_SERIAL != 115200) {
    for (int i = 0; i < DEVICE_LED_PIN_SANCTIONED; i++) {
      pinMode(ledPins[i], OUTPUT); 
      digitalWrite(ledPins[i], HIGH);
    }  
  } else {
    pinMode(1, OUTPUT); // TX (GPIO1) as output
    pinMode(3, OUTPUT); // RX (GPIO3) as input
  }
  
 
  Serial.print("Starting web servers...");
  server.serveStatic("/", SPIFFS, "/index.html");
  server.on("/settings", HTTP_POST, handleSettingsUpdate);

  server.on("/switch", HTTP_POST, handleSwitchSensor);
  server.on("/ping", HTTP_GET, sendAllSensorData);
  
  server.on("/reset-device", HTTP_GET, resetDevice);
  server.on("/restart-device", HTTP_GET, restartDevice);
  server.begin();
  Serial.println("Servers Begun");

  delay(2000);
  Serial.println("Begining device setup");
  
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
  
  if(WiFi.status() == WL_CONNECTED) {
    if (deviceLoggedIn == true) {
      sendAllActiveSensorData();
      if (millis() >= ipAddressNextUpdate) {
        updateDeviceIPAddress();
        ipAddressNextUpdate = millis() + 3600000UL;
      }
    } else {
      Serial.println("Device not loged in attempting to refresh token");
      deviceLoggedIn = deviceLogin();
      if (deviceLoggedIn == false && deviceRegistered == false) {
        registerDevice();
      }
    }
    if (publicIpAddress[0] == '\0') {
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
      }
    } 
  }
  if (dhtSensor.settingsJsonExists == true && dhtSensor.valuesAreSet == false) {
    setDhtValues();
    if (dhtSensor.valuesAreSet == true) {
      Serial.println("Starting Dht");
      dhtSensor.activeSensor = true;
    }     
  }
  if (relayData.settingsJsonExists == true && relayData.valuesAreSet == false) {
    setRelayValues();
    Serial.println("Starting Relay");
    relayData.activeSensor = true;
  }
  if (ldrData.settingsJsonExists == true && ldrData.valuesAreSet == false) {
    setLdrValues();
    Serial.println("Starting LDRS");
    ldrData.activeSensor = true;
  } 

  if (shtData.settingsJsonExists == true && shtData.valuesAreSet == false) {
    bool shtSetSuccess = setShtValues();
    if (shtSetSuccess == false) {
      shtData.valuesAreSet = false;
    }
    Serial.println("Starting SHT");
    shtData.activeSensor = true;
  }
}
