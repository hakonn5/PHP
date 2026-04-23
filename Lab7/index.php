<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/src/ValidatorInterface.php';
require_once __DIR__ . '/src/Validators.php';
require_once __DIR__ . '/src/functions.php';

$dataFile = __DIR__ . '/data.json';
$successMessage = '';
$errorMessages = [];

$engine = $_GET['engine'] ?? 'native';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $description = htmlspecialchars(trim($_POST['description'] ?? ''));
    $startDate = htmlspecialchars(trim($_POST['start_date'] ?? ''));
    $category = htmlspecialchars(trim($_POST['category'] ?? ''));
    $isDaily = isset($_POST['is_daily']);

    $reqVal = new RequiredValidator();
    $minLenVal = new MinLengthValidator(3);

    if ($err = $reqVal->validate($name) ?? $minLenVal->validate($name)) {
        $errorMessages[] = "Название: " . $err;
    }
    if ($err = $reqVal->validate($description)) {
        $errorMessages[] = "Описание: " . $err;
    }
    if ($err = $reqVal->validate($startDate)) {
        $errorMessages[] = "Дата начала: " . $err;
    }

    if (empty($errorMessages)) {
        $habit = [
            'id' => uniqid('hab_'),
            'name' => $name,
            'description' => $description,
            'start_date' => $startDate,
            'category' => $category,
            'is_daily' => $isDaily,
            'created_at' => date('Y-m-d H:i:s')
        ];

        saveHabit($dataFile, $habit);
        $successMessage = "Привычка успешно добавлена!";
    }
}

$habits = getHabits($dataFile);
$sortColumn = $_GET['sort'] ?? 'created_at';

usort($habits, function ($a, $b) use ($sortColumn) {
    if ($sortColumn === 'name') {
        return strcmp($a['name'], $b['name']);
    } elseif ($sortColumn === 'category') {
        return strcmp($a['category'], $b['category']);
    }
    return strtotime($b['created_at']) <=> strtotime($a['created_at']);
});

if ($engine === 'twig') {
    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates/twig');
    $twig = new \Twig\Environment($loader, [
        'cache' => false,
    ]);

    // Custom Twig filter for Category Icon
    $categoryIconFilter = new \Twig\TwigFilter('category_icon', function ($category) {
        $icon = '📌';
        if ($category === 'Здоровье')
            $icon = '🏥';
        elseif ($category === 'Обучение')
            $icon = '📚';
        elseif ($category === 'Спорт')
            $icon = '🏋️';
        elseif ($category === 'Финансы')
            $icon = '💰';
        return $icon . ' ' . $category;
    });
    $twig->addFilter($categoryIconFilter);

    echo $twig->render('index.twig', [
        'successMessage' => $successMessage,
        'errorMessages' => $errorMessages,
        'habits' => $habits,
        'engine' => $engine
    ]);
} else {
    // Native PHP template
    require __DIR__ . '/templates/native/layout.php';
}