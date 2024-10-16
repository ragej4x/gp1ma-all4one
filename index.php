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
<html>
    <head>
        <title>Home</title>
        <link rel="stylesheet" href="style/index.css">
    </head>
    <body>
    <div class="container">
        <div class="left">
            <a class='profile' href="php/profile.php"><br><br><br><h3>Edit Profile</h3></a>
            <?php echo '<h2 class="name" >' . htmlspecialchars($user['first_name']). str_repeat("&nbsp;", 1). htmlspecialchars($user['last_name']).'</h2>'?>

        </div>



        <div class="middle">
            <h2>Middle Content</h2>

        </div>
        <div class="right">
            <h2>Right Side</h2>
        </div>
    </div>

    </body>
</html>