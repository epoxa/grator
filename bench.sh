ab -H "Authorization: Basic QWRlbGU6MQ==" -H "Accept: application/json" -m POST http://127.0.0.1:8880/start >> /dev/null
ab -H "Authorization: Basic QWRlbGU6MQ=="  -H "Accept: application/json" -n 1000 -c 2 http://127.0.0.1:8880/
