#!/bin/bash

# -----------------------------------------------------------------------------
# install.sh - Script to set up the DeskPlanner project
# -----------------------------------------------------------------------------

# 1. Dynamically determine the installation directory
#    (If this script is located in /home/pi/DeskPlanner/, we get /home/pi/DeskPlanner as $INSTALL_DIR)

INSTALL_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
echo "Installation directory: $INSTALL_DIR"

# 2. Very broad permissions for the whole project (read, write, execute for everyone)
#    WARNING: chmod -R 777 is extremely permissive. Consider using more restrictive permissions.
chmod -R 777 "$INSTALL_DIR"

# 3. Clone, build, and install WiringPi and raspberry-remote in the sensors directory
echo "Cloning and building WiringPi and raspberry-remote..."
cd "$INSTALL_DIR/webserver/sensors"

# (Optionally ensure git and build tools are installed)
sudo apt-get update -y
sudo apt-get install -y git build-essential

# Clone WiringPi
git clone https://github.com/WiringPi/WiringPi
cd WiringPi
./build
cd ..

# Clone raspberry-remote
git clone https://github.com/xkonni/raspberry-remote.git
cd raspberry-remote
make send
cd ..

# Go back to the main install directory (optional, for cleanliness)
cd "$INSTALL_DIR"

# 4. Set executable permissions for *.py scripts and the C binary "send"
chmod +x "$INSTALL_DIR/webserver/sensors/LED.py"
chmod +x "$INSTALL_DIR/webserver/sensors/socket.py"
chmod +x "$INSTALL_DIR/webserver/sensors/temperature.py"
chmod +x "$INSTALL_DIR/webserver/sensors/raspberry-remote/send"

# 5. Update package lists and install necessary packages (Apache, PHP, Python, etc.)
echo "Updating package lists and installing required packages..."
sudo apt-get update -y
sudo apt-get install -y apache2 php libapache2-mod-php python3-dev python3-pip python3-rpi.gpio

# Upgrade pip and install Adafruit_DHT
echo "Upgrading pip and installing Python dependencies..."
sudo python3 -m pip install --upgrade pip setuptools wheel
sudo pip3 install Adafruit_DHT

# 6. Configure Apache to serve DeskPlanner from $INSTALL_DIR/webserver
echo "Configuring Apache virtual host..."

# Create a new Apache configuration file
sudo bash -c "cat <<EOF > /etc/apache2/sites-available/deskplanner.conf
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot $INSTALL_DIR/webserver

    <Directory $INSTALL_DIR/webserver>
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/deskplanner_error.log
    CustomLog \${APACHE_LOG_DIR}/deskplanner_access.log combined
</VirtualHost>
EOF"

# Disable the default site and enable deskplanner.conf
sudo a2dissite 000-default.conf
sudo a2ensite deskplanner.conf

# Make sure Apache can access (read) the files
sudo chown -R www-data:www-data "$INSTALL_DIR/webserver"
sudo chmod -R 755 "$INSTALL_DIR/webserver"

# Restart Apache to apply changes
echo "Restarting Apache..."
sudo systemctl restart apache2

# 7. Add the current user to the gpio group for GPIO access (requires re-login or reboot)
echo "Adding user '$USER' to the gpio group..."
sudo usermod -a -G gpio "$USER"

echo "Installation complete."
echo "Please log out and log back in (or reboot) for the GPIO group membership to take effect."
