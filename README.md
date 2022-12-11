<p align="center"><img src="https://scontent.fvte2-3.fna.fbcdn.net/v/t39.30808-6/318962272_6289331561126266_7992790271398857011_n.jpg?_nc_cat=103&ccb=1-7&_nc_sid=5cd70e&_nc_eui2=AeGd0Fqrw466WCubdhG5srzxhzwIUCOyCxuHPAhQI7ILGxrWgPMJfmimMRGjWN5ooKB8ARW8Lsw9sfdtoB6VdGEc&_nc_ohc=G_TBP9f-bzQAX8WKqJU&_nc_ht=scontent.fvte2-3.fna&oh=00_AfCvhr9Bchjs5AvmANHMtbuihYKnXB3VoeX1qBBfaXhstQ&oe=6399CE11" width="400" alt="work fun Logo"></p>

> ### WorkFun is an application that helps new employees feel like they fit well into the organization, encourages them to communicate, and provides feedback to the team in a challenging style. A leader can keep track of the heat score (the average difficulty level of the challenge for each employee) and achievement score.

----------

# Getting started

## Installation

In this project we use Laravel framework. Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/9.x/installation)

Clone the repository

    git clone https://github.com/Sjoedy/WorkFunBackEnd.git

Switch to the repo folder

    cd WorkFunBackEnd

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate

Generate a new Passport authentication secret key

    php artisan passport:install

Copy Password grant client id and client secret to .env

    ISSUE_TOKEN_URL=/oauth/token
    PASSPORT_PERSONAL_GRANT_CLIENT_ID=2
    PASSPORT_PERSONAL_GRANT_SECRET=tf9xYETQKR5DNaRaraBhQaVoyS92Or7JCpY0GofN

Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Start the local development server

    php artisan serve

You can now access the server at http://localhost:8000

The api can be accessed at [http://localhost:8000/api](http://localhost:8000/api).
