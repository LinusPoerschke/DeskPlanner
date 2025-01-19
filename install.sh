#!/bin/bash
# install.sh – sorgt dafür, dass unser DeskPlanner-Programm korrekt läuft

# Beende das Skript bei Fehlern
set -e

# Funktion zur Anzeige von Nachrichten mit Farben
function echo_info {
    echo -e "\e[32m$1\e[0m"
}

function echo_warn {
    echo -e "\e[33m$1\e[0m"
}

function echo_error {
    echo -e "\e[31m$1\e[0m"
}

# 1) System aktualisieren (optional, aber empfohlen)
echo_info "Möchtest du das System aktualisieren? (y/n)"
read UPDATE_SYS
if [[ "$UPDATE_SYS" =~ ^[Yy]$ ]]; then
    echo_info "System wird aktualisiert..."
    sudo apt-get update -y
    sudo apt-get upgrade -y
else
    echo_info "Systemaktualisierung übersprungen."
fi

# 2) Abhängigkeiten installieren
echo_info "Installiere Abhängigkeiten..."
sudo apt-get install -y python3 python3-pip git

# (Optional) Weitere Abhängigkeiten installieren, z.B. pigpio
echo_info "Möchtest du pigpio installieren? (y/n)"
read INSTALL_PIGPIO
if [[ "$INSTALL_PIGPIO" =~ ^[Yy]$ ]]; then
    echo_info "Installiere pigpio..."
    sudo apt-get install -y pigpio python3-pigpio
else
    echo_info "pigpio-Installation übersprungen."
fi

# 3) Benutzer zur gpio-Gruppe hinzufügen
echo_info "Füge Benutzer zur gpio-Gruppe hinzu..."

# Bestimme den aktuellen Benutzer
CURRENT_USER=$(whoami)

# Füge den aktuellen Benutzer zur gpio-Gruppe hinzu
sudo adduser "$CURRENT_USER" gpio

# Füge den Webserver-Benutzer (www-data) zur gpio-Gruppe hinzu
sudo adduser www-data gpio

echo_info "Benutzer wurden zur gpio-Gruppe hinzugefügt."

# 4) Dynamische Ermittlung des Installationsverzeichnisses
# Annahme: Das Skript wird im Hauptverzeichnis von DeskPlanner ausgeführt
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
WEB_DIR="$SCRIPT_DIR/webserver"

echo_info "Installationsverzeichnis erkannt: $WEB_DIR"

# 5) Besitz und Berechtigungen für exercises.txt setzen
EXERCISES_FILE="$WEB_DIR/exercises.txt"

echo_info "Setze Besitz und Berechtigungen für $EXERCISES_FILE..."

# Erstelle die Datei, falls sie nicht existiert, und initialisiere sie mit einem leeren JSON-Array
if [ ! -f "$EXERCISES_FILE" ]; then
    echo_info "Erstelle $EXERCISES_FILE mit einem leeren JSON-Array."
    sudo bash -c "echo '[]' > '$EXERCISES_FILE'"
fi

# Setze den Eigentümer der Datei auf www-data:www-data
sudo chown www-data:www-data "$EXERCISES_FILE"

# Setze die Berechtigungen auf 664 (Lesen und Schreiben für Besitzer und Gruppe)
sudo chmod 664 "$EXERCISES_FILE"

echo_info "Besitz und Berechtigungen für $EXERCISES_FILE wurden gesetzt."

# 6) Setze Berechtigungen für das Webserver-Verzeichnis
echo_info "Setze Berechtigungen für das Webserver-Verzeichnis..."

# Setze den Eigentümer des Webserver-Verzeichnisses auf www-data:www-data
sudo chown -R www-data:www-data "$WEB_DIR"

# Setze die Berechtigungen auf 755 für Verzeichnisse und 664 für Dateien
sudo find "$WEB_DIR" -type d -exec chmod 755 {} \;
sudo find "$WEB_DIR" -type f -exec chmod 664 {} \;

echo_info "Berechtigungen für das Webserver-Verzeichnis wurden gesetzt."

# 7) (Optional) pigpiod-Dienst aktivieren und starten, falls pigpio verwendet wird
if [[ "$INSTALL_PIGPIO" =~ ^[Yy]$ ]]; then
    echo_info "Aktiviere und starte den pigpiod-Dienst..."
    sudo systemctl enable pigpiod
    sudo systemctl start pigpiod
    echo_info "pigpiod-Dienst wurde aktiviert und gestartet."
fi

# Abschlussnachricht
echo_info "Installation abgeschlossen."
echo_warn "Bitte melde dich einmal ab und wieder an (oder starte den Pi neu), damit die Gruppenänderungen wirksam werden."
