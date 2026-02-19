<?php

$conn = false;

while (!$conn) {

    try {
        $conn = mysqli_connect(getenv('MYSQL_HOST'), getenv('MYSQL_USER'), getenv('MYSQL_ROOT_PASSWORD'), getenv('MYSQL_DATABASE'));
    } catch (\Exception $e) {
        echo 'Waiting for the DB connection...', PHP_EOL;
        sleep(5);
    }

}

echo 'Connected', PHP_EOL;
