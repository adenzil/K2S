<?php

require('./K2S.php');

echo "\nWelcome, To fech your profile details from K2S please enter your email id and password \n\n";

$handle = fopen ("php://stdin","r");

// Validate email
do {
	echo "Please enter a valid email id : ";
	$email = trim(fgets($handle));
} while (preg_match("/^[a-zA-Z-' ]*$/",$email));

echo "\nYour password : ";
$password = trim(fgets($handle));
echo "\n";

$k2s = new K2S($email, $password);
$k2s->getProfileData();

