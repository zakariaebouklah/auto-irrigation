# Project Setup Guide

This guide provides step-by-step instructions on how to set up and run the "pfa-backend" project. Please follow the instructions below to get started.

## Prerequisites
- Git
- Docker
- Docker Compose

## Setup Instructions

### 1. Clone the projects
Clone the project repositories `IN THE SAME DIRECTORY`. Open your terminal or command prompt and run the following commands:

```bash
git clone <pfa-symfony-backend-repo-url>
git clone <python-fastAPI-calculation-repo-url>
```

Replace `<pfa-symfony-backend-repo-url>` and `<python-calculation-repo-url>` with the actual URLs from GitHub repositories.

### 2. Copy the environment file
Navigate to the "pfa-backend" project directory:

```bash
cd pfa-fastAPI
```

Make a copy of the `.env.example` file and rename it to `.env`:

```bash
cp .env.example .env
```

Open the `.env` file using a text editor and make sure to set the necessary environment variables, including the `WEATHER_API_KEY` value.

You can get your own API key from this website: `https://www.tomorrow.io/`

### 3. Start the application

Navigate to the "pfa-symfony-backend" project directory:

```bash
cd pfa-symfony-backend
```

Start the application using Docker Compose. In the terminal, run the following command:

```bash
docker-compose up -d
```

This command will download the necessary Docker images and start the containers in the background. The `-d` flag is used to run the containers in detached mode.

Wait for the process to complete. Once it's done, you can proceed to the next steps.

### 4. Run database migrations
Access the running "pfa-backend" container in interactive mode using the following command:

```bash
docker exec -it php sh
```

Once inside the container, run the following commands to apply database migrations:

```bash
php bin/console d:m:m
```

### 5. Generate JWT keypair
While still inside the "pfa-backend" container, generate the JWT keypair using the following command:

```bash
php bin/console lexik:jwt:generate-keypair
```

### 6. Configure crontab (for production)
In a production environment, you need to configure a cron job to run the Python script periodically. To do this, follow these steps:

- Edit the crontab file using a text editor:

```bash
crontab -e
```

- Add the following line to the crontab file:

```bash
0 7 * * * docker exec -it python /usr/local/bin/python /py-app/calculation/cron.py
```

This configuration will run the Python script every minute. Adjust the cron schedule as needed.

- Save and exit the crontab file.
