<?php
// Database connection
$host = 'localhost';
$dbname = 'crud_example';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Create User
if ($_POST['action'] == 'create') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $country = $_POST['country'];
    $skills = isset($_POST['skills']) ? implode(',', $_POST['skills']) : '';

    // Handle Image Upload
    $profile_pic = '';
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $profile_pic = 'uploads/' . time() . '_' . $_FILES['profile_pic']['name'];
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_pic);
    }

    $sql = "INSERT INTO users (name, email, gender, skills, profile_pic, country) 
            VALUES (:name, :email, :gender, :skills, :profile_pic, :country)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':gender' => $gender,
        ':skills' => $skills,
        ':profile_pic' => $profile_pic,
        ':country' => $country
    ]);

    echo "User created successfully!";
}

// Read Users
if ($_POST['action'] == 'read') {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $output = '';
    foreach ($users as $user) {
        $output .= "<div class='user'>
                        <img src='{$user['profile_pic']}' width='100'><br>
                        Name: {$user['name']}<br>
                        Email: {$user['email']}<br>
                        Gender: {$user['gender']}<br>
                        Skills: {$user['skills']}<br>
                        Country: {$user['country']}<br>
                        <button class='edit-btn btn btn-warning' data-id='{$user['id']}'>Edit</button>
                        <button class='delete-btn btn btn-danger' data-id='{$user['id']}'>Delete</button>
                    </div><hr>";
    }
    echo $output;
}

// Get User for Edit
if ($_POST['action'] == 'get_user') {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($user);
}

// Update User
if ($_POST['action'] == 'update') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $country = $_POST['country'];
   
}

?>

<?php

// Update User
if ($_POST['action'] == 'update') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $country = $_POST['country'];
    $skills = isset($_POST['skills']) ? implode(',', $_POST['skills']) : '';

    // Handle Image Upload (if a new file is uploaded)
    $profile_pic = '';
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        // Upload new image
        $profile_pic = 'uploads/' . time() . '_' . $_FILES['profile_pic']['name'];
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_pic);
    } else {
        // If no new file is uploaded, keep the existing profile picture
        $stmt = $pdo->prepare("SELECT profile_pic FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        $profile_pic = $user['profile_pic'];
    }

    // SQL query to update user details
    $sql = "UPDATE users 
            SET name = :name, email = :email, gender = :gender, skills = :skills, profile_pic = :profile_pic, country = :country 
            WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':gender' => $gender,
        ':skills' => $skills,
        ':profile_pic' => $profile_pic,
        ':country' => $country,
        ':id' => $id
    ]);

    echo "User updated successfully!";
}


// Delete User
if ($_POST['action'] == 'delete') {
    $id = $_POST['id'];
    
    // Delete the user's profile picture from the server (optional)
    $stmt = $pdo->prepare("SELECT profile_pic FROM users WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch();
    if (file_exists($user['profile_pic'])) {
        unlink($user['profile_pic']); // Delete the image file
    }

    // SQL query to delete the user
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute([':id' => $id]);

    echo "User deleted successfully!";
}

?>