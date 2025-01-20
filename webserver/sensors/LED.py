#!/usr/bin/env python3
import sys
import RPi.GPIO as GPIO

LED_PIN = 18

# Initialize GPIO
GPIO.setwarnings(False)  # Suppress GPIO warnings
GPIO.setmode(GPIO.BCM)
GPIO.setup(LED_PIN, GPIO.OUT)

def set_led(state):
    if state == "1":
        GPIO.output(LED_PIN, GPIO.HIGH)
    else:
        GPIO.output(LED_PIN, GPIO.LOW)

def get_led_state():
    return GPIO.input(LED_PIN)

if __name__ == "__main__":
    try:
        if len(sys.argv) > 1:
            state = sys.argv[1].strip()
            set_led(state)
            # Small delay to ensure the state is set before reading
            GPIO.output(LED_PIN, GPIO.HIGH if state == "1" else GPIO.LOW)
            print("ON" if state == "1" else "OFF")
        else:
            state = get_led_state()
            print("ON" if state else "OFF")
    except Exception as e:
        print(f"Error: {e}")
    finally:
        # Do not clean up GPIO to maintain state
        pass
