<?php // TODO: fix !!!
$to      = 'mcorne@yahoo.com';
$subject = 'the subject';
$message = 'hello';
$headers = 'From: no-reply@gmail.com';

$result = mail($to, $subject, $message, $headers);

echo $result;
