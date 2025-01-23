# DeskPlanner

DeskPlanner is a web-based studying and planning tool designed specifically for Raspberry Pi systems. It integrates various sensors and GPIO controls to provide a seamless user experience for managing deadlines, exercises, learning sessions and more. Leveraging Apache2 and PHP for the web server, alongside Python scripts for hardware interactions, DeskPlanner offers a handy solution for personal and educational projects.

## Table of Contents

- [Features](#features)
- [Prerequisites](#prerequisites)
  - [Hardware](#hardware)
  - [Pin Allocation and Wiring Diagram](#pin-allocation-and-wiring-diagram)
    - [LED](#led)
    - [433 MHz Transmitter for Wireless Socket](#433-mhz-transmitter-for-wireless-socket)
    - [DHT22 Temperature and Moisture Sensor](#dht22-temperature-and-moisture-sensor)
    - [Wiring Diagram](#wiring-diagram)
  - [Software](#software)
- [Installation](#installation)



---

## Features

- **Web Interface:** Manage deadlines, exercises, peripherals and user data through an intuitive web interface.  
- **Sensor Integration:** Utilize GPIO pins to interact with LEDs, sockets, and temperature/humidity sensors.  
- **Dynamic Configuration:** Automatically adapts to different installation directories.  
- **Apache & PHP Integration:** Serves the web application efficiently with Apache2 and PHP support.

---

## Prerequisites

Before installing DeskPlanner, ensure that your Raspberry Pi setup meets the following requirements:

### Hardware

- Raspberry Pi (preferably Raspberry Pi 3 or later)  
- MicroSD card with Raspberry Pi OS installed  
- Power supply for Raspberry Pi  
- Internet connection (Ethernet or Wi-Fi)  
- 6 x FF Jumper Wire
- 2 x MF Jumper Wire
- DHT22 temperature and moisture sensor
- 433 MHz transmitter
- 433 MHz wireless socket
- LED
- Breadboard

## Pin allocation and Wiring Diagram
### LED
- Anode: Pin 12 (GPIO 18)
- Kathode: Pin 30 (GND)
### 433 Mhz transmitter for wireless socket
- VCC: Pin2 (3,3 V)
- GND: Pin 6 (GND)
- DATA: Pin 11 (GPIO 17)
### DHT22 temperature and moisture sensor
- Plus : Pin 1 (3,3 v)
- Minus : Pin 39 (GND)
- out: Pin 3 (GPIO 2)

### Wiring Diagram

![Wiring Diagram](webserver/img/DeskPlanner_Steckplatine.jpg "Wiring Diagram")


### Software

- Raspberry Pi OS (preferably the Lite version for headless setups)  
- SSH access (optional but recommended for headless installations)

---

## Installation

Follow these step-by-step instructions to set up DeskPlanner on your Raspberry Pi. We have included a install.sh containing the necessary installations for an easy setup.

### 1. Install Git

Git is required to clone the DeskPlanner repository from GitHub.

```bash
sudo apt-get update
sudo apt-get install -y git
```

### 2. Clone the Repository

1. **Navigate to Your Desired Installation Directory**
   
   It's common to place projects in the `/home/pi` directory. You can create a directory for projects if it doesn't exist:
   ```bash
   cd /home/pi
   mkdir Projects
   cd Projects
   ```   
2. **Clone the DeskPlanner Repository**
```bash
  git clone https://github.com/LinusPoerschke/DeskPlanner.git

```
3. **Navigate to the Project Directory**
```bash
  cd DeskPlanner
```

4. **Run the Installation Script**
```bash
   chmod +x install.sh
  ./install.sh
```
### 3. Navigate to the Website and register

http://<your_IP>/DeskPlanner.html
