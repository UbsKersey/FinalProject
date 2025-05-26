<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("No student ID provided.");
}

$id = $_GET['id'];
$xmlFile = 'students.xml';
$uploadDir = 'uploads/';

if (!file_exists($xmlFile)) {
    die("Students file not found.");
}

$dom = new DOMDocument();
$dom->load($xmlFile);

$xpath = new DOMXPath($dom);
$studentNodes = $xpath->query("//student[id='$id']");

if ($studentNodes->length === 0) {
    die("Student with ID $id not found.");
}

$studentNode = $studentNodes->item(0);

// Remove picture file if exists
$pictureNode = $studentNode->getElementsByTagName('picture')->item(0);
if ($pictureNode) {
    $picFile = $pictureNode->nodeValue;
    if ($picFile && file_exists($uploadDir . $picFile)) {
        unlink($uploadDir . $picFile);
    }
}

// Remove student node from the document
$studentNode->parentNode->removeChild($studentNode);

$dom->save($xmlFile);

header('Location: homepage.php');
exit();
?>
