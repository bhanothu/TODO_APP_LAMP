<!DOCTYPE html>
<?php
session_start();
error_reporting(E_ALL); 
ini_set('display_errors', 1);
include('config.php');

// Registration Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        echo "Username and Password are required.";
        exit;
    }
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('ss', $username, $hashed_password);
        if ($stmt->execute()) {
            header('Location: http://localhost/LAMP/public/login.html');
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

// Login Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        echo "Username and Password are required.";
        exit;
    }
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                // Debugging session
                echo "Session Set: " . $_SESSION['username']; 
                header('Location: http://localhost/LAMP/public/index.html');
                exit;
            } else {
                echo "Incorrect password!";
            }
        } else {
            echo "No user found with that username.";
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}
?>

