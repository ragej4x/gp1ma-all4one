if (isset($_POST['accept'])) {
    $request_id = $_POST['request_id'];

    // Update the friend request status to accepted
    $stmt = $pdo->prepare("UPDATE friends SET status = 'accepted' WHERE id = ?");
    $stmt->execute([$request_id]);
    echo "Friend request accepted!";
}

if (isset($_POST['decline'])) {
    $request_id = $_POST['request_id'];

    // Delete the friend request
    $stmt = $pdo->prepare("DELETE FROM friends WHERE id = ?");
    $stmt->execute([$request_id]);
    echo "Friend request declined!";
}



//test
//wag i exect bka mag karoon ng data leak