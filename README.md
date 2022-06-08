# articles-platform
Main site articles-platform

# Install

1. git clone https://github.com/asidorov72/articles-platform.git
2. composer install
3. create database and set it in the .env file
4. in terminal run: symfony console make:migration
5. in terminal run: symfony console doctrine:migrations:migrate
6. symfony server:start
7. git clone https://github.com/asidorov72/articles-platform-api.git
8. modify api's .env file
9. symfony server:start

# Api registration URL (localhost)
http://articles-api/api/register

# Upload directory
chmod -R 777 /public/images

<project_dir>/public/images

# Migrations
<project_dir>/migrations

# Logger directory

chmod -R 777 <project_dir>/log

<project_dir>/log

