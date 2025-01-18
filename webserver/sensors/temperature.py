import Adafruit_DHT
import time

DHT_SENSOR = Adafruit_DHT.DHT22
DHT_PIN = 2
FILE_PATH = "/home/pi/DeskPlanner/webserver/sensorData.txt"

def log_sensor_data():
    with open(FILE_PATH, "w") as file:  # "w"
        humidity, temperature = Adafruit_DHT.read_retry(DHT_SENSOR, DHT_PIN)
        if humidity is not None and temperature is not None:
            data = f"{temperature:.1f},{int(humidity)}\n"
            print(data.strip())  # Optional: Konsolenausgabe
            file.write(data)
            file.flush()
        else:
            print("Fehler beim Lesen des DHT22-Sensors.")

if __name__ == "__main__":
    log_sensor_data()