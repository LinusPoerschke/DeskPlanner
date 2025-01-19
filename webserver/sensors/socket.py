#!/usr/bin/env python3
import os
import sys
import subprocess

script_dir = os.path.dirname(os.path.abspath(__file__))
SEND_DIR = os.path.join(script_dir, 'raspberry-remote')

def switch_socket(state):
    if state == "1":
        subprocess.run(["./send", "01111", "1", "1"], cwd=SEND_DIR)
        print("Steckdose eingeschaltet.")
    elif state == "0":
        subprocess.run(["./send", "01111", "1", "0"], cwd=SEND_DIR)
        print("Steckdose ausgeschaltet.")
    else:
        print("Ungültiger Befehl. Erwartet '0' oder '1'.")

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Usage: socket.py <0|1>")
        sys.exit(1)

    desired_state = sys.argv[1]
    switch_socket(desired_state)
