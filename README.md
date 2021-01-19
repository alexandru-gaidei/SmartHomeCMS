PHP tool for management of sensors (HTTP get and post) and IP cameras (RTSP).

## Setup

### Requirements

- https://laravel.com/docs/7.x#server-requirements
- php >= 7.3
- mysql
- curl
- redis-server
- ffmpeg tool (libx264)

### General

- complete `.env` file
- run `npm install`
- run `composer install`
- run `passport install`
- run `php artisan migrate`
- add new `crontab` with content:  
`* * * * * cd /project/path && php artisan schedule:run >> /dev/null 2>&1`

### Sensors

- setup supervisors from `_supervisors` folder.

### IP Camera

- install `ffmpeg` tool
- after cameras creation or updates, restarts cam servers:  
`./cam-servers.sh restart`
- add permissions for php user to access(remove) `/project/path/public/_cam` files or add new `crontab` as root with content:  
` * * * * * chmod -R 777 /project/path/public/_cam`