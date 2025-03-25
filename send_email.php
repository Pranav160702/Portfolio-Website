<?php
// Start session for CSRF protection
session_start();

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed. Please try again.");
    }
    
    // Initialize error array
    $errors = [];
    
    // Validate and sanitize input data
    $name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? sanitize_input($_POST['subject']) : '';
    $message = isset($_POST['message']) ? sanitize_input($_POST['message']) : '';
    
    // Validate name
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Validate subject
    if (empty($subject)) {
        $errors[] = "Subject is required";
    }
    
    // Validate message
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    // If there are no errors, proceed with sending the email
    if (empty($errors)) {
        // Set the recipient email address
        $to = "jadhavpranav1602@gmail.com";
        
        // Set the email headers
        $headers = "From: " . $name . " <" . $email . ">" . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        // Compose the email message with sanitized data
        $email_message = "
        <html>
        <head>
            <title>Contact Form Submission</title>
        </head>
        <body>
            <h2>Contact Form Submission</h2>
            <p><strong>Name:</strong> " . $name . "</p>
            <p><strong>Email:</strong> " . $email . "</p>
            <p><strong>Subject:</strong> " . $subject . "</p>
            <p><strong>Message:</strong><br>" . nl2br($message) . "</p>
            <p><em>This message was sent from your website contact form at " . date("Y-m-d H:i:s") . "</em></p>
        </body>
        </html>
        ";
        
        // Attempt to send the email
        if (mail($to, $subject, $email_message, $headers)) {
            // Log successful submission
            error_log("Contact form submitted by $name <$email> at " . date("Y-m-d H:i:s"));
            
            // Redirect to a thank you page or show success message
            header("Location: thank-you.html");
            exit;
        } else {
            // Log the error
            error_log("Failed to send contact form from $name <$email> at " . date("Y-m-d H:i:s"));
            
            // Show error message
            echo json_encode(['success' => false, 'message' => 'Failed to send your message. Please try again later or contact us directly.']);
        }
    } else {
        // Return validation errors
        echo json_encode(['success' => false, 'errors' => $errors]);
    }
} else {
    // If the request method is not POST, redirect to the contact page
    header("Location: contact.html");
    exit;
}

// Generate CSRF token for the form
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>