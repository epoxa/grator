ROOT=$(cd "$(dirname "$0")" && pwd)
cd "$ROOT/docker" \
  && docker-compose exec db sh -c "MYSQL_PWD=1 mysql -ugrator grator"
cd "$ROOT" || exit