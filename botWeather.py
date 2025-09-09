import requests
import random
import time

# Base URL API
BASE_URL = "http://localhost/iotlearning/api/insertdata.php"

def send_data(device_id='c546b92c328c231ffbbdeddac2381073'):
    # Generate dummy data
    temp = round(random.uniform(20.0, 35.0), 1)   # suhu antara 20-35 °C
    humd = round(random.uniform(30.0, 90.0), 1)   # kelembaban 30-90%

    # Buat parameter query
    params = {
        "number_devices": device_id,
        "temp": temp,
        "humd": humd
    }

    try:
        # Kirim data via GET request
        response = requests.get(BASE_URL, params=params)

        # Debug log
        print(f"[SEND] Device {device_id} → Temp={temp}, Humd={humd}")
        print(f"[RESP] {response.status_code}: {response.text}\n")

    except Exception as e:
        print(f"[ERROR] {e}")

if __name__ == "__main__":
    # Loop kirim data tiap 5 detik
    while True:
        send_data(device_id='c546b92c328c231ffbbdeddac2381073')  # bisa diganti 2,3 dst
        time.sleep(5)