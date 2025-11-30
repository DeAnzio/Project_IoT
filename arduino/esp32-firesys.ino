#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>          // Library DHT Sensor Library
#include <ArduinoJson.h>  // Library ArduinoJson

// Konfigurasi Sensor DHT11
#define DHTPIN 23         // Pin DHT11 - Data Sensor
#define DHTTYPE DHT11     // Tipe Sensor DHT11
DHT dht(DHTPIN, DHTTYPE);

// Konfigurasi Sensor MQ-2
#define MQ2PIN 34         // Pin Analog MQ-2 (Gunakan GPIO 34, 35, 36, atau 39 untuk ADC)

// Konfigurasi Aktuator
#define PUMP_PIN 5        // Pin Relay Pompa (D5)
// Buzzer pin (optional, added for fire suppression alert)
#define BUZZER_PIN 19     // Pin Buzzer

//10.35.125.230

// Konfigurasi WiFi dan Server
const char* ssid = "Hitam_Legam";                       // Nama WiFi Kamu
const char* password = "00000000";              // Password WiFI Kamu
const char* server = "http://10.252.112.230/project_iot/api_pdo";  // IP Server
const char* DEVICE_ID = "esp32-unit-002";             // ID Device

// Pengaturan Waktu
unsigned long lastSend = 0;
const unsigned long sendInterval = 2 * 1000UL; // Kirim data tiap 2 detik

unsigned long lastPoll = 0;
const unsigned long pollInterval = 2 * 1000UL;  // Cek perintah tiap 2 detik

// Buzzer tracking
unsigned long buzzerStartTime = 0;
const unsigned long buzzerDuration = 3000; // Buzzer on duration (ms)

void setup(){
  // Setup Basic
  Serial.begin(115200);             // Jalankan Serial Monitor
  dht.begin();                      // Jalankan DHT Sensor
  
  // Setup Pin
  pinMode(MQ2PIN, INPUT);           // MQ-2 sebagai INPUT (Analog)
  pinMode(PUMP_PIN, OUTPUT);        // Relay Pompa sebagai OUTPUT
  digitalWrite(PUMP_PIN, LOW);      // Default pompa mati (relay LOW)
  pinMode(BUZZER_PIN, OUTPUT);
  digitalWrite(BUZZER_PIN, LOW);

  // Koneksi WiFi
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
  
  // Kirim data sensor sesuai interval
  if (now - lastSend >= sendInterval) {
    lastSend = now;
    sendSensorData();
  }
  
  // Terima perintah dari server sesuai interval
  if (now - lastPoll >= pollInterval) {
    lastPoll = now;
    pollCommands();
  }

  // Handle buzzer timeout
  if (buzzerStartTime > 0 && (now - buzzerStartTime) > buzzerDuration) {
    digitalWrite(BUZZER_PIN, LOW);
    buzzerStartTime = 0;
  }
}

// Fungsi untuk mengirim data sensor ke database
void sendSensorData(){

  // Baca data MQ-2 (Analog) - ESP32 ADC: 0-4095 (12-bit)
  int mq2Value = analogRead(MQ2PIN);
  
  // Konversi ke voltage (ESP32: 0-3.3V)
  float voltage = (mq2Value / 4095.0) * 3.3;
  
  
  Serial.printf("MQ-2 Analog: %d (%.2fV)\n", mq2Value, voltage);
  
  // Kirim data ke server jika WiFi terhubung
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    
    // === Kirim Data Temperature ===
    String url = String(server) + "/save_data_firesys.php";
    http.begin(url);
    http.addHeader("Content-Type", "application/json");
    

    // === Kirim Data MQ-2 (Analog) ===
    http.begin(url);
    http.addHeader("Content-Type", "application/json");
    
    StaticJsonDocument<256> docMQ2;
    docMQ2["device_id"] = DEVICE_ID;
    docMQ2["sensor_type"] = "MQ2_gas";
    docMQ2["value"] = mq2Value;  // Nilai analog 0-4095
    docMQ2["raw"] = String("voltage=") + String(voltage, 2);
    
    String bodyMQ2;
    serializeJson(docMQ2, bodyMQ2);
    int codeMQ2 = http.POST(bodyMQ2);
    
    if (codeMQ2 > 0) {
      Serial.printf("MQ-2 sent, code=%d\n", codeMQ2);
    } else {
      Serial.printf("MQ-2 POST failed: %s\n", http.errorToString(codeMQ2).c_str());
    }
    http.end();
    
    Serial.println("==================");
  }
}

// Fungsi untuk menerima perintah dari server
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

// Fungsi untuk eksekusi perintah aktuator
void executeCommand(long id, String cmd, String payload){
  Serial.printf("Exec command id=%ld cmd=%s payload=%s\n", id, cmd.c_str(), payload.c_str());
  String result = "unknown";
  
  if (cmd.length() == 0) {
    Serial.println("Command kosong, diabaikan");
    return;
  }
  
  // Kontrol Pompa
  if (cmd == "pompa_on") {
    digitalWrite(PUMP_PIN, HIGH);  // Nyalakan pompa
    result = "pompa_on_ok";
    Serial.println("Pompa ON");
  } 
  else if (cmd == "pompa_off") {
    digitalWrite(PUMP_PIN, LOW);   // Matikan pompa
    result = "pompa_off_ok";
    Serial.println("Pompa OFF");
  } 
  // Buzzer controls for alerting
  else if (cmd == "buzzer_on") {
    digitalWrite(BUZZER_PIN, HIGH);
    buzzerStartTime = millis();
    tone(BUZZER_PIN,1000);
    result = "buzzer_on_ok";
    Serial.println("Buzzer ON");
  }
  else if (cmd == "buzzer_off") {
    digitalWrite(BUZZER_PIN, LOW);
    buzzerStartTime = 0;
    noTone(BUZZER_PIN);
    result = "buzzer_off_ok";
    Serial.println("Buzzer OFF");
  } 
  else {
    result = "cmd_not_supported";
    Serial.println("Command tidak didukung");
  }
  
  // Kirim acknowledgment ke server
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
      Serial.printf("ack code=%d resp=%s\n", code, http.getString().c_str());
    }
    
    http.end();
  }
}