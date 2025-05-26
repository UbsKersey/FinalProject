<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>About Us</title>
  <link rel="stylesheet" href="homepage.css" />
  <link rel="stylesheet" href="about_us.css" />
</head>
<body>

  <div class="navbar">
    <div class="nav-left">
      <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</div>
    </div>
    <div class="nav-right">
      <a href="homepage.php" class="nav-link">Home</a>
      <form method="post" action="logout.php" onsubmit="return confirm('Are you sure you want to logout?');" style="margin:0 0 0 15px;">
        <button type="submit">Logout</button>
      </form>
    </div>
  </div>

  <div style="padding: 20px; max-width: 700px; margin: auto; background: white; margin-top: 30px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
    <h2>About Us</h2>
    <p>Welcome! This is the final project requirement on IT 310 Web Systems and Technologies 2.</p>

    <div class="team-container">
      <div class="team-member">
        <img src="uploads\682fd7d3a3d53.jpg" />
        <div class="name">Morte, Rexie John C.</div>
        <div class="position">BSIT 3B G1</div>
        <div class="position">Backend Developer</div>
      </div>

      <div class="team-member">
        <img src="uploads\682fd91224dc3.jpg" />
        <div class="name">Javier, Ubs Kersey L.</div>
        <div class="position">BSIT 3B G1</div>
        <div class="position">Frontend Designer</div>
      </div>
    </div>

    <p style="margin-top: 30px;"><strong>Submitted to: Mr. Sheldon V. Arenas</strong></p>
  </div>

</body>
</html>
