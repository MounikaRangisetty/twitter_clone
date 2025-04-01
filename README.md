# Social media clone TWEET

## Table of Contents
1. [Introduction](#introduction)
2. [System Requirements](#system-requirements)
3. [Setup and Installation](#setup-and-installation)
4. [Features and Functionality](#features-and-functionality)
5. [Screenshots and Explanations](#screenshots-and-explanations)
6. [Directory Structure](#directory-structure)
7. [Troubleshooting](#troubleshooting)
8. [Conclusion](#conclusion)

## Introduction
This project is a database-backed clone of social media platforms like Twitter and Parler. Built using PHP and MySQL, it allows users to create accounts, post tweets, follow other users, and like tweets. The goal of this project is to replicate key functionalities of a social media platform in a simplified manner.

## System Requirements
To run this project, you need:
- Web Server: Apache or Nginx
- Database: MySQL
- Programming Language: PHP (version 7.4 or higher recommended)
- Tools: NetBeans for development, Git for version control

## Setup and Installation
1. Clone the Repository  
    First, clone the project repository from GitHub:  
    ```bash
    git clone https://github.com/MounikaRangisetty/Social_media_clone_TWEET.git
    ```
2. Configure the Database
    a. Create the Database
    ```sql
    Log in to MySQL
    Create a new database:
    CREATE DATABASE social_media;
    USE social_media;

    Import the schema from database_schema.sql (included in the repository):
    ```
    b. Update Database Configuration  
    Edit `config.php` to match your database credentials.
    ```php
    <?php
    $servername = "localhost";
    $username = "root"; // Change as needed
    $password = ""; // Change as needed
    $dbname = "social_media";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    ?>
    ```

## Features and Functionality
1. User Authentication
    - Login: Users can log in with their username and password.
    - Registration: Users can register a new account by providing a username and password.
2. User Profile
    - View Profile: Users can view their own profile or other users' profiles, which shows their tweets and stats.
    - Follow/Unfollow: Users can follow or unfollow other users directly from their profile page.
3. Tweet Management
    - Create Tweet: Users can create new tweets with optional image uploads.
    - View Tweets: Users can see tweets from the users they follow.
    - Like Tweets: Users can like tweets to show their appreciation.
4. Additional Features
    - Show Following: Users can view lists of the users they are following.

## Screenshots and Explanations
1. Login Screen  
    Description: The login page where users enter their credentials to access their account.
   
    ![Login Screen](https://github.com/user-attachments/assets/9381233e-1bbc-42b1-92d1-8ad167de76a6)
    - Username Field: Input field where users enter their username.
    - Password Field: Input field where users enter their password.
    - Login Button: Submits the login form.

2. Registration Screen  
    Description: Allows new users to create an account.
   
    ![Registration Screen](https://github.com/user-attachments/assets/edb6c467-3863-4240-977a-eb6e39e9a0ad)
    - Username Field: Input field for the new user's username.
    - Password Field: Input field for the new user's password.
    - Register Button: Submits the registration form.

3. User Profile  
    Description: Displays user profile information including statistics and tweets.
   
    ![User Profile](https://github.com/user-attachments/assets/4bea3c1d-d20d-43c6-a260-648fc00a565c)
    - Profile Stats: Shows the number of followers, following, and posts.
    - Follow/Unfollow Button: Allows users to follow/unfollow the profile owner.
    - Tweets Section: Displays the user's tweets with options to like.

4. Create Tweet  
    Description: Form for creating a new tweet.
   
    ![Create Tweet](https://github.com/user-attachments/assets/ffe083b5-dfb5-44cf-aec4-fb76e9d3a391)
    - Tweet Content: Textarea for entering the tweet content.
    - Image Upload: Option to upload an image with the tweet.
    - Tweet Button: Submits the tweet.

5. Tweets Feed  
    Description: Displays tweets from users the current user follows.
   
    ![Tweets Feed](https://github.com/user-attachments/assets/88da0eec-a94b-439e-a9c8-51e80fd0fafb)
    - Tweet Content: Shows the content of each tweet.
    - Like Button: Allows users to like a tweet.

6. Follow/Unfollow List  
    Description: Shows suggested users that the current user might want to follow.
   
    ![Follow Unfollow List](https://github.com/user-attachments/assets/072779b7-f062-461f-aa6c-6e3ba8e37f0f)
    - Username: Displays suggested usernames.
    - View Button: Links to the profile page of the suggested user.
      
7. Error Handling  
    Description: Displays an error message when something goes wrong.
   
    ![Error Handling](https://github.com/user-attachments/assets/05e1fa5f-d981-4439-afa6-b9ed5116b2da)
    - Back to home: Button to navigate to the home page when something goes wrong.

## Directory Structure 
![Directory Structure](https://github.com/user-attachments/assets/c1382ad1-e71f-4897-9c4b-32d8f27ace13)
    

## Troubleshooting
Common Issues
- Database Connection Errors: Ensure `config.php` has the correct database credentials.
- File Upload Errors: Verify permissions on the `images/` directory and file upload settings in `php.ini`.
- PHP Errors: Check the PHP error log for detailed error messages.

Tips
- Ensure Permissions: Make sure your web server has write permissions for the `images/` directory.
- Check Logs: Review server and PHP logs for troubleshooting.

## Conclusion
This project offers a basic yet functional social media platform clone. The documentation provided here outlines the setup, features, and detailed explanations of each component. For further development, consider adding features such as direct messaging or advanced user analytics.
