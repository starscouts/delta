#!/bin/bash
touch /app/maintenance
touch /app-staging/maintenance
rm -rf /app-snapshot
cp -rv /app /app-snapshot
cp -rv /app/includes/data /tmp/delta-data
cp -rv /app/uploads /tmp/delta-uploads
cp -rv /app-staging/* /app
rm -rf /app/includes/data/*
cp -rv /tmp/delta-data/* /app/includes/data
rm -rf /tmp/delta-data
rm -rf /app/uploads/*
cp -rv /tmp/delta-uploads/* /app/uploads
rm -rf /tmp/delta-uploads
chmod -Rfv 777 /app-staging /app
rm /app/maintenance
rm /app-staging/maintenance