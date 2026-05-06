#include <Arduino.h>
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

int relayPin = 0;
int inputPin = 2;
int pirState = LOW;
int val = 0;

void setup() {
  pinMode(relayPin, OUTPUT);
  pinMode(inputPin, INPUT);
 
  Serial.begin(115200);
}

void loop(){
  val = digitalRead(inputPin);
  if (val == HIGH) {
    digitalWrite(relayPin, HIGH);
    if (pirState == LOW) {
      Serial.println("Motion detected!");
      pirState = HIGH;
    }
    delay(5000);
  } else {
    digitalWrite(relayPin, LOW);
    if (pirState == HIGH){
      Serial.println("Motion ended!");
      pirState = LOW;
    }
  }
}
