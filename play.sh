ROOT=$(cd "$(dirname "$0")" && pwd)
cd "$ROOT/docker" \
  && docker-compose exec php sh -c "cd /srv && php console/play.php" \
  && cd "$ROOT" || exit