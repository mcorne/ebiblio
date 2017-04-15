<?php
list($base_uri) = explode('/actions', $_SERVER['REQUEST_URI']);
$base_url = sprintf('https://a:a@%s/%s', $_SERVER['HTTP_HOST'], $base_uri);

header("Location: $base_url");
