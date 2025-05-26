<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

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

    $uploadDir = 'uploads/';

    if (!file_exists($xmlFile)) {
        die("Students file not found.");
    }

    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->load($xmlFile);

    $xpath = new DOMXPath($dom);

    $studentNodes = $xpath->query("//student[id='$id']");

    if ($studentNodes->length === 0) {
        die("Student with ID $id not found.");
    }

    $studentNode = $studentNodes->item(0);

    // Update fields
    $tags = [
        'firstname' => $firstname,
        'middlename' => $middlename,
        'lastname' => $lastname,
        'birthday' => $birthday,
        'gender' => $gender,
        'email' => $email,
        'contact' => $contact,
        'course' => $course,
    ];

    foreach ($tags as $tag => $value) {
        $child = $studentNode->getElementsByTagName($tag)->item(0);
        if ($child) {
            $child->nodeValue = htmlspecialchars($value);
        } else {
            $newChild = $dom->createElement($tag, htmlspecialchars($value));
            $studentNode->appendChild($newChild);
        }
    }

    // Handle picture upload
    if (!empty($_FILES['picture']['name'])) {
        $ext = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
        $pictureName = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['picture']['tmp_name'], $uploadDir . $pictureName);

        // Update picture node
        $pictureNode = $studentNode->getElementsByTagName('picture')->item(0);
        if ($pictureNode) {
            // Delete old picture file if exists and is not empty
            $oldPic = $pictureNode->nodeValue;
            if ($oldPic && file_exists($uploadDir . $oldPic)) {
                unlink($uploadDir . $oldPic);
            }
            $pictureNode->nodeValue = $pictureName;
        } else {
            $newPictureNode = $dom->createElement('picture', $pictureName);
            $studentNode->appendChild($newPictureNode);
        }
    }

    // Save pretty-printed XML
    $dom->save($xmlFile);

    header('Location: homepage.php');
    exit();
}
?>
