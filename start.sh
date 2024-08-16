#!/bin/bash

# Print environment variables
echo "Environment variables:"
env

# Check if .env file exists
if [ -f /var/www/html/.env ]; then
    echo ".env file exists"
    cat /var/www/html/.env
else
    echo ".env file does not exist"
fi

# Check PHP version
php -v

# Check if required PHP extensions are installed
php -m

# Try to connect to the database
php -r "
\$host = '${DB_SERVERNAME}';
\$db   = '${DB_NAME}';
\$user = '${DB_USER}';
\$pass = '${DB_PASS}';

echo \"Attempting to connect to database: \$host, \$db, \$user\n\";

try {
    \$mysqli = new mysqli(\$host, \$user, \$pass, \$db);
    if (\$mysqli->connect_errno) {
        throw new Exception(\$mysqli->connect_error);
    }
    echo \"Database connection successful\n\";
    \$mysqli->close();
} catch (Exception \$e) {
    echo \"Database connection failed: \" . \$e->getMessage() . \"\n\";
}
"

# Start Apache in foreground
apache2-foreground
