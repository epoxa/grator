ROOT=$(cd $(dirname $0) && pwd)
cd "$ROOT/docker" \
  && docker-compose exec php sh -c "cd /srv && vendor/bin/phpunit test" \
  && cd "$ROOT"