<?php

//form data
$name = $_POST['name'];
$email = $_POST['email'];
$subscribe = $_POST['subscribe'];
$comments = $_POST['comments'];

// check for empty message
if (empty ($_POST["comments"])) {
	echo "Thank you!";
	exit;
	}

/* begin spam test block */

// test if a bot has entered data into invisible field
$botty = $_POST['botty'];

if($botty != NULL){
echo "Thank you!";
exit;
}

// check for web links
 $ges = 0 ;
 $txt = "xx" . $email . $name . $comments ;
   
 $check = strtolower($txt) ;
//  $posfnd = strpos($check,"www");                    $ges = $ges + $posfnd ;
//  $posfnd = strpos($check,"http");                  $ges = $ges + $posfnd ;
//  $posfnd = strpos($check,"any_string");        $ges = $ges + $posfnd ;
 
 
// echo "$txt $ges " ; 
 if ( $ges > 0 )
		{
		echo "Thank you!";
		exit;
      	}

/* end spam test block */

// the email address where the script will email the form results to
$to = "info@root-sounds.com";

// where the email will look like it is sent from
$from = "root-sounds mailer";

$subject = "Your message to root-sounds";

$body .= "Name: " . $name . "
";
$body .= "Email: " . $email . "
";
$body .= "Comments: " . $comments . "
";
$body .= "Newsletter: " . $subscribe . "
";

$isMailed = mail($to, $subject, $body );

header("Location: /thanks_contact.shtml");
		
?>