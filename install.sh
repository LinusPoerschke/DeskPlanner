#!/bin/bash
# install.sh – Ensures that our DeskPlanner program runs correctly

# Exit the script if any command fails
set -e

# Function to display informational messages in green
function echo_info {
    echo -e "\e[32m$1\e[0m"
}

# Function to display warning messages in yellow
function echo_warn {
    echo -e "\e[33m$1\e[0m"
}

# Function to display error messages in red
function echo_error {
    echo -e "\e[31m$1\e[0m"
}

# 1) System Update (Optional but Recommended)
echo_info "Do you want to update the system? (y/n)"
read UPDATE_SYS
if [[ "$UPDATE_SYS" =~ ^[Yy]$ ]]; then
    echo_info "Updating system packages..."
    sudo apt-get update -y
    sudo apt-get upgrade -y
else
    echo_info "System update skipped."
fi

# 2) Install Dependencies
echo_info "Installing required dependencies..."
sudo apt-get install -y python3 python3-pip git

# (Optional) Install pigpio
echo_info "Do you want to install pigpio? (y/n)"
read INSTALL_PIGPIO
if [[ "$INSTALL_PIGPIO" =~ ^[Yy]$ ]]; then
    echo_info "Installing pigpio..."
    sudo apt-get install -y pigpio python3-pigpio
else
    echo_info "pigpio installation skipped."
fi

# 3) Add Users to gpio Group
echo_info "Adding users to gpio group..."

# Determine the current user
CURRENT_USER=$(whoami)

# Add the current user to gpio group
sudo adduser "$CURRENT_USER" gpio

# Add the Webserver user (www-data) to gpio group
sudo adduser www-data gpio

echo_info "Users have been added to gpio group."

# 4) Configure sudoers to Allow www-data to Execute send Script Without Password
echo_info "Configuring sudoers to allow www-data to execute the send script without a password..."

# Path to the send script
SEND_SCRIPT_PATH="/home/pi/DeskPlanner/webserver/sensors/raspberry-remote/send"

# Ensure the send script exists and is executable
if [ -f "$SEND_SCRIPT_PATH" ]; then
    sudo chmod +x "$SEND_SCRIPT_PATH"
else
    echo_error "Send script not found at $SEND_SCRIPT_PATH"
    exit 1
fi

# Add sudoers rule if not already present
if ! sudo grep -q "^www-data ALL=(ALL) NOPASSWD: $SEND_SCRIPT_PATH" /etc/sudoers; then
    echo "www-data ALL=(ALL) NOPASSWD: $SEND_SCRIPT_PATH" | sudo tee -a /etc/sudoers
    echo_info "Sudoers rule added for www-data to execute send script without a password."
else
    echo_info "Sudoers rule for www-data already exists."
fi

# 5) Set Ownership and Permissions for the Webserver Directory
echo_info "Setting ownership and permissions for the webserver directory..."

# Define the webserver directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
WEB_DIR="$SCRIPT_DIR/webserver"

# Change owner and group to www-data:www-data recursively
sudo chown -R www-data:www-data "$WEB_DIR"

# Set directory permissions to 750 (rwxr-x---)
sudo find "$WEB_DIR" -type d -exec chmod 750 {} \;

# Set file permissions to 640 (rw-r-----)
sudo find "$WEB_DIR" -type f -exec chmod 640 {} \;

# Specifically, set execution permissions for Python scripts and send
sudo chmod +x "$WEB_DIR/sensors/socket.py"
sudo chmod +x "$WEB_DIR/sensors/raspberry-remote/send"
sudo chmod +x "$WEB_DIR/sensors/LED.py"
sudo chmod +x "$WEB_DIR/sensors/temperature.py"

echo_info "Ownership and permissions for the webserver directory have been set."

# 6) Set Permissions for user_data Directory
echo_info "Setting permissions for the user_data directory..."

USER_DATA_DIR="$WEB_DIR/user_data"

# Ensure the user_data directory exists
if [ ! -d "$USER_DATA_DIR" ]; then
    sudo mkdir -p "$USER_DATA_DIR"
    echo_info "Created user_data directory."
fi

# Set ownership and permissions
sudo chown -R www-data:www-data "$USER_DATA_DIR"
sudo chmod -R 750 "$USER_DATA_DIR"

echo_info "Permissions for the user_data directory have been set."

# 7) Create users.txt if it Doesn't Exist and Set Permissions
echo_info "Setting up users.txt..."

USERS_FILE="$WEB_DIR/users.txt"

if [ ! -f "$USERS_FILE" ]; then
    echo_info "Creating users.txt with empty content."
    sudo bash -c "echo '' > '$USERS_FILE'"
fi

# Set ownership and permissions
sudo chown www-data:www-data "$USERS_FILE"
sudo chmod 660 "$USERS_FILE"

echo_info "users.txt has been set up with appropriate permissions."

# 8) Rename or Remove index.html to Prioritize index.php
echo_info "Renaming/removing index.html to ensure index.php is used as the default page..."

INDEX_HTML="$WEB_DIR/index.html"
INDEX_PHP="$WEB_DIR/index.php"

if [ -f "$INDEX_HTML" ]; then
    sudo mv "$INDEX_HTML" "$WEB_DIR/index_backup.html"
    echo_info "index.html has been renamed to index_backup.html"
else
    echo_info "index.html does not exist. No action needed."
fi

# 9) Ensure socket.log Exists and Is Writable
echo_info "Setting up socket.log..."

SOCKET_LOG="$WEB_DIR/sensors/socket.log"

if [ ! -f "$SOCKET_LOG" ]; then
    sudo touch "$SOCKET_LOG"
    echo_info "Created socket.log"
fi

sudo chown www-data:www-data "$SOCKET_LOG"
sudo chmod 660 "$SOCKET_LOG"

echo_info "socket.log has been set up with appropriate permissions."

# 10) Restart Webserver to Apply Changes
echo_info "Restarting the webserver to apply changes..."
sudo systemctl restart apache2  # Uncomment if using Apache
# sudo systemctl restart nginx    # Uncomment if using Nginx

echo_info "Webserver has been restarted."

# 11) Enable and Start pigpiod Service if pigpio Was Installed
if [[ "$INSTALL_PIGPIO" =~ ^[Yy]$ ]]; then
    echo_info "Enabling and starting pigpiod service..."
    sudo systemctl enable pigpiod
    sudo systemctl start pigpiod
    echo_info "pigpiod service has been enabled and started."
fi

# Completion Message
echo_info "Installation completed successfully."
echo_warn "Please log out and log back in (or reboot the Pi) for group changes to take effect."
