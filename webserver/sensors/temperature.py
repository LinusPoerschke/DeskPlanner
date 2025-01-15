# -*- coding: utf-8 -*-
import Adafruit_DHT
import time

DHT_SENSOR = Adafruit_DHT.DHT22
DHT_PIN = 2
FILE_PATH = "/home/pi/DeskPlanner/webserver/sensors/sensorData.txt"

def log_sensor_data():
    with open(FILE_PATH, "a") as file:
        humidity, temperature = Adafruit_DHT.read_retry(DHT_SENSOR, DHT_PIN)
        if humidity is not None and temperature is not None:
            data = f"{time.strftime('%Y-%m-%d %H:%M:%S')}, Temp: {temperature:.2f}\u00b0C, Humidity: {humidity:.2f}%\n"
            print(data.strip())
            file.write(data)
            file.flush()
        else:
            print("Fehler beim Lesen des DHT22-Sensors.")

if __name__ == "__main__":
    log_sensor_data()
