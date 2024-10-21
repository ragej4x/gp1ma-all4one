<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT groups.id, groups.name 
                       FROM groups
                       JOIN group_members ON groups.id = group_members.group_id
                       WHERE group_members.user_id = ?");
$stmt->execute([$user_id]);
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat Dashboard</title>
</head>
<body>
    <h2>Your Groups</h2>
    <ul>
        <?php if (count($groups) > 0): ?>
            <?php foreach ($groups as $group): ?>
                <li>
                    <a href="group_chat.php?group_id=<?php echo $group['id']; ?>">
                        <?php echo htmlspecialchars($group['name']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>You are not a member of any group yet.</li>
        <?php endif; ?>
    </ul>

    <br>
    <a href="create_group.php">Create a New Group</a><br>
    <a href="profile.php">View Profile</a><br>
    <a href="logout.php">Logout</a>

</body>
</html>
