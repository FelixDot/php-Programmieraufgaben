<?php
$logFile = './updatev12-access-pseudonymized.log';
$fileOpen = fopen($logFile, 'r');
$hardwareClasses = [];

if ($fileOpen) {
    while (!feof($fileOpen)) {
        $line = fgets($fileOpen);
        $serialPos = strpos($line, 'serial=');
        $specsPos = strpos($line, 'specs=');
        if ($serialPos && $specsPos) {

            $serialPart = substr($line, $serialPos + 7);
            $serial = substr($serialPart, 0, strpos($serialPart, ' '));
            $specsPart = substr($line, $specsPos + 6);
            $specs = substr($specsPart, 0, strpos($specsPart, ' '));

            $decodedSpecs = base64_decode($specs);
            $decompressedSpecs = gzdecode($decodedSpecs);
            $data = json_decode($decompressedSpecs);

            if (isset($data->cpu) && isset($data->mem) && isset($data->disk_data) && isset($data->disk_root)) {

                $cpu = $data->cpu;
                $mem = $data->mem;
                $disk_root = $data->disk_root;
                $disk_data = $data->disk_data;

                // Save hardware components as one String to use as a key
                $hardwareClass = "$cpu $mem RAM $disk_root KB disk root $disk_data KB disk data";

                // Save per hardware class each unique serial number
                if (!isset($hardwareClasses[$hardwareClass])) {
                    $hardwareClasses[$hardwareClass] = [];
                }
                if (!in_array($serial, $hardwareClasses[$hardwareClass])) {
                    $hardwareClasses[$hardwareClass][] = $serial;
                }
            }
        }
    }
}
fclose($fileOpen);

$HardwareSerialCounts = [];
// Count per hardware class the amount of serial number  
foreach ($hardwareClasses as $hardwareClass => $serial) {
    $HardwareSerialCounts[$hardwareClass] = count($serial);
}

foreach ($HardwareSerialCounts as $hardwareClass => $count) {
    echo "Hardware class: $hardwareClass,  amount of serial number : $count<br>";
}
