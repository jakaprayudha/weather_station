#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>

// WiFi
const char* ssid = "WIFI_SSID";
const char* password = "WIFI_PASSWORD";

// ThingSpeak API
String apiKey = "R25QZFW0LNVJAWJ8";  // ganti dengan API Key channel kamu
const char* server = "http://api.thingspeak.com/update";

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);

  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nConnected to WiFi");
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    // Buat data dummy (misalnya sensor suhu & kelembaban)
    float temperature = random(250, 350) / 10.0; // 25.0 - 35.0
    float humidity = random(400, 900) / 10.0;    // 40.0 - 90.0

    // Format URL dengan parameter field
    String url = server;
    url += "?api_key=" + apiKey;
    url += "&field1=" + String(temperature);
    url += "&field2=" + String(humidity);

    Serial.println("Request URL: " + url);
    //http://api.thingspeak.com/update?api_key=YOUR_WRITE_API_KEY&field1=25.0&field2=40.0

    http.begin(url);
    int httpCode = http.GET();

    if (httpCode > 0) {
      Serial.printf("Response code: %d\n", httpCode);
      String payload = http.getString();
      Serial.println("ThingSpeak reply: " + payload);
    } else {
      Serial.printf("Error sending data: %s\n", http.errorToString(httpCode).c_str());
    }

    http.end();
  }

  delay(20000); // delay 20 detik (ThingSpeak minimal 15 detik antar request)
}