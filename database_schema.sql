CREATE DATABASE social_media;

USE social_media;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE tweets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE follows (
    follower_id INT NOT NULL,
    followed_id INT NOT NULL,
    PRIMARY KEY (follower_id, followed_id),
    FOREIGN KEY (follower_id) REFERENCES users(id),
    FOREIGN KEY (followed_id) REFERENCES users(id)
);

CREATE TABLE likes (
    user_id INT NOT NULL,
    tweet_id INT NOT NULL,
    PRIMARY KEY (user_id, tweet_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (tweet_id) REFERENCES tweets(id)
);


-- Insert sample data into users table
INSERT INTO users (username, password) VALUES 
('john_doe', 'password123'),
('jane_smith', 'password456'),
('alice_johnson', 'password789'),
('bob_brown', 'password000');

-- Insert sample data into tweets table
INSERT INTO tweets (user_id, content, image) VALUES
(1, 'Hello world! This is my first tweet.', 'bts.jpg'),
(2, 'Good Morning', 'coffee.jpg'),
(1, 'Just finished a great book!', 'book.jpg'),
(3, 'Loving the new features in the app.', NULL);

-- Insert sample data into follows table
INSERT INTO follows (follower_id, followed_id) VALUES
(1, 2),
(1, 3),
(2, 4),
(3, 1);

-- Insert sample data into likes table
INSERT INTO likes (user_id, tweet_id) VALUES
(1, 2),
(2, 1),
(3, 4),
(4, 3);