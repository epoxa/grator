ROOT=$(cd "$(dirname "$0")" && pwd)
cd "$ROOT/docker" \
  && docker-compose exec db sh -c "MYSQL_PWD=1 mysqldump -y -ugrator grator" > "$ROOT/docker/db_data/database.sql" \
  && echo "Database dumped successfully" || echo "Database dump error"
cd "$ROOT" || exit