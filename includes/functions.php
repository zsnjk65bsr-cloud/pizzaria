<?php

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $to): void
{
    header('Location: ' . $to);
    exit;
}

function post(string $key, $default = null)
{
    return $_POST[$key] ?? $default;
}

function get(string $key, $default = null)
{
    return $_GET[$key] ?? $default;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
}

function flash_set(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function flash_get_all(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function cart_init(): void
{
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function cart_total(): float
{
    cart_init();
    $total = 0.0;
    foreach ($_SESSION['cart'] as $item) {
        $qty = (int)($item['qty'] ?? 1);
        $price = (float)($item['unit_price'] ?? 0);
        $total += $qty * $price;
    }
    return round($total, 2);
}

function render(string $view, array $data = []): void
{
    $path = __DIR__ . '/../views/' . $view . '.php';
    if (!is_file($path)) {
        throw new RuntimeException('View not found: ' . $view);
    }

    extract($data, EXTR_SKIP);
    require $path;
}
