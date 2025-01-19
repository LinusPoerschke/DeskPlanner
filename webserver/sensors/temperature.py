#!/usr/bin/env python3
import Adafruit_DHT

DHT_SENSOR = Adafruit_DHT.DHT22
DHT_PIN = 2

def read_sensor_data():
    humidity, temperature = Adafruit_DHT.read_retry(DHT_SENSOR, DHT_PIN)
    if humidity is not None and temperature is not None:
        # Einfach "Temperatur,Feuchtigkeit" ausgeben
        print(f"{temperature:.1f},{int(humidity)}")
    else:
        print("Fehler beim Lesen des DHT22-Sensors.")

if __name__ == "__main__":
    read_sensor_data()
