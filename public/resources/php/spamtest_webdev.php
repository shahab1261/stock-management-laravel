<?php 
//form data
$name = strip_tags($_POST['name']);
$email = strip_tags($_POST['email']);
$comments = strip_tags($_POST['comments']);

// test if a bot has entered data
$botty = strip_tags($_POST['botty']);

if($botty != NULL){
echo "Gotcha.";
exit;
}

if (!empty($comments));
	echo "No message!";
	exit;
	
// check for web links and treat as spam 
 $ges = 0 ;
 $txt = "xx" . $email . $name . $os . $daw . $comments ;
   
 $check = strtolower($txt) ;
 $posfnd = strpos($check,"www");       $ges = $ges + $posfnd ;
 $posfnd = strpos($check,"http");      $ges = $ges + $posfnd ;
 $posfnd = strpos($check,"<a");        $ges = $ges + $posfnd ;
 
echo "$txt $ges " ; 
// exit;

 if ( $ges > 0 )
		{
		echo "Spam in one or more text areas."; 
		exit;
      	}

echo "Mailing this stuff now...";
?>