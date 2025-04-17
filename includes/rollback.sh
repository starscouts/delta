#!/bin/bash
touch /app/maintenance
rm -rf /app-legacy
cp -rv /app /app-legacy
systemctl stop nginx.service
rm -rf /app/*
cp -rv /app-snapshot/* /app
rm /app/maintenance
systemctl start nginx.service