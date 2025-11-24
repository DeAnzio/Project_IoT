#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>          // Library DHT Sensor Library
#include <ArduinoJson.h>  // Library ArduinoJson

// Konfigurasi Sensor DHT11
#define DHTPIN 21         // Pin DHT11 - Data Sensor
#define DHTTYPE DHT11     // Tipe Sensor DHT11
DHT dht(DHTPIN, DHTTYPE);

// Konfigurasi Sensor MQ-2
#define MQ2PIN 19         // Pin Digital MQ-2

// Konfigurasi Aktuator
#define PUMP_PIN 5        // Pin Relay Pompa (D5)

// Konfigurasi WiFi dan Server
const char* ssid = "YOUR_SSID";                       // Nama WiFi Kamu
const char* password = "YOUR_WIFI_PASS";              // Password WiFI Kamu
const char* server = "http://your-domain.or.ip/project_iot/api";  // IP Server
const char* DEVICE_ID = "esp32-unit-002";             // ID Device

// Pengaturan Waktu
unsigned long lastSend = 0;
const unsigned long sendInterval = 15 * 1000UL; // Kirim data tiap 15 detik

unsigned long lastPoll = 0;
const unsigned long pollInterval = 2 * 1000UL;  // Cek perintah tiap 2 detik

void setup(){
  // Setup Basic
  Serial.begin(115200);             // Jalankan Serial Monitor
  dht.begin();                      // Jalankan DHT Sensor
  
  // Setup Pin
  pinMode(MQ2PIN, INPUT);           // MQ-2 sebagai INPUT
  pinMode(PUMP_PIN, OUTPUT);        // Relay Pompa sebagai OUTPUT
  digitalWrite(PUMP_PIN, LOW);      // Default pompa mati (relay LOW)

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
}

// Fungsi untuk mengirim data sensor ke database
void sendSensorData(){
  // Baca data DHT11
  float humidity = dht.readHumidity();
  float temperature = dht.readTemperature();
  
  // Baca data MQ-2 (Digital)
  int mq2Status = digitalRead(MQ2PIN);  // 0 = Gas terdeteksi, 1 = Aman
  
  // Validasi data DHT11
  if (isnan(humidity) || isnan(temperature)) {
    Serial.println("Failed to read from DHT11");
    return;
  }
  
  // Tampilkan data di Serial Monitor
  Serial.println("=== Sensor Data ===");
  Serial.printf("Temperature: %.2f°C\n", temperature);
  Serial.printf("Humidity: %.2f%%\n", humidity);
  Serial.printf("MQ-2 Status: %s\n", mq2Status == 0 ? "Gas Detected!" : "Safe");
  
  // Kirim data ke server jika WiFi terhubung
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    
    // === Kirim Data Temperature ===
    String url = String(server) + "/save_data.php";
    http.begin(url);
    http.addHeader("Content-Type", "application/json");
    
    StaticJsonDocument<256> docTemp;
    docTemp["device_id"] = DEVICE_ID;
    docTemp["sensor_type"] = "DHT11_temp";
    docTemp["value"] = temperature;
    docTemp["raw"] = String("h=") + String(humidity);
    
    String bodyTemp;
    serializeJson(docTemp, bodyTemp);
    int codeTemp = http.POST(bodyTemp);
    
    if (codeTemp > 0) {
      Serial.printf("Temperature sent, code=%d\n", codeTemp);
    } else {
      Serial.printf("Temperature POST failed: %s\n", http.errorToString(codeTemp).c_str());
    }
    http.end();
    
    // === Kirim Data Humidity ===
    http.begin(url);
    http.addHeader("Content-Type", "application/json");
    
    StaticJsonDocument<256> docHum;
    docHum["device_id"] = DEVICE_ID;
    docHum["sensor_type"] = "DHT11_humidity";
    docHum["value"] = humidity;
    docHum["raw"] = String("t=") + String(temperature);
    
    String bodyHum;
    serializeJson(docHum, bodyHum);
    int codeHum = http.POST(bodyHum);
    
    if (codeHum > 0) {
      Serial.printf("Humidity sent, code=%d\n", codeHum);
    } else {
      Serial.printf("Humidity POST failed: %s\n", http.errorToString(codeHum).c_str());
    }
    http.end();
    
    // === Kirim Data MQ-2 ===
    http.begin(url);
    http.addHeader("Content-Type", "application/json");
    
    StaticJsonDocument<256> docMQ2;
    docMQ2["device_id"] = DEVICE_ID;
    docMQ2["sensor_type"] = "MQ2_gas";
    docMQ2["value"] = mq2Status;  // 0 atau 1
    docMQ2["raw"] = mq2Status == 0 ? "gas_detected" : "safe";
    
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