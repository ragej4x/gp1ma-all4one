<?php
session_start();
include 'db.php'; // Include your database connection file

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth.php');
    exit;
}

// Handle friend request submission
if (isset($_POST['friend_id'])) {
    $user_id = $_SESSION['user_id'];
    $friend_id = $_POST['friend_id'];

    // Check if the friend request already exists
    $stmt = $pdo->prepare("SELECT * FROM friends WHERE user_id = ? AND friend_id = ?");
    $stmt->execute([$user_id, $friend_id]);
    $existing_request = $stmt->fetch();

    if (!$existing_request) {
        // Send a friend request
        $stmt = $pdo->prepare("INSERT INTO friends (user_id, friend_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $friend_id]);
        echo "Friend request sent!";
    } else {
        echo "You have already sent a friend request to this user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users List</title>
</head>
<body>
    <h1>Users</h1>

    <!-- Example of displaying users with add friend option -->
    <?php
    $stmt = $pdo->query("SELECT id, username FROM users");
    while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($user['id'] !== $_SESSION['user_id']) { // Prevent adding self
            echo "<div>";
            echo htmlspecialchars($user['username']);
            echo "<form method='POST' style='display:inline;'>
                    <input type='hidden' name='friend_id' value='" . htmlspecialchars($user['id']) . "'>
                    <button type='submit'>Add Friend</button>
                  </form>";
            echo "</div>";
        }
    }
    ?>
</body>
</html>
