#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

#define MAGNETIC_PIN 5   // Pin untuk Magnetic Sensor (Reed Switch)
#define PIR_PIN 18       // Pin untuk PIR Sensor
#define LED 23           // pin Led
#define BUZZER 19        // Pin buzzer

// Konfigurasi WiFi dan server
const char* ssid = "Hitam_Legam";
const char* password = "00000000";
const char* server = "http://10.35.125.230/project_iot/api_pdo";
const char* DEVICE_ID = "esp32-unit-003";

// Untuk ngatur waktu
unsigned long lastSend = 0;
const unsigned long sendInterval = 2 * 1000UL;
unsigned long lastPoll = 0;
const unsigned long pollInterval = 2 * 1000UL;

// Alarm state
bool alarmEnabled = false;
unsigned long buzzerStartTime = 0;
const unsigned long buzzerDuration = 3000; // Buzzer berbunyi 3 detik

void setup(){
  Serial.begin(115200);
  pinMode(MAGNETIC_PIN, INPUT_PULLUP);
  pinMode(PIR_PIN, INPUT);
  pinMode(LED, OUTPUT);
  pinMode(BUZZER, OUTPUT);
  digitalWrite(LED, LOW);
  digitalWrite(BUZZER, LOW);

  WiFi.begin(ssid, password);
  Serial.println("Connecting to WiFi...");
  int retries = 0;
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print("Status: ");
    Serial.println(WiFi.status());
    retries++;
    if (retries > 20) {
      Serial.println("Failed to connect to WiFi.");
      return;
    }
  }
  Serial.println("Connected!");
  Serial.println(WiFi.localIP());
}

void loop(){
  unsigned long now = millis();
  
  // Kirim data sensor
  if (now - lastSend >= sendInterval) {
    lastSend = now;
    sendSensorData();
  }
  
  // Polling commands
  if (now - lastPoll >= pollInterval) {
    lastPoll = now;
    pollCommands();
  }
  
  // Handle buzzer timeout
  if (buzzerStartTime > 0 && (now - buzzerStartTime) > buzzerDuration) {
    digitalWrite(BUZZER, LOW);
    buzzerStartTime = 0;
  }
}

void sendSensorData(){
  int magneticState = digitalRead(MAGNETIC_PIN);
  int pirState = digitalRead(PIR_PIN);
  
  // Send Magnetic Sensor
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = String(server) + "/save_data_secusys.php";
    http.begin(url);
    http.addHeader("Content-Type", "application/json");

    
    StaticJsonDocument<256> doc;
    doc["device_id"] = DEVICE_ID;
    doc["sensor_type"] = "MAGNETIC";
    doc["value"] = magneticState;
    
    String body;
    serializeJson(doc, body);
    int code = http.POST(body);
    
    if (code > 0) {
      Serial.printf("Send Magnetic: %d, code=%d\n", magneticState, code);
    }
    http.end();
  }
  
  delay(100);
  
  // Send PIR Sensor
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = String(server) + "/save_data_secusys.php";
    http.begin(url);
    http.addHeader("Content-Type", "application/json");
    
    StaticJsonDocument<256> doc;
    doc["device_id"] = DEVICE_ID;
    doc["sensor_type"] = "PIR";
    doc["value"] = pirState;
    
    String body;
    serializeJson(doc, body);
    int code = http.POST(body);
    
    if (code > 0) {
      Serial.printf("Send PIR: %d, code=%d\n", pirState, code);
    }
    http.end();
  }

}

void pollCommands(){
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = String(server) + "/get_command.php?device_id=" + DEVICE_ID;
    http.begin(url);
    
    int code = http.GET();
    
    if (code == 200) {
      String payload = http.getString();
      Serial.println("pollCommands: " + payload);
      
      DynamicJsonDocument doc(512);
      DeserializationError err = deserializeJson(doc, payload);
      
      if (!err && doc["status"] == String("ok")) {
        long cmd_id = doc["command_id"];
        const char* cmd = doc["command"];
        const char* cmdPayload = doc["payload"];

        String payloadStr = cmdPayload ? String(cmdPayload) : "";
        executeCommand(cmd_id, String(cmd), payloadStr);
      }
    } else {
      Serial.printf("GET failed, code %d\n", code);
    }
    
    http.end();
  }
}

void executeCommand(long id, String cmd, String payload){
  Serial.printf("Exec command id=%ld cmd=%s payload=%s\n", id, cmd.c_str(), payload.c_str());
  String result = "unknown";

  if (cmd.length() == 0) {
    Serial.println("Command kosong, diabaikan");
    return;
  }
  
  // Handle alarm commands
  if (cmd == "alarm_on") {
    alarmEnabled = true;
    digitalWrite(LED, HIGH);
    result = "alarm_on_ok";
    Serial.println("Alarm ENABLED");
  } 
  else if (cmd == "alarm_off") {
    alarmEnabled = false;
    digitalWrite(LED, LOW);
    digitalWrite(BUZZER, LOW);
    buzzerStartTime = 0;
    result = "alarm_off_ok";
    Serial.println("Alarm DISABLED");
  }
    else if (cmd == "buzzer_on") {
    digitalWrite(BUZZER, HIGH);
    buzzerStartTime = millis();
    result = "buzzer_on_ok";
    Serial.println("Buzzer ON (dari server)");
  }
  else if (cmd == "buzzer_off") {
    digitalWrite(BUZZER, LOW);
    buzzerStartTime = 0;
    result = "buzzer_off_ok";
    Serial.println("Buzzer OFF (dari server)");
  }
  else if (cmd == "lampu_on") {
    digitalWrite(LED, HIGH);
    result = "lampu_on_ok";
  } 
  else if (cmd == "lampu_off") {
    digitalWrite(LED, LOW);
    result = "lampu_off_ok";
  } 
  else {
    result = "cmd_not_supported";
  }

  // Ack ke server
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = String(server) + "/ack_command.php";
    http.begin(url);
    http.addHeader("Content-Type", "application/json");


    StaticJsonDocument<256> doc;
    doc["command_id"] = id;
    doc["result"] = result;

    String body;
    serializeJson(doc, body);
    int code = http.POST(body);
    
    if (code > 0) {
      Serial.printf("ack code=%d\n", code);
    }
    http.end();
  }
}