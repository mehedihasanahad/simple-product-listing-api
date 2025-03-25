# Simple Product Listing API - Setup Guide

## Table of Contents

1. [Clone the Repository](#clone-the-repository)
2. [Build and Run Docker Containers](#build-and-run-docker-containers)
3. [Access the Container](#access-the-container)
4. [Run Database Migrations](#run-database-migrations)
5. [Run Unit Tests](#run-unit-tests)
6. [Generate API Documentation](#generate-api-documentation)
7. [Access the Application](#access-the-application)

## Clone the Repository

First, clone the repository to your local machine:

- git clone https://github.com/mehedihasanahad/simple-product-listing-api.git
- cd simple-product-listing-api

## Build and Run Docker Containers

Please, run docker before execute below command:

- docker compose up --build -d

## Access the container
- docker exec -it laravel-app sh

## Run Database Migrations

- php artisan migrate
- php artisan db:seed

## Test the Application

- php artisan test

## Generate API Documentation

- php artisan l5-swagger:generate

## Access the Application

- http://localhost:8000

- http://localhost:8000/api/documentation

- http://localhost:8000/docs?api-docs.json



## Design explanation are given below as per requirement:

For this project, I followed REST API principles to ensure a standardized approach. I have implemented the Repository Design Pattern to separate business logic from data access, promoting cleaner, more maintainable code. This pattern enhances modularity, making the application easier to test and extend, while also maintaining a clear structure that can easily accommodate future changes.