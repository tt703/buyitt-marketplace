<?php
include("../includes/config.php");

//set admin details
$name = "AdminTest";
$email = "admin@buyitt.com";
$role = "admin";
$phone = "1234567890";
$address1 = "Admin Address";
$address2 = "";
$city = "Admin City";
$province = "Admin Province";
$postal_code = "1234"; 
$password = "adminpassword";

$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
if( $stmt->fetch()){
    echo "Admin already exists";
    exit();
}

$hashed_password = password_hash($password, PASSWORD_BCRYPT);

$stmt = $db->prepare("INSERT INTO users (name, email, role, phone, address1, address2, city, province, postal_code, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$name, $email, $role, $phone, $address1, $address2, $city, $province, $postal_code, $hashed_password]); 

echo "Admin created successfully";
?>