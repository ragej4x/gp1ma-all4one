<?php
// Start the session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php'; // Include your database connection file

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth.php');
    exit;
}

// Initialize variables
$user_id = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id'] ?? null;

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && isset($_POST['receiver_id'])) {
    $message = $_POST['message'];
    
    // Prepare the SQL statement to insert the message
    $stmt = $pdo->prepare("INSERT INTO private_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    if ($stmt->execute([$user_id, $_POST['receiver_id'], $message])) {
        $successMessage = "Message sent!";
    } else {
        $errorMessage = "Error sending message.";
    }
}

// Fetch messages between the two users
if ($receiver_id) {
    $stmt = $pdo->prepare("SELECT * FROM private_messages 
                           WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
                           ORDER BY created_at ASC");
    $stmt->execute([$user_id, $receiver_id, $receiver_id, $user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Private Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .message {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .sent {
            background-color: #d1e7dd;
            text-align: right;
        }
        .received {
            background-color: #f8d7da;
        }
        form {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Chat with User ID: <?php echo htmlspecialchars($receiver_id); ?></h1>

    <div id="chat-box">
        <?php if (isset($messages)): ?>
            <?php foreach ($messages as $message): ?>
                <div class="message <?php echo $message['sender_id'] == $user_id ? 'sent' : 'received'; ?>">
                    <strong><?php echo htmlspecialchars($message['sender_id']); ?>:</strong>
                    <?php echo htmlspecialchars($message['message']); ?>
                    <br>
                    <small><?php echo $message['created_at']; ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No messages yet.</p>
        <?php endif; ?>
    </div>

    <?php if (isset($successMessage)): ?>
        <p style="color: green;"><?php echo $successMessage; ?></p>
    <?php elseif (isset($errorMessage)): ?>
        <p style="color: red;"><?php echo $errorMessage; ?></p>
    <?php endif; ?>

    <form action="chat.php?receiver_id=<?php echo htmlspecialchars($receiver_id); ?>" method="POST">
        <input type="hidden" name="receiver_id" value="<?php echo htmlspecialchars($receiver_id); ?>">
        <textarea name="message" required></textarea>
        <button type="submit">Send</button>
    </form>

    <br>
    <a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
