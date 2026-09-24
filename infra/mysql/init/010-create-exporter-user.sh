#!/bin/sh
set -eu

password="$(tr -d '\r\n' < /run/secrets/mysql_exporter_password)"
case "$password" in
  ''|*[!a-f0-9]*) echo 'mysql_exporter_password must be a non-empty lowercase hexadecimal value.' >&2; exit 1 ;;
esac

mysql --protocol=socket -uroot -p"${MYSQL_ROOT_PASSWORD}" <<SQL
CREATE USER IF NOT EXISTS 'prom_exporter'@'%' IDENTIFIED BY '${password}';
GRANT PROCESS, REPLICATION CLIENT, SELECT ON *.* TO 'prom_exporter'@'%';
FLUSH PRIVILEGES;
SQL
