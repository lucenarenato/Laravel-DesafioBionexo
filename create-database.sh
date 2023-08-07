#!/usr/bin/env bash

mysql --user=root --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS bionexo;
    GRANT ALL PRIVILEGES ON \`bionexo%\`.* TO '$MYSQL_USER'@'%';
EOSQL
