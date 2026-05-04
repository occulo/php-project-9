# Page Analyzer
[![Actions Status](https://github.com/occulo/php-project-9/actions/workflows/hexlet-check.yml/badge.svg)](https://github.com/occulo/php-project-9/actions) [![PHP CI](https://github.com/occulo/php-project-9/actions/workflows/ci.yml/badge.svg)](https://github.com/occulo/php-project-9/actions/workflows/ci.yml) [![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=occulo_php-project-9&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=occulo_php-project-9)

## Description
Page Analyzer is a simple application that provides basic SEO checkup of webpages. It crawls provided URLs and extracts metadata such as title, h1, and meta description.

## Prerequisites
* Linux, MacOS, WSL
* PHP >= 8.3
* Composer >= 2.0
* PostgreSQL >= 16
* Git
* Make

## Installation
### Source
If you wish to install from source, clone the repository to your machine and install all required Composer dependencies with the following set of commands:
```bash
git clone https://github.com/occulo/php-project-9.git
cd php-project-9
make install
```
### Setup
Create a `.env` file in the root of the project and set the database connection URL:
```
# .env
DATABASE_URL=postgresql://user:password@localhost:5432/page_analyzer
```
Initialize the database using PostgreSQL:
```bash
psql -d $DATABASE_URL -f database.sql
```

## Running
To start the application, run:
```bash
make start
```
After this, it will be available at `http://localhost:8000`.

## Demo
A live demo of this app is available at: https://php-project-9-kg20.onrender.com/