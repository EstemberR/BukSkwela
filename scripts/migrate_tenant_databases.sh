#!/bin/bash

# Script to migrate tenant databases one by one with clean connections
# This is useful if you're having connection issues with the regular artisan commands

# Get the application root directory
APP_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/.."
cd $APP_DIR

# Get list of tenant IDs
TENANT_IDS=$(php artisan tinker --execute="echo implode(',', App\Models\Tenant::pluck('id')->toArray());")

# Remove quotes if present
TENANT_IDS=${TENANT_IDS//\"/}
TENANT_IDS=${TENANT_IDS//\'/}

echo "Found tenants: $TENANT_IDS"
echo "Starting migration for each tenant..."

# Convert comma-separated string to array
IFS=',' read -ra TENANT_ARRAY <<< "$TENANT_IDS"

# Counter for progress
TOTAL=${#TENANT_ARRAY[@]}
CURRENT=1

# Process each tenant
for TENANT_ID in "${TENANT_ARRAY[@]}"
do
    echo "[$CURRENT/$TOTAL] Processing tenant: $TENANT_ID"
    
    # Run migration with new PHP process to ensure clean connection
    php artisan tenants:migrate --tenant=$TENANT_ID --force
    
    # Optional: seed the database too
    php artisan tenants:seed --tenant=$TENANT_ID --force
    
    echo "Completed tenant: $TENANT_ID"
    echo "Sleeping for 5 seconds before next tenant..."
    
    # Sleep to ensure connections are closed
    sleep 5
    
    # Increment counter
    CURRENT=$((CURRENT+1))
done

echo "All tenant migrations completed!"
