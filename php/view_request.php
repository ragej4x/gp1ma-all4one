<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch pending friend requests
$stmt = $pdo->prepare("SELECT users.id, users.username FROM friends
                        JOIN users ON friends.user_id = users.id
                        WHERE friends.friend_id = ? AND friends.status = 'pending'");
$stmt->execute([$user_id]);
$pending_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Friend Requests</title>
</head>
<body>
    <h1>Friend Requests</h1>

    <?php if (count($pending_requests) > 0): ?>
        <ul>
            <?php foreach ($pending_requests as $request): ?>
                <li>
                    <?php echo htmlspecialchars($request['username']); ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['id']); ?>">
                        <button type="submit" name="accept">Accept</button>
                        <button type="submit" name="decline">Decline</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>You have no pending friend requests.</p>
    <?php endif; ?>
</body>
</html>
