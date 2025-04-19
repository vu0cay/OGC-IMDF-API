docker build -t vu0cay/ctu-imdf-platform:latest .
docker push vu0cay/ctu-imdf-platform:latest 
@REM docker-compose down                   
docker pull vu0cay/ctu-imdf-platform:latest 
docker-compose up -d