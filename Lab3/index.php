<?php
$dayOfWeek = date('N');

// John Styles
if (in_array($dayOfWeek, [1, 3, 5])) {
    $johnSchedule = "8:00-12:00";
} else {
    $johnSchedule = "Нерабочий день";
}

// Jane Doe
if (in_array($dayOfWeek, [2, 4, 6])) {
    $janeSchedule = "12:00-16:00";
} else {
    $janeSchedule = "Нерабочий день";
}

echo "<b>№ | Фамилия Имя   | График работы </b><br>";
echo "1 | John Styles   | $johnSchedule <br>";
echo "2 | Jane Doe      | $janeSchedule <br>";
?>

<?php
echo "<h3>1. Цикл FOR с выводом промежуточных значений</h3>";
$a = 0;
$b = 0;

for ($i = 0; $i <= 5; $i++) {
    $a += 10;
    $b += 5;
    echo "Шаг $i: a = $a, b = $b <br>";
}
echo "<b>End of the loop: a = $a, b = $b</b><br><br>";
?>

<?php
echo "<h3>2. Цикл WHILE</h3>";
$a = 0;
$b = 0;
$i = 0;

while ($i <= 5) {
    $a += 10;
    $b += 5;
    echo "Шаг $i: a = $a, b = $b <br>";
    $i++;
}
echo "<b>End of the loop: a = $a, b = $b</b><br><br>";
?>

<?php
echo "<h3>3. Цикл DO-WHILE</h3>";
$a = 0;
$b = 0;
$i = 0;

do {
    $a += 10;
    $b += 5;
    echo "Шаг $i: a = $a, b = $b <br>";
    $i++;
} while ($i <= 5);

echo "<b>End of the loop: a = $a, b = $b</b><br>";
?>