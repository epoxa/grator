ROOT=$(cd "$(dirname "$0")" && pwd)
if [[ $1 ]]
then
  filter="--filter=$1"
fi
cd "$ROOT/docker" \
  && docker-compose exec php sh -c "cd /srv && vendor/bin/phpunit $filter" \
  && cd "$ROOT" || exit