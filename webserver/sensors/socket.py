import os
import time
import subprocess

# Pfad zur Datei mit den Schaltbefehlen
FILE_PATH = "/home/pi/DeskPlanner/webserver/socket.txt"

# Verzeichnis mit dem 433 MHz Sendeprogramm
SEND_DIR = "/home/pi/raspberry-remote"

# Globale Variable zum Speichern des letzten Zustands
last_state = None

def read_socket_file():
    """Liest den Inhalt der socket.txt-Datei aus."""
    try:
        with open(FILE_PATH, "r") as file:
            return file.read().strip()
    except FileNotFoundError:
        print(f"Fehler: {FILE_PATH} wurde nicht gefunden.")
        return None

def switch_socket(state):
    """Schaltet die Funksteckdose ein oder aus."""
    if state == "1":
        subprocess.run(["./send", "01111", "1", "1"], cwd=SEND_DIR)
        print("Steckdose eingeschaltet.")
    elif state == "0":
        subprocess.run(["./send", "01111", "1", "0"], cwd=SEND_DIR)
        print("Steckdose ausgeschaltet.")
    else:
        print("Ungueltiger Befehl. Erwartet '0' oder '1'.")

if __name__ == "__main__":
    print("Ueberwachung der socket.txt gestartet...")
    while True:
        # Inhalt der Datei lesen
        current_state = read_socket_file()

        # Check, ob sich der Zustand gechanged hat
        if current_state and current_state != last_state:
            switch_socket(current_state)
            last_state = current_state

        # Kurze Pause, um die CPU-Auslastung zu minimieren
        time.sleep(1)
