<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $xmlFile = 'students.xml';

    $id = $_POST['id'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $course = $_POST['course'];

    // Handle picture upload
    $uploadDir = 'uploads/';
    $pictureName = '';
    if (!empty($_FILES['picture']['name'])) {
        $ext = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
        $pictureName = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['picture']['tmp_name'], $uploadDir . $pictureName);
    }

    if (file_exists($xmlFile)) {
        $students = simplexml_load_file($xmlFile);
    } else {
        $students = new SimpleXMLElement('<?xml version="1.0"?><students></students>');
    }

    // Check if student ID already exists
    foreach ($students->student as $stu) {
        if ((string)$stu->id === $id) {
            $message = "Student with ID $id already exists.";
            break;
        }
    }

    if (empty($message)) {
        $newStudent = $students->addChild('student');
        $newStudent->addChild('id', $id);
        $newStudent->addChild('firstname', htmlspecialchars($firstname));
        $newStudent->addChild('middlename', htmlspecialchars($middlename));
        $newStudent->addChild('lastname', htmlspecialchars($lastname));
        $newStudent->addChild('birthday', $birthday);
        $newStudent->addChild('gender', $gender);
        $newStudent->addChild('email', htmlspecialchars($email));
        $newStudent->addChild('contact', htmlspecialchars($contact));
        $newStudent->addChild('course', htmlspecialchars($course));
        $newStudent->addChild('picture', $pictureName);

        // Save with pretty format
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($students->asXML());
        $dom->save($xmlFile);

        header('Location: homepage.php');
        exit();
    }
}
?>

<?php if (!empty($message)): ?>
<script>
    alert("<?= $message ?>");
    window.history.back();
</script>
<?php endif; ?>
