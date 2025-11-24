#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

#define MAGNETIC_PIN 5   // Pin untuk Magnetic Sensor (Reed Switch)
#define PIR_PIN 18       // Pin untuk PIR Sensor
#define LED_BUILTIN 2

// Konfigurasi WiFi dan server
const char* ssid = "YOUR_SSID";                       // Nama WiFi Kamu
const char* password = "YOUR_WIFI_PASS";              // Password WiFI Kamu
const char* server = "http://your-domain.or.ip/project_iot/api_pdo";  // IP PC kamu - PHP 
// const char* server = "http://your-domain.or.ip:3000/api";  // IP PC kamu - Express
const char* DEVICE_ID = "esp32-unit-003";             // (opsional) untuk ngasih tau Device aja

// Untuk ngatur waktu
unsigned long lastSend = 0;
const unsigned long sendInterval = 15 * 1000UL; // kirim tiap 15 detik
unsigned long lastPoll = 0;
const unsigned long pollInterval = 2 * 1000UL; // cek perintah tiap 2 detik

void setup(){
  // Setup Basic
  Serial.begin(115200);             // Jalanin Serial Monitor
  pinMode(MAGNETIC_PIN, INPUT_PULLUP); // Magnetic sensor dengan pull-up resistor
  pinMode(PIR_PIN, INPUT);          // PIR sensor sebagai input
  pinMode(LED_BUILTIN, OUTPUT);     // Lampu Builtin ESP32 jadi OUTPUT
  digitalWrite(LED_BUILTIN, LOW);   // default mati

  // Koneksi WiFI
  WiFi.begin(ssid, password);                    // Koneksi ke Hospot kita
  Serial.println("Connecting to WiFi...");       // Ngasih tau kalo lagi konek
  int retries = 0;
  while (WiFi.status() != WL_CONNECTED) {        // Handler untuk status
    delay(1000);
    Serial.print("Status: ");
    Serial.println(WiFi.status());               // Kode Status cek "Contoh Status"
    retries++;
    if (retries > 20) {                          // Kalo gak konek konek langsung tampil error
      Serial.println("Failed to connect to WiFi.");  
      return; // keluar dari setup
    }
  }
  Serial.println("Connected!");                  // Berhasil Connect
  Serial.println(WiFi.localIP());                // Nampilin IP ESP32
}

void loop(){
  unsigned long now = millis();
  
  // Ngirim data sesuai sendInterval (berapa waktu yang diset tadi)
  if (now - lastSend >= sendInterval) {
    lastSend = now;
    sendSensorData();
  }
  
  // Nerima status sesuai PollInterval (berapa waktu yang diset tadi)
  if (now - lastPoll >= pollInterval) {
    lastPoll = now;
    pollCommands();
  }
}

// -- Fungsi untuk mengirimkan Data Sensor (hanya baca dan kirim)
void sendSensorData(){
  // Baca state sensor saat ini (TANPA PROSES)
  int magneticState = digitalRead(MAGNETIC_PIN);
  int pirState = digitalRead(PIR_PIN);
  
  // Kirim data Magnetic Sensor
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
  
  delay(100); // Delay kecil antar pengiriman
  
  // Kirim data PIR Sensor
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

// -- Fungsi untuk nangkap status dan commands
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

// -- Fungsi untuk eksekusi command dari server (HANYA EKSEKUSI AKTUATOR)
void executeCommand(long id, String cmd, String payload){
  Serial.printf("Exec command id=%ld cmd=%s payload=%s\n", id, cmd.c_str(), payload.c_str());
  String result = "unknown";

  if (cmd.length() == 0) {
    Serial.println("Command kosong, diabaikan");
    return;
  }
  
  // HANYA EKSEKUSI AKTUATOR - TANPA PROSES LOGIKA
  if (cmd == "lampu_on") {
    digitalWrite(LED_BUILTIN, HIGH);
    result = "lampu_on_ok";
  } else if (cmd == "lampu_off") {
    digitalWrite(LED_BUILTIN, LOW);
    result = "lampu_off_ok";
  } else {
    result = "cmd_not_supported";
  }

  // ack ke server - konfirmasi eksekusi selesai
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