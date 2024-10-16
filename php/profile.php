<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth.php');
    exit;
}

// Fetch current user details
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $bio = $_POST['bio']; // Get bio from POST data

    // Optional password change
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, bio = ?, password = ? WHERE id = ?");
        $stmt->execute([$first_name, $last_name, $username, $email, $bio, $password, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, bio = ? WHERE id = ?");
        $stmt->execute([$first_name, $last_name, $username, $email, $bio, $user_id]);
    }

    // Update session username if changed //sign ng bug if d nag cchange nag ooverflow ung buffer
    $_SESSION['username'] = $username;

    // Handle profile picture upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
        
        // Validate file type
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($imageFileType, $allowed_types)) {
            // Move uploaded file
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
                $stmt->execute([$_FILES["profile_pic"]["name"], $user_id]);
                // Redirect to the same page to refresh the data
                header('Location: profile.php'); // Change to the correct path if necessary
                exit; // Make sure to exit after the redirect
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="style/profile-style.css">
</head>
<body>

    <div class="box-body">
        <div class="sidebar">

            <?php
            // Display profile picture
            if (!empty($user['profile_pic'])) {
                echo "<img class='profile-pic' src='uploads/" . htmlspecialchars($user['profile_pic']) . "' alt='Profile Picture'>";
            }
            ?>

            <div><?php echo '<h2 class="name">' . htmlspecialchars($user['first_name']) . str_repeat("&nbsp;", 1) . htmlspecialchars($user['last_name']) . '</h2>' ?></div>

            <a href="../index.php"><h4 id="return">Return</h4></a>
            <a href="logout.php"><h4 id="logout">Logout</h4></a>

        </div>

        <form method="POST" action="" enctype="multipart/form-data" id="profile-form">
            <br><br><br>
            <label for="first_name">First Name:</label><label for="last_name" id="lname">Last Name:</label>
            <input type="text" placeholder="First Name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>">
            <input type="text" placeholder="Last Name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>">
            <label for="username">Username:</label><label id="mail" for="email">Email:</label>

            <input type="text" placeholder="Username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
            <input type="email" placeholder="Email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
            <br>
            <label for="bio">Bio:</label><br>
            <textarea type="text" rows="4" cols="69" name="bio"><?php echo htmlspecialchars(isset($user['bio']) ? $user['bio'] : ''); ?></textarea>
            <br>
            <br>

            <label for="password">Password:</label><label for="password" id="p2">Confirm Password:</label><br>

            <input type="password" name="password" id="pass"> <input type="password" id="confirm-pass">
            <input type="file" name="profile_pic">
            <button type="submit">Update Profile</button>

        </form>
    </div>

<script src="javascript/confirmation.js"></script>

</body>
</html>
