echo "================================================================================="
echo "                           !!! ATTENTION !!!"
echo "For better real life simulation we will start two concurrent threads."
echo "The first continuously starts new game, and the second accepts any prize offered."
echo "================================================================================="
echo ""

# Reset
printf "Restarting..."
curl -s -H "Authorization: Basic QWRlbGU6MQ==" -H "Accept: application/json" -X POST http://127.0.0.1:8880/restart > /dev/null
echo "done"

ab -H "Authorization: Basic QWRlbGU6MQ==" -H "Accept: application/json" -n 1000 -m POST http://127.0.0.1:8880/start > /dev/null &
ab -H "Authorization: Basic QWRlbGU6MQ=="  -H "Accept: application/json" -n 1000 -m POST http://127.0.0.1:8880/accept
