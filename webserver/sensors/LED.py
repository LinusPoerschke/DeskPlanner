#!/usr/bin/env python3
import sys
import RPi.GPIO as GPIO

LED_PIN = 18

# GPIO initialisieren
GPIO.setmode(GPIO.BCM)
GPIO.setup(LED_PIN, GPIO.OUT)

def set_led(state):
    if state == "1":
        GPIO.output(LED_PIN, GPIO.HIGH)
    else:
        GPIO.output(LED_PIN, GPIO.LOW)

if __name__ == "__main__":
    try:
        # Prüfen, ob ein Parameter übergeben wurde (z. B. "1" oder "0")
        if len(sys.argv) > 1:
            state = sys.argv[1].strip()
            # LED entsprechend schalten
            set_led(state)
        else:
            print("Fehler: Kein Parameter übergeben. Aufruf z.B.: python3 LED.py 1 oder 0")
    finally:
        # GPIO-Ressourcen aufräumen
        GPIO.cleanup()
