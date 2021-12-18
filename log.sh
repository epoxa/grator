ROOT=$(cd "$(dirname "$0")" && pwd)
cd "$ROOT/docker" \
  && docker-compose logs -f php
cd "$ROOT" || exit