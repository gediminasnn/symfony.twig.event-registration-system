
# Event Registration System Application

![register](https://github.com/user-attachments/assets/a1d5a98a-3621-430d-a55d-8d02937adf53)

This Event Registration System is built with Symfony. It allows users to register for events through a web interface. This document outlines the steps required to set up the system on your local development environment.

## Prerequisites

Before proceeding with the setup, ensure you have the following installed on your machine:

-   Docker
-   Docker Compose

## Setup

#### 1.  **Clone the Repository**
    
First, clone the repository to your local machine. Open a terminal and run the following command:
    
`git clone git@github.com:gediminasnn/symfony.twig.event-registration-system.git` 
    
(Optional) Replace `git@github.com:gediminasnn/symfony.twig.event-registration-system.git` with the URL of repository.
    
#### 2.  **Navigate to the Application Directory**
    
Change directory to the application root:
    
`cd symfony.twig.event-registration-system` 
    
(Optional) Replace `symfony.twig.event-registration-system` with the path where you cloned the repository.
    
#### 3.  **Prepare the Environment File**
    
Prepare the application's environment file. Locate the `env.example` file in the application root and create a new file named `.env` using it as a template. Optionally, edit the `.env` file to adjust any environment variables specific to your setup.

####  4.  **Start the Docker Containers**
    
Use Docker Compose to start the Docker containers. Run the following command in your terminal:
    
`docker-compose up`
    
This command builds and starts all containers needed for the application. The first time you run this, it might take a few minutes to download and build everything.

#### 5.  **Setup Development Database**

After the Docker environment is up and running, set up the development database by executing the following command:
    
`docker-compose exec php php bin/console doctrine:migrations:migrate`

This command runs the migration files to create the necessary database tables for the application.

#### 6. **Run Database Seeds**

After successfully running the migrations, it's time to populate the database with some initial data, run the database fixtures. Ensure your Docker containers are up and running. In the terminal, execute the following command:

`docker-compose exec php bin/console doctrine:fixtures:load`

This command will execute the fixtures defined in your application, populating the database with sample or default data.
    
#### 7.  **(Optional) Run Tests**
    
Ensure that your Docker containers are still up and running. Open a new terminal window or tab and execute the following command:
    
`docker-compose exec php php bin/phpunit` 
    
This command will use phpunit's built-in test runner to execute your application's test suite. It will run all the tests located in the tests directory of your application.

By completing this step, you will have fully set up your Event Registration System Application on your local development environment, ensuring it is ready for further development, testing, or deployment.

## Pages documentation
### Events List
![list](https://github.com/user-attachments/assets/d1a752de-21cc-4bda-bcf0-ffce964db28f)
### Event Preview
![preview](https://github.com/user-attachments/assets/a97523bf-8fe4-4206-85ec-313349a94eeb)
### Event Registration
![register](https://github.com/user-attachments/assets/a1d5a98a-3621-430d-a55d-8d02937adf53)
### Event Registration Success
![success](https://github.com/user-attachments/assets/14390373-b257-4b01-8721-7bf623e4c3d7)
### Event Registration Error
![error](https://github.com/user-attachments/assets/bd7d3cc4-5c9d-44e0-b604-041103ff7e2a)

## Entity Relationship Diagram
![erdiagram](https://github.com/user-attachments/assets/fb821666-3a4f-4c55-863c-4fa1e1ca3b55)

## License

This project is licensed under the MIT License
