<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

function calculateAge($birthday) {
    $birthDate = new DateTime($birthday);
    $today = new DateTime('today');
    $age = $birthDate->diff($today)->y;
    return $age;
}

$uploadDir = 'uploads/';
$noImage = 'no-image.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Home Page</title>
  <link rel="stylesheet" href="homepage.css" />
  <style>
    #viewModal {
      display: none;
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.7);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }
    #viewModal > div {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      max-width: 600px;
      width: 90%;
      max-height: 80vh;
      overflow-y: auto;
      box-shadow: 0 0 15px rgba(0,0,0,0.5);
      position: relative;
    }
    #viewModal button.closeBtn {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 24px;
      border: none;
      background: none;
      cursor: pointer;
      color: #333;
    }
  </style>
</head>
<body>

  <div class="navbar">
    <div class="nav-left">
      <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</div>
    </div>
    <div class="nav-right">
      <a href="about_us.php" class="nav-link">About Us</a>
      <form method="post" action="logout.php" onsubmit="return confirm('Are you sure you want to logout?');" style="margin:0 0 0 15px;">
        <button type="submit">Logout</button>
      </form>
    </div>
  </div>

  <div style="padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
      <h3 style="margin: 0; font-size: 50px;">Student List</h3>
      <button onclick="document.getElementById('modal').style.display='flex';">Add Student</button>
    </div>

    <input 
      type="text" 
      id="searchInput" 
      placeholder="Search by ID or Name" 
      style="margin-bottom: 10px; width: 100%; padding: 8px;" 
      onkeyup="filterStudents()" 
    />

    <table id="studentsTable" style="width:100%">
      <tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Middle Name</th>
        <th>Last Name</th>
        <th>Age</th>
        <th>Gender</th>
        <th>Email</th>
        <th>Contact</th>
        <th>Course</th>
        <th>Actions</th>
      </tr>
      <?php
      $xmlFile = 'students.xml';

      if (file_exists($xmlFile)) {
          $students = simplexml_load_file($xmlFile);
          foreach ($students->student as $s) {
              $picFilename = isset($s->picture) ? (string)$s->picture : '';
              $age = isset($s->birthday) ? calculateAge((string)$s->birthday) : 'N/A';
              echo "<tr>
                  <td>{$s->id}</td>
                  <td>{$s->firstname}</td>
                  <td>{$s->middlename}</td>
                  <td>{$s->lastname}</td>
                  <td>{$age}</td>
                  <td>{$s->gender}</td>
                  <td>{$s->email}</td>
                  <td>{$s->contact}</td>
                  <td>{$s->course}</td>
                  <td>
                    <button onclick=\"viewStudent('".addslashes($s->id)."', '".addslashes($s->firstname)."', '".addslashes($s->middlename)."', '".addslashes($s->lastname)."', '".addslashes($age)."', '".addslashes($s->gender)."', '".addslashes($s->email)."', '".addslashes($s->contact)."', '".addslashes($s->course)."', '".addslashes($picFilename)."')\">View</button>
                    <button onclick=\"openEditModal('".addslashes($s->id)."', '".addslashes($s->firstname)."', '".addslashes($s->middlename)."', '".addslashes($s->lastname)."', '".addslashes($s->birthday)."', '".addslashes($s->gender)."', '".addslashes($s->email)."', '".addslashes($s->contact)."', '".addslashes($s->course)."', '".addslashes($picFilename)."')\">Edit</button>
                    <button onclick=\"deleteStudent('".addslashes($s->id)."')\">Delete</button>
                  </td>
                </tr>";
          }
      } else {
          echo "<tr><td colspan='10'>No students found.</td></tr>";
      }
      ?>
    </table>
  </div>

  <!-- Add Student Form -->
  <div id="modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
    <div id="modal-content" style="background:#fff; padding:20px; border-radius:10px; width: 400px; position: relative;">
      <h3>Add Student</h3>
      <form id="addStudentForm" enctype="multipart/form-data" method="post" action="add_student.php">
        <label>ID:</label><br><input type="text" name="id" required><br>
        <label>First Name:</label><br><input type="text" name="firstname" required><br>
        <label>Middle Name:</label><br><input type="text" name="middlename"><br>
        <label>Last Name:</label><br><input type="text" name="lastname" required><br>
        <label>Birthday:</label><br><input type="date" name="birthday" required><br>
        <label>Gender:</label><br>
        <select name="gender" required>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select><br>
        <label>Email:</label><br><input type="email" name="email" required><br>
        <label>Contact:</label><br><input type="text" name="contact" required><br>
        <label>Course:</label><br>
        <select name="course" required>
          <option></option>

            <optgroup label="College of Architecture and Fine Arts (CAFA)">
              <option value="Bachelor of Science in Architecture">Bachelor of Science in Architecture</option>
              <option value="Bachelor of Landscape Architecture">Bachelor of Landscape Architecture</option>
              <option value="Bachelor of Fine Arts Major in Visual Communication">Bachelor of Fine Arts Major in Visual Communication</option>
            </optgroup>

            <optgroup label="College of Arts and Letters (CAL)">
              <option value="Bachelor of Arts in Broadcasting">Bachelor of Arts in Broadcasting</option>
              <option value="Bachelor of Arts in Journalism">Bachelor of Arts in Journalism</option>
              <option value="Bachelor of Performing Arts (Theater Track)">Bachelor of Performing Arts (Theater Track)</option>
              <option value="Batsilyer ng Sining sa Malikhaing Pagsulat (Bachelor of Arts in Creative Writing)">Batsilyer ng Sining sa Malikhaing Pagsulat (Bachelor of Arts in Creative Writing)</option>
            </optgroup>

            <optgroup label="College of Business Administration (CBA)">
              <option value="Bachelor of Science in Business Administration Major in Business Economics">Bachelor of Science in Business Administration Major in Business Economics</option>
              <option value="Bachelor of Science in Business Administration Major in Financial Management">Bachelor of Science in Business Administration Major in Financial Management</option>
              <option value="Bachelor of Science in Business Administration Major in Marketing Management">Bachelor of Science in Business Administration Major in Marketing Management</option>
              <option value="Bachelor of Science in Entrepreneurship">Bachelor of Science in Entrepreneurship</option>
              <option value="Bachelor of Science in Accountancy">Bachelor of Science in Accountancy</option>
              <option value="Bachelor of Science in Accounting Information System">Bachelor of Science in Accounting Information System</option>
            </optgroup>

            <optgroup label="College of Criminal Justice Education (CCJE)">
              <option value="Bachelor of Arts in Legal Management">Bachelor of Arts in Legal Management</option>
              <option value="Bachelor of Science in Criminology">Bachelor of Science in Criminology</option>
            </optgroup>

            <optgroup label="College of Hospitality and Tourism Management (CHTM)">
              <option value="Bachelor of Science in Hospitality Management">Bachelor of Science in Hospitality Management</option>
              <option value="Bachelor of Science in Tourism Management">Bachelor of Science in Tourism Management</option>
            </optgroup>

            <optgroup label="College of Information and Communications Technology (CICT)">
              <option value="Bachelor of Science in Information Technology">Bachelor of Science in Information Technology</option>
              <option value="Bachelor of Library and Information Science">Bachelor of Library and Information Science</option>
            </optgroup>

            <optgroup label="College of Industrial Technology (CIT)">
              <option value="Bachelor of Industrial Technology major in Automotive">Bachelor of Industrial Technology major in Automotive</option>
              <option value="Bachelor of Industrial Technology major in Computer">Bachelor of Industrial Technology major in Computer</option>
              <option value="Bachelor of Industrial Technology major in Drafting">Bachelor of Industrial Technology major in Drafting</option>
              <option value="Bachelor of Industrial Technology major in Electrical">Bachelor of Industrial Technology major in Electrical</option>
              <option value="Bachelor of Industrial Technology major in Electronics">Bachelor of Industrial Technology major in Electronics</option>
              <option value="Bachelor of Industrial Technology major in Mechanical">Bachelor of Industrial Technology major in Mechanical</option>
              <option value="Certificate in Two-Year Technology major in Foods">Certificate in Two-Year Technology major in Foods</option>
              <option value="Certificate in Two-Year Technology major in Welding and Fabrication">Certificate in Two-Year Technology major in Welding and Fabrication</option>
              <option value="Certificate in Two-Year Technology major in Heating, Ventilation & Air-Conditioning">Certificate in Two-Year Technology major in Heating, Ventilation & Air-Conditioning</option>
            </optgroup>

            <optgroup label="College of Law (CLaw)">
              <option value="Bachelor of Laws">Bachelor of Laws</option>
              <option value="Juris Doctor">Juris Doctor</option>
            </optgroup>

            <optgroup label="College of Nursing (CN)">
              <option value="Bachelor of Science in Nursing">Bachelor of Science in Nursing</option>
            </optgroup>

            <optgroup label="College of Engineering (COE)">
              <option value="Bachelor of Science in Civil Engineering">Bachelor of Science in Civil Engineering</option>
              <option value="Bachelor of Science in Computer Engineering">Bachelor of Science in Computer Engineering</option>
              <option value="Bachelor of Science in Electrical Engineering">Bachelor of Science in Electrical Engineering</option>
              <option value="Bachelor of Science in Electronics Engineering">Bachelor of Science in Electronics Engineering</option>
              <option value="Bachelor of Science in Industrial Engineering">Bachelor of Science in Industrial Engineering</option>
              <option value="Bachelor of Science in Manufacturing Engineering">Bachelor of Science in Manufacturing Engineering</option>
              <option value="Bachelor of Science in Mechanical Engineering">Bachelor of Science in Mechanical Engineering</option>
              <option value="Bachelor of Science in Mechatronics Engineering">Bachelor of Science in Mechatronics Engineering</option>
            </optgroup>

            <optgroup label="College of Education (COED)">
              <option value="Bachelor of Elementary Education">Bachelor of Elementary Education</option>
              <option value="Bachelor of Early Childhood Education">Bachelor of Early Childhood Education</option>
              <option value="Bachelor of Secondary Education Major in English minor in Mandarin">Bachelor of Secondary Education Major in English minor in Mandarin</option>
              <option value="Bachelor of Secondary Education Major in Filipino">Bachelor of Secondary Education Major in Filipino</option>
              <option value="Bachelor of Secondary Education Major in Sciences">Bachelor of Secondary Education Major in Sciences</option>
              <option value="Bachelor of Secondary Education Major in Mathematics">Bachelor of Secondary Education Major in Mathematics</option>
              <option value="Bachelor of Secondary Education Major in Social Studies">Bachelor of Secondary Education Major in Social Studies</option>
              <option value="Bachelor of Secondary Education Major in Values Education">Bachelor of Secondary Education Major in Values Education</option>
              <option value="Bachelor of Technical Vocational Teacher Education with specialization in Foods and Service Management">Bachelor of Technical Vocational Teacher Education with specialization in Foods and Service Management</option>
              <option value="Bachelor of Technical Vocational Teacher Education with specialization in Garments, Fashion and Design">Bachelor of Technical Vocational Teacher Education with specialization in Garments, Fashion and Design</option>
              <option value="Bachelor of Physical Education">Bachelor of Physical Education</option>
            </optgroup>

            <optgroup label="College of Science (CS)">
              <option value="Bachelor of Science in Biology">Bachelor of Science in Biology</option>
              <option value="Bachelor of Science in Environmental Science">Bachelor of Science in Environmental Science</option>
              <option value="Bachelor of Science in Food Technology">Bachelor of Science in Food Technology</option>
              <option value="Bachelor of Science in Mathematics with Specialization in Computer Science">Bachelor of Science in Mathematics with Specialization in Computer Science</option>
              <option value="Bachelor of Science in Mathematics with Specialization in Applied Statistics">Bachelor of Science in Mathematics with Specialization in Applied Statistics</option>
              <option value="Bachelor of Science in Mathematics with Specialization in Business Applications">Bachelor of Science in Mathematics with Specialization in Business Applications</option>
            </optgroup>

            <optgroup label="College of Sports, Exercise and Recreation (CSER)">
              <option value="Bachelor of Science in Exercise and Sports Sciences with specialization in Fitness and Sports Coaching">Bachelor of Science in Exercise and Sports Sciences with specialization in Fitness and Sports Coaching</option>
              <option value="Bachelor of Science in Exercise and Sports Sciences with specialization in Fitness and Sports Management">Bachelor of Science in Exercise and Sports Sciences with specialization in Fitness and Sports Management</option>
              <option value="Certificate in Physical Education">Certificate in Physical Education</option>
            </optgroup>

            <optgroup label="College of Social Sciences and Philosophy (CSSP)">
              <option value="Bachelor of Public Administration">Bachelor of Public Administration</option>
              <option value="Bachelor of Science in Social Work">Bachelor of Science in Social Work</option>
              <option value="Bachelor of Science in Psychology">Bachelor of Science in Psychology</option>
            </optgroup>
        </select><br>
        <label>Picture:</label><br><input type="file" name="picture" accept="image/*"><br><br>
        <button type="submit">Add</button>
        <button type="button" onclick="document.getElementById('modal').style.display='none';">Cancel</button>
      </form>
    </div>
  </div>

  <!-- Edit Student Form -->
  <div id="editModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
    <div id="editModal-content" style="background:#fff; padding:20px; border-radius:10px; width: 400px; position: relative;">
      <h3>Edit Student</h3>
      <form id="editStudentForm" enctype="multipart/form-data" method="post" action="edit_student.php">
        <input type="hidden" name="id" id="edit-id" readonly>
        <label>First Name:</label><br><input type="text" name="firstname" id="edit-firstname" required><br>
        <label>Middle Name:</label><br><input type="text" name="middlename" id="edit-middlename"><br>
        <label>Last Name:</label><br><input type="text" name="lastname" id="edit-lastname" required><br>
        <label>Birthday:</label><br><input type="date" name="birthday" id="edit-birthday" required><br>
        <label>Gender:</label><br>
        <select name="gender" id="edit-gender" required>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select><br>
        <label>Email:</label><br><input type="email" name="email" id="edit-email" required><br>
        <label>Contact:</label><br><input type="text" name="contact" id="edit-contact" required><br>
        <label>Course:</label><br>
        <select name="course" id="edit-course" required>
          <option></option>

            <optgroup label="College of Architecture and Fine Arts (CAFA)">
              <option value="Bachelor of Science in Architecture">Bachelor of Science in Architecture</option>
              <option value="Bachelor of Landscape Architecture">Bachelor of Landscape Architecture</option>
              <option value="Bachelor of Fine Arts Major in Visual Communication">Bachelor of Fine Arts Major in Visual Communication</option>
            </optgroup>

            <optgroup label="College of Arts and Letters (CAL)">
              <option value="Bachelor of Arts in Broadcasting">Bachelor of Arts in Broadcasting</option>
              <option value="Bachelor of Arts in Journalism">Bachelor of Arts in Journalism</option>
              <option value="Bachelor of Performing Arts (Theater Track)">Bachelor of Performing Arts (Theater Track)</option>
              <option value="Batsilyer ng Sining sa Malikhaing Pagsulat (Bachelor of Arts in Creative Writing)">Batsilyer ng Sining sa Malikhaing Pagsulat (Bachelor of Arts in Creative Writing)</option>
            </optgroup>

            <optgroup label="College of Business Administration (CBA)">
              <option value="Bachelor of Science in Business Administration Major in Business Economics">Bachelor of Science in Business Administration Major in Business Economics</option>
              <option value="Bachelor of Science in Business Administration Major in Financial Management">Bachelor of Science in Business Administration Major in Financial Management</option>
              <option value="Bachelor of Science in Business Administration Major in Marketing Management">Bachelor of Science in Business Administration Major in Marketing Management</option>
              <option value="Bachelor of Science in Entrepreneurship">Bachelor of Science in Entrepreneurship</option>
              <option value="Bachelor of Science in Accountancy">Bachelor of Science in Accountancy</option>
              <option value="Bachelor of Science in Accounting Information System">Bachelor of Science in Accounting Information System</option>
            </optgroup>

            <optgroup label="College of Criminal Justice Education (CCJE)">
              <option value="Bachelor of Arts in Legal Management">Bachelor of Arts in Legal Management</option>
              <option value="Bachelor of Science in Criminology">Bachelor of Science in Criminology</option>
            </optgroup>

            <optgroup label="College of Hospitality and Tourism Management (CHTM)">
              <option value="Bachelor of Science in Hospitality Management">Bachelor of Science in Hospitality Management</option>
              <option value="Bachelor of Science in Tourism Management">Bachelor of Science in Tourism Management</option>
            </optgroup>

            <optgroup label="College of Information and Communications Technology (CICT)">
              <option value="Bachelor of Science in Information Technology">Bachelor of Science in Information Technology</option>
              <option value="Bachelor of Library and Information Science">Bachelor of Library and Information Science</option>
            </optgroup>

            <optgroup label="College of Industrial Technology (CIT)">
              <option value="Bachelor of Industrial Technology major in Automotive">Bachelor of Industrial Technology major in Automotive</option>
              <option value="Bachelor of Industrial Technology major in Computer">Bachelor of Industrial Technology major in Computer</option>
              <option value="Bachelor of Industrial Technology major in Drafting">Bachelor of Industrial Technology major in Drafting</option>
              <option value="Bachelor of Industrial Technology major in Electrical">Bachelor of Industrial Technology major in Electrical</option>
              <option value="Bachelor of Industrial Technology major in Electronics">Bachelor of Industrial Technology major in Electronics</option>
              <option value="Bachelor of Industrial Technology major in Mechanical">Bachelor of Industrial Technology major in Mechanical</option>
              <option value="Certificate in Two-Year Technology major in Foods">Certificate in Two-Year Technology major in Foods</option>
              <option value="Certificate in Two-Year Technology major in Welding and Fabrication">Certificate in Two-Year Technology major in Welding and Fabrication</option>
              <option value="Certificate in Two-Year Technology major in Heating, Ventilation & Air-Conditioning">Certificate in Two-Year Technology major in Heating, Ventilation & Air-Conditioning</option>
            </optgroup>

            <optgroup label="College of Law (CLaw)">
              <option value="Bachelor of Laws">Bachelor of Laws</option>
              <option value="Juris Doctor">Juris Doctor</option>
            </optgroup>

            <optgroup label="College of Nursing (CN)">
              <option value="Bachelor of Science in Nursing">Bachelor of Science in Nursing</option>
            </optgroup>

            <optgroup label="College of Engineering (COE)">
              <option value="Bachelor of Science in Civil Engineering">Bachelor of Science in Civil Engineering</option>
              <option value="Bachelor of Science in Computer Engineering">Bachelor of Science in Computer Engineering</option>
              <option value="Bachelor of Science in Electrical Engineering">Bachelor of Science in Electrical Engineering</option>
              <option value="Bachelor of Science in Electronics Engineering">Bachelor of Science in Electronics Engineering</option>
              <option value="Bachelor of Science in Industrial Engineering">Bachelor of Science in Industrial Engineering</option>
              <option value="Bachelor of Science in Manufacturing Engineering">Bachelor of Science in Manufacturing Engineering</option>
              <option value="Bachelor of Science in Mechanical Engineering">Bachelor of Science in Mechanical Engineering</option>
              <option value="Bachelor of Science in Mechatronics Engineering">Bachelor of Science in Mechatronics Engineering</option>
            </optgroup>

            <optgroup label="College of Education (COED)">
              <option value="Bachelor of Elementary Education">Bachelor of Elementary Education</option>
              <option value="Bachelor of Early Childhood Education">Bachelor of Early Childhood Education</option>
              <option value="Bachelor of Secondary Education Major in English minor in Mandarin">Bachelor of Secondary Education Major in English minor in Mandarin</option>
              <option value="Bachelor of Secondary Education Major in Filipino">Bachelor of Secondary Education Major in Filipino</option>
              <option value="Bachelor of Secondary Education Major in Sciences">Bachelor of Secondary Education Major in Sciences</option>
              <option value="Bachelor of Secondary Education Major in Mathematics">Bachelor of Secondary Education Major in Mathematics</option>
              <option value="Bachelor of Secondary Education Major in Social Studies">Bachelor of Secondary Education Major in Social Studies</option>
              <option value="Bachelor of Secondary Education Major in Values Education">Bachelor of Secondary Education Major in Values Education</option>
              <option value="Bachelor of Technical Vocational Teacher Education with specialization in Foods and Service Management">Bachelor of Technical Vocational Teacher Education with specialization in Foods and Service Management</option>
              <option value="Bachelor of Technical Vocational Teacher Education with specialization in Garments, Fashion and Design">Bachelor of Technical Vocational Teacher Education with specialization in Garments, Fashion and Design</option>
              <option value="Bachelor of Physical Education">Bachelor of Physical Education</option>
            </optgroup>

            <optgroup label="College of Science (CS)">
              <option value="Bachelor of Science in Biology">Bachelor of Science in Biology</option>
              <option value="Bachelor of Science in Environmental Science">Bachelor of Science in Environmental Science</option>
              <option value="Bachelor of Science in Food Technology">Bachelor of Science in Food Technology</option>
              <option value="Bachelor of Science in Mathematics with Specialization in Computer Science">Bachelor of Science in Mathematics with Specialization in Computer Science</option>
              <option value="Bachelor of Science in Mathematics with Specialization in Applied Statistics">Bachelor of Science in Mathematics with Specialization in Applied Statistics</option>
              <option value="Bachelor of Science in Mathematics with Specialization in Business Applications">Bachelor of Science in Mathematics with Specialization in Business Applications</option>
            </optgroup>

            <optgroup label="College of Sports, Exercise and Recreation (CSER)">
              <option value="Bachelor of Science in Exercise and Sports Sciences with specialization in Fitness and Sports Coaching">Bachelor of Science in Exercise and Sports Sciences with specialization in Fitness and Sports Coaching</option>
              <option value="Bachelor of Science in Exercise and Sports Sciences with specialization in Fitness and Sports Management">Bachelor of Science in Exercise and Sports Sciences with specialization in Fitness and Sports Management</option>
              <option value="Certificate in Physical Education">Certificate in Physical Education</option>
            </optgroup>

            <optgroup label="College of Social Sciences and Philosophy (CSSP)">
              <option value="Bachelor of Public Administration">Bachelor of Public Administration</option>
              <option value="Bachelor of Science in Social Work">Bachelor of Science in Social Work</option>
              <option value="Bachelor of Science in Psychology">Bachelor of Science in Psychology</option>
            </optgroup>
        </select><br>
        <label>Picture: (leave empty to keep current)</label><br>
        <input type="file" name="picture" accept="image/*"><br><br>
        <button type="submit">Save Changes</button>
        <button type="button" onclick="document.getElementById('editModal').style.display='none';">Cancel</button>
      </form>
    </div>
  </div>

  <!-- View Student Info -->
  <div id="viewModal">
    <div>
      <button class="closeBtn" onclick="document.getElementById('viewModal').style.display='none'">&times;</button>
      <div id="viewResume"></div>
    </div>
  </div>

