ROOT=$(cd "$(dirname "$0")" && pwd)
cd "$ROOT/docker" \
  && DB_DATABASE=grator DB_USERNAME=grator DB_PASSWORD=1 \
  docker-compose up -d \
  && docker-compose exec php sh -c "cd /srv && docker/composer install"
cd "$ROOT" || exit