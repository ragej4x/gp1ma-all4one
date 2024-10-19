<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


include 'db.php'; // Include your database connection file

session_start();
// Start session
// Redirect if the user is not logged in
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
        $successMessage = "Friend request sent!";
    } else {
        $errorMessage = "You have already sent a friend request to this user.";
    }
}

// Handle accepting friend request
if (isset($_POST['accept_friend_id'])) {
    $user_id = $_SESSION['user_id'];
    $friend_id = $_POST['accept_friend_id'];

    // Update the friend request status to 'accepted'
    $stmt = $pdo->prepare("UPDATE friends SET status = 'accepted' WHERE user_id = ? AND friend_id = ?");
    $stmt->execute([$friend_id, $user_id]);
    $successMessage = "Friend request accepted!";
}

// Handle removing a friend
if (isset($_POST['remove_friend_id'])) {
    $user_id = $_SESSION['user_id'];
    $friend_id = $_POST['remove_friend_id'];

    // Delete the friend record
    $stmt = $pdo->prepare("DELETE FROM friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)");
    $stmt->execute([$user_id, $friend_id, $friend_id, $user_id]);
    $successMessage = "Friend removed!";
}

// Handle sending a message
if (isset($_POST['receiver_id']) && isset($_POST['message'])) {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];

    // Insert the message into the database
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$sender_id, $receiver_id, $message]);
    $successMessage = "Message sent!";
}

// Fetch all users for adding friends
$stmt = $pdo->query("SELECT id, username FROM users");
$allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch friends for the logged-in user
$stmt = $pdo->prepare("SELECT users.id, users.username, friends.status FROM friends JOIN users ON friends.friend_id = users.id WHERE friends.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch messages for the logged-in user
$stmt = $pdo->prepare("SELECT messages.*, sender.username AS sender_name FROM messages JOIN users AS sender ON messages.sender_id = sender.id WHERE messages.receiver_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        .friend-list, .message-list {
            margin-top: 20px;
        }
        .friend, .message {
            margin: 5px 0;
        }
    </style>
</head>
<body>

    <h1>Chat Application</h1>
    
    <!-- Display success or error messages -->
    <?php if (isset($successMessage)): ?>
        <p class="success"><?php echo $successMessage; ?></p>
    <?php elseif (isset($errorMessage)): ?>
        <p class="error"><?php echo $errorMessage; ?></p>
    <?php endif; ?>

    <!-- Add Friend Section -->
    <h2>Add Friend</h2>
    <form method="POST">
        <select name="friend_id" required>
            <option value="">Select a user</option>
            <?php foreach ($allUsers as $user): ?>
                <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                    <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
        <button type="submit">Add Friend</button>
    </form>

    <!-- Friend List Section -->
    <h2>Your Friends</h2>
    <div class="friend-list">
        <?php foreach ($friends as $friend): ?>
            <div class="friend">
                <?php echo htmlspecialchars($friend['username']); ?>
                <?php if ($friend['status'] === 'pending'): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="accept_friend_id" value="<?php echo $friend['id']; ?>">
                        <button type="submit">Accept</button>
                    </form>
                <?php endif; ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="remove_friend_id" value="<?php echo $friend['id']; ?>">
                    <button type="submit">Remove</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Message Sending Section -->
    <h2>Send Message</h2>
    <form method="POST">
        <select name="receiver_id" required>
            <option value="">Select a friend</option>
            <?php foreach ($friends as $friend): ?>
                <option value="<?php echo $friend['id']; ?>"><?php echo htmlspecialchars($friend['username']); ?></option>
            <?php endforeach; ?>
        </select>
        <textarea name="message" required></textarea>
        <button type="submit">Send Message</button>
    </form>

    <!-- Messages Section -->
    <h2>Your Messages</h2>
    <div class="message-list">
        <?php foreach ($messages as $message): ?>
            <div class="message">
                <strong><?php echo htmlspecialchars($message['sender_name']); ?>:</strong>
                <?php echo htmlspecialchars($message['message']); ?> <em>(<?php echo $message['created_at']; ?>)</em>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>