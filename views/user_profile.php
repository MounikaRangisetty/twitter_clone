<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="page-title"></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .following-list {
            display: none;
            margin-top: 20px;
        }
        .following-list p {
            margin: 5px 0;
        }
        .tweet {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }
        .tweet img {
            max-width: 100%;
        }
        .tweet span {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1 id="profile-username"></h1>
        <nav>
            <a href="../index.php">Home</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
   
    <div class="container">
        <br>
        <div class="profile-stats">
            <p><strong>Followers:</strong> <span id="follower-count"></span></p>
            <p>
                <a href="#" onclick="toggleFollowingList(); return false;" style="text-decoration:none">
                    <strong>Following:</strong> <span id="following-count"></span>
                </a>
            </p>
            <p><strong>Posts:</strong> <span id="post-count"></span></p>
        </div>

        <!-- Following Users List -->
        <div class="following-list" id="following-list">
            <h2>Following Users</h2>
            <div id="following-users"></div>
        </div>

        <h2>Your Tweets</h2>
        <div id="tweets"></div>
    </div>

    <script>
        function fetchData() {
            fetch('../user_profile.php')
                .then(response => response.json())
                .then(data => {
                    // Update the page title and header
                    document.getElementById('page-title').innerText = `${data.username}'s Profile`;
                    document.getElementById('profile-username').innerText = `${data.username}'s Profile`;

                    // Update profile stats
                    document.getElementById('follower-count').innerText = data.follower_count;
                    document.getElementById('following-count').innerText = data.following_count;
                    document.getElementById('post-count').innerText = data.post_count;

                    // Update following users list
                    const followingList = document.getElementById('following-users');
                    data.following_users.forEach(user => {
                        const userDiv = document.createElement('div');
                        userDiv.className = 'user-profile';
                        userDiv.innerHTML = `<p><a href="./profile.php?username=${user.username}"><strong>${user.username}</strong></a></p>`;
                        followingList.appendChild(userDiv);
                    });

                    // Update tweets
                    const tweetsContainer = document.getElementById('tweets');
                    if (data.tweets.length > 0) {
                        data.tweets.forEach(tweet => {
                            const tweetDiv = document.createElement('div');
                            tweetDiv.className = 'tweet';
                            tweetDiv.innerHTML = `
                                ${tweet.image ? `<img src="../images/${tweet.image}" alt="Tweet Image">` : ''}
                                <p>${tweet.content}</p>
                                <span>${tweet.likes_count} Likes</span>
                                <a href="../like.php?tweet_id=${tweet.id}">
                                    <i class="fa${tweet.is_liked ? 's' : 'r'} fa-thumbs-up"></i>
                                </a>
                            `;
                            tweetsContainer.appendChild(tweetDiv);
                        });
                    } else {
                        tweetsContainer.innerHTML = '<p>No tweets to show.</p>';
                    }
                });
        }

        function toggleFollowingList() {
            var list = document.getElementById('following-list');
            list.style.display = list.style.display === 'none' ? 'block' : 'none';
        }

        // Fetch data when the page loads
        window.onload = fetchData;
    </script>
</body>
</html>
