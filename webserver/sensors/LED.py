import time
import RPi.GPIO as GPIO

# GPIO-Pin, an dem die LED angeschlossen ist
LED_PIN = 18

# Pfad zur Datei mit dem LED-Zustand
FILE_PATH = "/home/pi/DeskPlanner/webserver/LED.txt"

# GPIO-Setup
GPIO.setmode(GPIO.BCM)
GPIO.setup(LED_PIN, GPIO.OUT)

# Globale Variable zum Speichern des letzten Zustands
last_state = None

def read_led_file():
    """Liest den Inhalt der LED.txt-Datei aus."""
    try:
        with open(FILE_PATH, "r") as file:
            return file.read().strip()
    except FileNotFoundError:
        print(f"Fehler: {FILE_PATH} wurde nicht gefunden.")
        return None

def control_led(state):
    """Steuert die LED basierend auf dem Zustand."""
    if state == "1":
        GPIO.output(LED_PIN, GPIO.HIGH)
    elif state == "0":
        GPIO.output(LED_PIN, GPIO.LOW)
    else:
        print("Ungueltiger Zustand. Erwartet '1' oder '0'.")

if __name__ == "__main__":
    try:
        while True:
            # Inhalt der Datei lesen
            current_state = read_led_file()

            # Pruefen, ob sich der Zustand geaendert hat
            if current_state and current_state != last_state:
                control_led(current_state)
                last_state = current_state

            # Kurze Pause, um die CPU-Auslastung zu minimieren
            time.sleep(1)
    except KeyboardInterrupt:
        print("\nProgramm wird beendet...")
    finally:
        GPIO.cleanup()
