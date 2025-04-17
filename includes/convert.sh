#!/bin/bash

cd /app/uploads
for i in *.jpg; do
  echo $i
  convert -resize "1080x1080>" -quality 75 $i $(basename $i .jpg).webp
done