<?php
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loginInput = $_POST['loginInput'];
    $password   = $_POST['password'];

    $xmlFile = 'users.xml';

    if (!file_exists($xmlFile)) {
        $message = "No users registered yet.";
    } else {
        $xml = simplexml_load_file($xmlFile);
        foreach ($xml->user as $user) {
            $storedUsername = (string)$user->username;
            $storedEmail    = (string)$user->email;
            $storedPassword = (string)$user->password;

            if (
                ($loginInput === $storedUsername || $loginInput === $storedEmail) 
                && $password === $storedPassword
            ) {
                $_SESSION['username'] = $storedUsername;
                header("Location: homepage.php");
                exit();
            }
        }
        $message = "Invalid username/email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login</title>
  <link rel="stylesheet" href="login.css" />
</head>
<body>
  <div class="container">
    <h2>Login</h2>

    <?php if ($message): ?>
      <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <label for="loginInput">Username or Email:</label>
      <input type="text" name="loginInput" required />

      <label for="password">Password:</label>
      <input type="password" name="password" required />

      <button type="submit">Login</button>
    </form>

    <form action="signup.php" method="get">
      <button type="submit" class="signup-button">Create an Account</button>
    </form>
  </div>
</body>
</html>
