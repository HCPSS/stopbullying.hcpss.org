#!/usr/bin/env bash
set -e

# Remove local database files
docker run --rm \
    --volume $(pwd):/app  \
    ubuntu bash -c "rm -rf /app/.data /app/drupal.sql"

# Find the latest backup
BACKUP_FILE=$(
    docker run \
        --rm \
        --volume ~/.aws:/root/.aws \
        banderson/aws \
        aws s3 ls hcpss.web.backups/stopbullying/ | tail -n 1 | awk '{print $NF}'
)

mkdir -p ./backup

# Download the latest backup
docker run \
    --rm \
    --volume ~/.aws:/root/.aws \
    --volume $(pwd)/backup:/app \
    banderson/aws \
    aws s3 cp s3://hcpss.web.backups/stopbullying/$BACKUP_FILE /app/$BACKUP_FILE

docker run --rm --volume $(pwd)/backup:/app \
    ubuntu bash -c "cd /app && tar -xzf ./$BACKUP_FILE"

docker run --rm \
    --volume $(pwd):/app \
    ubuntu bash -c "mv /app/backup/drupal.sql /app/drupal.sql && rm -rf /app/backup"