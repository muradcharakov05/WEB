<?php
session_start();

/**
 * 1. Базовый класс Product
 */
class Product {
    private $name;
    private $price;
    private $quantity;

    public function __construct($name, $price, $quantity = 0) {
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    // Геттеры
    public function getName() { return $this->name; }
    public function getPrice() { return $this->price; }
    public function getQuantity() { return $this->quantity; }

    // Метод для изменения количества
    public function add($amount) {
        $this->quantity += $amount;
        if ($this->quantity < 0) $this->quantity = 0;
    }

    // 2. Метод вывода карточки товара
    public function getDisplay($index) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px; border-radius: 8px; width: 250px; display: inline-block; vertical-align: top;'>";
        echo "<h3>{$this->name}</h3>";
        echo "<p>Цена: <b>{$this->price} руб.</b></p>";
        $this->displaySpecialFeatures(); // Доп. характеристики для наследников
        echo "<p>В корзине: <b>{$this->quantity}</b></p>";
        
        // Формы для изменения количества
        echo "<form method='post' style='display:inline;'>
                <input type='hidden' name='product_id' value='{$index}'>
                <button type='submit' name='action' value='add'> + </button>
                <button type='submit' name='action' value='remove'> - </button>
              </form>";
        echo "</div>";
    }

    // Вспомогательный метод для переопределения в дочерних классах
    protected function displaySpecialFeatures() {
        echo "<p><small>Категория: Общий товар</small></p>";
    }
}

/**
 * 7. Дочерний класс (Электроника)
 */
class ElectronicProduct extends Product {
    private $warranty; // Срок гарантии

    public function __construct($name, $price, $warranty, $quantity = 0) {
        parent::__construct($name, $price, $quantity);
        $this->warranty = $warranty;
    }

    // Переопределяем вывод характеристик
    protected function displaySpecialFeatures() {
        echo "<p style='color: blue;'><small>Гарантия: {$this->warranty} мес.</small></p>";
    }
}

/**
 * 4. Управление данными в сессии
 */

// Инициализация тестовых данных, если сессия пуста
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [
        new Product("Кофе заморский", 500),
        new ElectronicProduct("Смартфон", 25000, 12),
        new ElectronicProduct("Наушники", 3500, 6),
        new Product("Печенье", 150)
    ];
}

// 5. Обработка нажатий кнопок
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $id = $_POST['product_id'];
    $action = $_POST['action'];

    if (isset($_SESSION['cart'][$id])) {
        if ($action === 'add') {
            $_SESSION['cart'][$id]->add(1);
        } elseif ($action === 'remove') {
            $_SESSION['cart'][$id]->add(-1);
        }
    }
    // Перезагрузка страницы для предотвращения повторной отправки формы
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

/**
 * 6. Подсчет общей стоимости
 */
$totalCost = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalCost += $item->getPrice() * $item->getQuantity();
}
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Моя корзина</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; padding: 20px; }
        .cart-summary { background: #fff; padding: 20px; margin-top: 20px; border-top: 3px solid #333; }
    </style>
</head>
<body>

    <h1>Каталог товаров</h1>
    
    <div class="products-list">
        <?php 
        // Вывод товаров
        foreach ($_SESSION['cart'] as $index => $product) {
            $product->getDisplay($index);
        }
        ?>
    </div>

    <div class="cart-summary">
        <h2>Итого в корзине:</h2>
        <p style="font-size: 1.5em; color: green;">
            Суммарная стоимость: <b><?php echo $totalCost; ?> руб.</b>
        </p>
        <form method="post">
            <button type="submit" name="clear" style="color: red;">Очистить корзину</button>
        </form>
    </div>

    <?php
    // Логика очистки (для удобства тестирования)
    if (isset($_POST['clear'])) {
        session_destroy();
        header("Location: " . $_SERVER['PHP_SELF']);
    }
    ?>
</body>
</html>
