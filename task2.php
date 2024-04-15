<?php
$logFile = './updatev12-access-pseudonymized.log';
$fileOpen = fopen($logFile, 'r');
$serialMacMap = [];

if ($fileOpen) {
    while (!feof($fileOpen)) {
        $line = fgets($fileOpen);

        $serialPos = strpos($line, 'serial=');
        $specsPos = strpos($line, 'specs=');

        $serialPart = substr($line, $serialPos + 7);
        $serial = trim(substr($serialPart, 0, strpos($serialPart, ' ')));
        $specsPart = substr($line, $specsPos + 6);
        $specs = trim(substr($specsPart, 0, strpos($specsPart, ' ')));

        // Decode and decompress specs
        $decodedSpecs = base64_decode($specs);
        $decompressedSpecs = gzdecode($decodedSpecs);

        // Decode json object
        $data = json_decode($decompressedSpecs);


        // Store per serial each unique mac address
        if (isset($data->mac))
            $macAddress = $data->mac; {
            if (!isset($serialMacMap[$serial])) {
                $serialMacMap[$serial] = [];
            }
            if (!in_array($macAddress, $serialMacMap[$serial])) {
                $serialMacMap[$serial][] = $macAddress;
            }
        }
    }
    fclose($fileOpen);
    $serialCounts = [];

    // Count per serial the number of mac addresses
    foreach ($serialMacMap as $serial => $macAddresses) {
        $serialCounts[$serial] = count($macAddresses);
    }
    arsort($serialCounts);
    $topSerials = array_slice(array_keys($serialCounts), 0, 10);
    foreach ($topSerials as $serial) {
        echo "Serialnumber: $serial, number of devices installed on $serialCounts[$serial] <br>";
    }
}
