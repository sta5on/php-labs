<?php

$day = date('N');

switch ($day) {
    case 1:
        $dayName = 'Monday';
        break;
    case 2:
        $dayName = 'Tuesday';
        break;
    case 3:
        $dayName = 'Wednesday';
        break;
    case 4:
        $dayName = 'Thursday';
        break;
    case 5:
        $dayName = 'Friday';
        break;
    case 6:
        $dayName = 'Saturday';
        break;
    case 7:
        $dayName = 'Sunday';
        break;
    default:
        $dayName = 'Unknown';
}

$johnSchedule = in_array($day, [1, 3, 5]) ? '8:00-12:00' : 'Нерабочий день';
$janeSchedule = in_array($day, [2, 4, 6]) ? '12:00-16:00' : 'Нерабочий день';

echo "<h1>Today is $dayName</h1>";

echo "<h2>Расписание на сегодня</h2>";
echo "<table border='1' cellpadding='8' cellspacing='0'>";
echo "<tr><th>№</th><th>Фамилия Имя</th><th>График работы</th></tr>";
echo "<tr><td>1</td><td>John Styles</td><td>$johnSchedule</td></tr>";
echo "<tr><td>2</td><td>Jane Doe</td><td>$janeSchedule</td></tr>";
echo "</table>";
echo "<hr>";


echo "<h2>Simple for() loop</h1>";
$a = 0;
$b = 0;

echo "Start of the loop: a = $a, b = $b";
echo "<br>";
for ($i = 0; $i <= 5; $i++) {
    echo "Iteration #" . ($i + 1);
    echo " | Current A = " . $a;

    echo " | Current B= " . $b . "<br>";

    $a += 10;
    $b += 5;
}

echo "End of the loop: a = $a, b = $b";

echo "<hr>";
echo "<h2>While loop</h1>";
$aWhile = 0;
$bWhile = 0;
$iWhile = 0;

echo "Start of the while loop: a = $aWhile, b = $bWhile";
echo "<br>";
while ($iWhile <= 5) {
    echo "Iteration #" . ($iWhile + 1);
    echo " | Current A = " . $aWhile;
    echo " | Current B = " . $bWhile . "<br>";

    $aWhile += 10;
    $bWhile += 5;
    $iWhile++;
}
echo "End of the while loop: a = $aWhile, b = $bWhile";

echo "<hr>";

echo "<h2>Do-While loop</h1>";
$aDoWhile = 0;
$bDoWhile = 0;
$iDoWhile = 0;

echo "Start of the do-while loop: a = $aDoWhile, b = $bDoWhile";
echo "<br>";
do {
    echo "Iteration #" . ($iDoWhile + 1);
    echo " | Current A = " . $aDoWhile;
    echo " | Current B = " . $bDoWhile . "<br>";

    $aDoWhile += 10;
    $bDoWhile += 5;
    $iDoWhile++;
} while ($iDoWhile <= 5);
echo "End of the do-while loop: a = $aDoWhile, b = $bDoWhile";

