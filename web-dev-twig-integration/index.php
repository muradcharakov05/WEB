<?php

require_once 'vendor/autoload.php';
echo "Автозагрузчик подключен<br>";

$loader = new \Twig\Loader\FilesystemLoader('templates');
echo "Загрузчик шаблонов создан<br>";

$twig = new \Twig\Environment($loader);
echo "Окружение Twig создано<br>";


$items = [
    [
        'name' => 'Tesla Model 3',
        'brand' => 'Tesla',
        'price' => 45000,
        'year' => 2024,
        'electric' => true
    ],
    [
        'name' => 'BMW X5',
        'brand' => 'BMW',
        'price' => 65000,
        'year' => 2023,
        'electric' => false
    ],
    [
        'name' => 'Toyota Camry',
        'brand' => 'Toyota',
        'price' => 28000,
        'year' => 2024,
        'electric' => false
    ],
    [
        'name' => 'Porsche Taycan',
        'brand' => 'Porsche',
        'price' => 105000,
        'year' => 2024,
        'electric' => true
    ],
    [
        'name' => 'Volkswagen ID.4',
        'brand' => 'Volkswagen',
        'price' => 40000,
        'year' => 2023,
        'electric' => true
    ]
];

$user = [
    'name' => 'Мурад Чараков',
    'role' => 'admin',
    'email' => 'MuradCharakov@sfedu.ru'
];

$stats = [
    'total' => array_sum(array_column($items, 'price')),
    'electric_count' => count(array_filter($items, fn($i) => $i['electric'])),
    'fuel_count' => count(array_filter($items, fn($i) => !$i['electric']))
];

echo "Данные подготовлены<br>";


echo $twig->render('index.twig', [
    'page_title' => 'Каталог автомобилей',
    'user' => $user,
    'items' => $items,
    'stats' => $stats
]);

?>