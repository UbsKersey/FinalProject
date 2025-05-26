<?php
$message = "";
$signupSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['name'];
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if ($password !== $confirm) {
        $message = "Passwords do not match.";
    } else {
        $xmlFile = 'users.xml';

        if (!file_exists($xmlFile)) {
            $xml = new SimpleXMLElement('<users/>');
        } else {
            $xml = simplexml_load_file($xmlFile);
        }

        foreach ($xml->user as $user) {
            if ((string)$user->username === $username || (string)$user->email === $email) {
                $message = "Username or email already exists.";
                break;
            }
        }

        if ($message === "") {
            $user = $xml->addChild('user');
            $user->addChild('name', htmlspecialchars($name));
            $user->addChild('username', htmlspecialchars($username));
            $user->addChild('email', htmlspecialchars($email));
            $user->addChild('password', htmlspecialchars($password));

            $dom = new DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($xml->asXML());
            $dom->save($xmlFile);

            $signupSuccess = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up</title>
  <link rel="stylesheet" href="signup.css">
</head>
<body>
  <div class="container">
    <h2>Sign Up</h2>

    <?php if ($message && !$signupSuccess): ?>
      <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <label for="name">Full Name:</label>
      <input type="text" name="name" required>

      <label for="username">Username:</label>
      <input type="text" name="username" required>

      <label for="email">Email:</label>
      <input type="email" name="email" required>

      <label for="password">Password:</label>
      <input type="password" name="password" required>

      <label for="confirm">Confirm Password:</label>
      <input type="password" name="confirm" required>

      <button type="submit">Sign Up</button>
    </form>

    <form action="index.php" method="get">
      <button type="submit" class="login-button">Go Back to Login</button>
    </form>
  </div>

  <?php if ($signupSuccess): ?>
    <script>
      alert("Signup successful!");
      window.location.href = "index.php";
    </script>
  <?php endif; ?>
</body>
</html>
