<?php


// Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$subscribe = isset($_POST['subscribe']) ? $_POST['subscribe'] : 'No'; // Default to "No"
$comments = $_POST['comments'];

// Check for empty comments
if (empty($comments)) {
    echo json_encode(["status" => "error", "message" => "Thank you!"]);
    exit;
}

/* Begin spam test block */

// Test if a bot has entered data into invisible field
$botty = $_POST['botty'];
if ($botty != NULL) {
    echo json_encode(["status" => "error", "message" => "Thank you!"]);
    exit;
}

// Check for web links (disabled in this version, but you can enable it)
// $ges = 0;
$txt = strtolower("xx" . $email . $name . $comments);

if (strpos($txt, "www") !== false || strpos($txt, "http") !== false) {
    echo json_encode(["status" => "success", "title" => "Thank you", "message" => ""]);
    exit;
}

/* End spam test block */

// Email details
$to = "info@root-sounds.com";
$from = "root-sounds mailer";
$subject = "Your message to root-sounds";

$body = "Name: $name\n";
$body .= "Email: $email\n";
$body .= "Comments: $comments\n";
// $body .= "Newsletter: $subscribe\n";
$subscribe = isset($_POST['subscribe']) ? $_POST['subscribe'] : 'No'; // Default to "No"

// Send email
$isMailed = mail($to, $subject, $body);

if ($isMailed) {
    echo json_encode(["status" => "success", "title" => "Thank you", "message" => "Your message was sent successfully! We will get back to you shortly."]);
} else {
    echo json_encode(["status" => "error", "title" => "Thank you", "message" => "There was an issue sending your message."]);
}
exit;
?>