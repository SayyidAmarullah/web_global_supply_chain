<?php
$json = file_get_contents('https://raw.githubusercontent.com/marchah/sea-ports/master/lib/ports.json');
$data = json_decode($json, true);
if(is_array($data)) {
    $first_few = array_slice($data, 0, 3);
    print_r($first_few);
} else {
    echo "Failed to parse.";
}
