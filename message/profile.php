<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch current user details
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update and picture upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $bio = $_POST['bio']; // Get bio from POST data

    // Optional password change
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, bio = ?, password = ? WHERE id = ?");
        $stmt->execute([$username, $email, $bio, $password, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, bio = ? WHERE id = ?");
        $stmt->execute([$username, $email, $bio, $user_id]);
    }

    // Update session username if changed
    $_SESSION['username'] = $username;

    // Handle profile picture upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
        
        // Validate file type (you can add more types if needed)
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($imageFileType, $allowed_types)) {
            // Move uploaded file
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
                $stmt->execute([$_FILES["profile_pic"]["name"], $user_id]);
                echo "Profile picture updated successfully!";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

    echo "Profile updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Overview</title>
</head>
<body>
    <h1>Profile Overview</h1>

    <form method="POST" action="" enctype="multipart/form-data">
        <label>Username: </label><br>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br><br>

        <label>Email: </label><br>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"><br><br>

        <label>Bio: </label><br>
        <textarea name="bio" rows="4" cols="50"><?php echo htmlspecialchars(isset($user['bio']) ? $user['bio'] : ''); ?></textarea><br><br>

        <label>New Password (optional): </label><br>
        <input type="password" name="password"><br><br>

        <label>Profile Picture: </label><br>
        <input type="file" name="profile_pic"><br><br>

        <button type="submit">Update Profile</button>
    </form>

    <br>
    <a href="chat.php">Back to Chat</a>

    <?php
    // Display profile picture
    if (!empty($user['profile_pic'])) {
        echo "<h2>Your Profile Picture:</h2>";
        echo "<img src='uploads/" . htmlspecialchars($user['profile_pic']) . "' alt='Profile Picture' width='100'>";
    }
    ?>
</body>
</html>
