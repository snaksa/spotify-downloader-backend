This repo contains a microservice that is responsible for running the core API connecting to Spotify and Youtube.

## Tech Stack
- **[Symfony5](https://symfony.com)**
- **[Docker](https://www.docker.com)**

### Requirements
- **[PHP 7.4 or higher](https://www.php.net)**
- **[Docker](https://www.docker.com)**
- **[Composer](https://getcomposer.org)**

### Prerequisites
- **[Spotify project and API key](https://developer.spotify.com)**
- **[Youtube project and API key](https://console.cloud.google.com)**

### Installation
Clone the GitHub repository
```bash
git clone git@github.com:snaksa/spotify-downloader-backend.git
cd spotify-downloader-backend
```

Build docker image or use the public one from **[DockerHub](https://hub.docker.com/u/snaksa)**
```bash
docker build -t snaksa/spotify-downloader-backend .
```

Docker Compose configuration to run the project
```yaml
version: "3"

services:
  app:
    image: snaksa/spotify-downloader-backend
    build:
      context: .
    container_name: spotify-backend
    env_file:
      - .env
    ports:
      - "8080:80"
``` 

### Environment variables
The application relies on several environment variables for its configuration.
- `SPOTIFY_CLIENT_ID` - Spotify project client ID 
- `SPOTIFY_CLIENT_SECRET` - Spotify project client secret key
- `SPOTIFY_REDIRECT_URL` - Redirect URL after Spotify authentication 
- `SPOTIFY_API_BASE_URL` - Spotify API base URL (https://api.spotify.com)
- `SPOTIFY_ACCOUNTS_BASE_URL` - Spotify authentication base URL (https://accounts.spotify.com)
- `YOUTUBE_API_BASE_URL` - Youtube API base URL (https://www.googleapis.com)
- `YOUTUBE_CLIENT_IDS` - Youtube project API keys. Can be concatenated to use more than one project after the API quota is reached with `_APPEND_`. E.g. key1_APPEND_key2_APPEND_key3 

### Running the tests
Inside the container run PHPUnit
```bash
docker exec -it spotify-backend bash
./bin/phpunit
```

### UI
You can run a ReactJS UI implemented in the following repository **[spotiy-downloader-frontend](https://github.com/snaksa/spotify-downloader-frontend)**. 
