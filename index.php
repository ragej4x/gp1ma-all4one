<?php 
session_start();
include 'php/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

// Fetch current user details
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="style/index.css">
</head>
<body>
<div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v21.0"></script>

    <div class="container">
        <div class="left">
            <?php
            // Profile picture logic
            $profile_pic = !empty($user['profile_pic']) && file_exists("php/uploads/" . $user['profile_pic']) 
                           ? "php/uploads/" . htmlspecialchars($user['profile_pic']) 
                           : "php/uploads/default-profile.png"; // Fallback image

            echo "<a href='php/profile.php'><img class='profile' src='$profile_pic' alt='Profile Picture'></a>";
            ?>

            <!-- Display username -->
            <h2 class="name"><?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?></h2>
        </div>

        <div class="middle">
            <h2>Public Announcement</h2>
            <div class="feed-container">
                <div class="fb-page" 
                     data-href="https://www.facebook.com/JonvicRemullaJr" 
                     data-tabs="timeline" 
                     data-small-header="true" 
                     data-adapt-container-width="false" 
                     data-hide-cover="false" 
                     data-width="500px"
                     data-height="650px"
                     data-show-facepile="true">
                    <blockquote cite="https://www.facebook.com/JonvicRemullaJr" class="fb-xfbml-parse-ignore">
                        <a href="https://www.facebook.com/JonvicRemullaJr">Jonvic Remulla</a>
                    </blockquote>
                </div>
            </div>

            
            <div class="feed-container">
                <div class="fb-page" 
                     data-href="https://www.facebook.com/thephoenixadvisory" 
                     data-tabs="timeline"
                     data-width="500px"
                     data-height="650px"
                     data-small-header="true" 
                     data-adapt-container-width="false" 
                     data-hide-cover="false" 
                     data-show-facepile="true">
                    <blockquote cite="https://www.facebook.com/thephoenixadvisory" class="fb-xfbml-parse-ignore">
                        <a href="https://www.facebook.com/thephoenixadvisory">The Phoenix Advisory</a>
                    </blockquote>
                </div>
            </div>
            
        </div>

        <div class="right"></div>


    </div>
</body>
</html>
