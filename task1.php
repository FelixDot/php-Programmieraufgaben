<?php
    $logFile = './updatev12-access-pseudonymized.log';
    // Open a file for read only
    $fileOpen = fopen($logFile, 'r');

    // Check if file opened successfully
    if ($fileOpen) {

        // Check if pointer is not end-of-file 
        while (!feof($fileOpen)) {
            
            // Get line
            $line = fgets($fileOpen);

            // Get the part of the line that begins with "serial="
            $serialPart = substr($line, strpos($line, 'serial=') + 7);

            // Get serial number by going to the next space
            $serial = trim(substr($serialPart, 0, strpos($serialPart, ' ')));

            // Count the serial number, if it is not already in the array $serials, initialize it with 1
            if (isset($serials[$serial])) {
                $serials[$serial]++;
            } else {
                $serials[$serial] = 1;
            }

        }
    }

    // Close file
    fclose($fileOpen);
    // Sort array in decending order
    arsort($serials);
    $topSerials = array_slice($serials, 0, 10);
    foreach ($topSerials as $serial => $count) {
        echo "license serial number: $serial number of times are they trying to access the server: $count <br>";
    }  
?>