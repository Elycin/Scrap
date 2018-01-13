# Scrap
A high performance file upload solution for ShareX.

## Requirements
Scrap is powered by `php7.2`, `redis-server` and `mysql-server`.  
You must have these installed on your system in order for scrap to work.

If you tweak your memory limit on memcached, you can also use that if specified in the `.env`

## Features
- User authentication
- In memory caching for files under `FILE_CACHE_THRESHOLD` bytes in size
- Adjustable caching times under `APP_CACHE_TIME`
- Duplication checking (duplicated files are aliased)
- Native Illuminate\Laravel Encryption Facade usage (AES-256-CBC)
- Proper HTTP Responses (200, 201, 400, 403, 404, 419, 500, 503)
- Dynamic Protocol Detection (Will automatically return HTTPS or HTTP based on POST method)
- CloudFlare Reverse Proxy support

<sub><sup>any highlighted text in this section can be found in the .env</sup></sub>

## Installation
```bash
$ git clone https://github.com/Elycin/Scrap
$ cd Scrap
$ composer install
$ cp .env.example .env
# At this point, please edit the .env and configure the database with MySQL. 
$ php artisan key:generate
$ php artisan migrate
```

Want a single threaded ready to go instance rather than configuring a webserver?  
run: `php artisan serve --host 0.0.0.0 --port 8000`

## How to use
By default, the upload function in the applications controller looks for the fields `username` and `password`.  
In ShareX, you should be able to specify a custom domain such as `http://$YOUR_DOMAIN/upload` with the `POST` Parameters as follows:
- `username` - Your application username
- `password` - Your password
- `file` - The uploaded file (also known as the "File form upload" field)

Additionally, you can provide a `encrypt` parameter if you wish for the files to be encrypted on the disk where the application runs.

## Cache Server Usage
The use of redis is recommended, and more so required for this application.
Redis works by storing data in the memory of your server and Scrap will dump data into it occasionally for faster access times, this is so that data will not be read from the disk if your server is in high demand.

You may change redis to `memcached` if you configure the memory limits on your system.  
You can adjust the time for a file to remain in the cache by adjusting the `FILE_CACHE_THRESHOLD` variable in the `.env`


## Automatic File Deletion
Scrap has the ability via the form of a cronjob every hour or console command via artisan to automatically delete files older than `DAYS_TO_STORE` in the `.env`

To set up automatic scheduling, add this line to your crontab:
```bash
* * * * * php /path/to/scrap/artisan schedule:run >> /dev/null/2>&1
```

Or you may choose to manually run the cleanup by:
```bash
$ php artisan clean:files
```
