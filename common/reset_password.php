<?php
$to = "mcorne@yahoo.com";
$subject = "test";
$txt = "test";
$headers = "From: test@test.com";

mail($to, $subject, $txt, $headers);
