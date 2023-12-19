<?php
error_reporting(0);
$letters = range('A', 'Z');
$availableDrivesArray = array();
$driveLetter = 0;
while ($driveLetter < 26) {
    $currentLetter = $letters[$driveLetter];
    if (disk_total_space($currentLetter . ":") !== false) {
        array_push($availableDrivesArray, $currentLetter);
    }
    $driveLetter++;
}
error_reporting(E_ALL);

$availableDrives = 0;
while ($availableDrives < count($availableDrivesArray)) {
    $freeSpace = round(disk_free_space($availableDrivesArray[$availableDrives] . ":") / 1024 / 1024 / 1024, 1);
    $totalSpace = round(disk_total_space($availableDrivesArray[$availableDrives] . ":") / 1024 / 1024 / 1024, 1);
    $invertedSpace = $totalSpace - $freeSpace;
    echo "
    <div id='drive-info'>
    <h1>{$availableDrivesArray[$availableDrives]}:</h1>
    <meter value='$invertedSpace' min='0' max='$totalSpace'></meter>
    <p>{$freeSpace}GB available</p>
    <p>{$totalSpace}GB total</p>
    </div>
    ";
    $availableDrives++;
}