#!/bin/bash
# install.sh – sorgt dafür, dass unser DeskPlanner-Programm korrekt läuft

# 1) System aktualisieren (optional, aber empfohlen)
#sudo apt-get update -y
#sudo apt-get upgrade -y

# 2) Abhängigkeiten installieren
sudo apt-get install -y python3 python3-pip git # und ggf. weitere Tools
# (z.B. pigpio installieren, falls du pigpio statt wiringPi nutzen willst)
# sudo apt-get install -y pigpio python3-pigpio

# 3) Benutzer in die gpio-Gruppe aufnehmen (falls gewünscht)
# Angenommen, dein Nutzer heißt 'pi' oder ein anderer Name in der Variable $USER.
sudo adduser "$USER" gpio
sudo adduser www-data gpio


# 4) (Optional) pigpiod-Dienst aktivieren und starten, falls du pigpio nutzt
# sudo systemctl enable pigpiod
# sudo systemctl start pigpiod

echo "Installation abgeschlossen."
echo "Bitte melde dich einmal ab und wieder an (oder starte den Pi neu), damit die Gruppenänderungen wirksam werden."