<script>
  function filterStudents() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toLowerCase();
    const table = document.getElementById("studentsTable");
    const trs = table.getElementsByTagName("tr");

    for (let i = 1; i < trs.length; i++) {
      const tds = trs[i].getElementsByTagName("td");
      if (tds.length < 4) continue;
      const idText = tds[0].textContent || tds[0].innerText;
      const firstNameText = tds[1].textContent || tds[1].innerText;
      const lastNameText = tds[3].textContent || tds[3].innerText;
      if (idText.toLowerCase().indexOf(filter) > -1 || firstNameText.toLowerCase().indexOf(filter) > -1 || lastNameText.toLowerCase().indexOf(filter) > -1) {
        trs[i].style.display = "";
      } else {
        trs[i].style.display = "none";
      }
    }
  }

  function viewStudent(id, firstname, middlename, lastname, birthday, gender, email, contact, course, picture) {
    const uploadDir = '<?php echo $uploadDir; ?>';
    const noImage = '<?php echo $noImage; ?>';

    function calculateAgeJS(birthdateStr) {
      if (!birthdateStr) return 'N/A';
      const today = new Date();
      const birthDate = new Date(birthdateStr);
      let age = today.getFullYear() - birthDate.getFullYear();
      const m = today.getMonth() - birthDate.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }
      return age;
    }

    let picPath = (picture && picture !== '') ? uploadDir + picture : noImage;
    let age = calculateAgeJS(birthday);

    const fullName = firstname + (middlename ? (' ' + middlename) : '') + ' ' + lastname;

    const html = `
      <h2 style="border-bottom:2px solid #007BFF; padding-bottom:5px; margin-bottom:15px;">Student Information</h2>
      <div style="display:flex; gap:20px; align-items:center; margin-bottom:20px;">
        <img src="${picPath}" alt="Picture" style="width:120px; height:120px; object-fit:cover; border-radius:8px; border:1px solid #ccc;">
        <div>
          <h3 style="margin:0;">${fullName}</h3>
          <p style="margin:4px 0 0 0; color:#555;">ID: ${id}</p>
          <p style="margin:4px 0 0 0; color:#555;"> ${course}</p>
        </div>
      </div>
      <div>
        <p><strong>Age:</strong> ${birthday}</p>
        <p><strong>Gender:</strong> ${gender}</p>
        <p><strong>Email:</strong> <a href="mailto:${email}">${email}</a></p>
        <p><strong>Contact:</strong> ${contact}</p>
        
      </div>
    `;

    document.getElementById('viewResume').innerHTML = html;
    document.getElementById('viewModal').style.display = 'flex';
  }

  function openEditModal(id, firstname, middlename, lastname, birthday, gender, email, contact, course, picture) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-firstname').value = firstname;
    document.getElementById('edit-middlename').value = middlename;
    document.getElementById('edit-lastname').value = lastname;
    document.getElementById('edit-birthday').value = birthday;
    document.getElementById('edit-gender').value = gender;
    document.getElementById('edit-email').value = email;
    document.getElementById('edit-contact').value = contact;
    document.getElementById('edit-course').value = course;
    document.getElementById('editModal').style.display = 'flex';
  }

  function deleteStudent(id) {
    if(confirm('Are you sure you want to delete student with ID ' + id + '?')) {
      window.location.href = 'delete_student.php?id=' + encodeURIComponent(id);
    }
  }
</script>

</body>
</html>
