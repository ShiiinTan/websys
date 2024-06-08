<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
$conn = mysqli_connect("localhost", "root", "", "test"); // Adjust credentials as needed

if (isset($_POST["register"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $verification_code = rand(100000, 999999);

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@gmail.com';
        $mail->Password = 'your_password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('your_email@gmail.com', 'Yenshin Shop');
        $mail->addAddress($email, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Email verification';
        $mail->Body = '<p>Your verification code is: <b style="font-size: 30px;">' . $verification_code . '</b></p>';
        
        $mail->send();

        $encrypted_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert in users table
        $sql = "INSERT INTO users(name, email, password, verification_code, email_verified_at) VALUES ('$name', '$email', '$encrypted_password', '$verification_code', NULL)";
        mysqli_query($conn, $sql);

        header("Location: email-verification.php?email=" . $email);
        exit();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if (isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        echo "Login successful!";
    } else {
        echo "Invalid email or password.";
    }
}
?>
