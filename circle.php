<?php
function circleArea(float $radius): float {
    return pi() * pow($radius, 2);   // area = πr²
}

$area = null;
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = trim($_POST["radius"] ?? "");

    if (!is_numeric($input) || $input <= 0) {
        $error = "Please enter a positive number.";
    } else {
        $radius = floatval($input);
        $area = round(circleArea($radius), 2);
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Area of a Circle</title></head>
<body>
    <h2>Area of a Circle</h2>
    <form method="post">
        Radius: <input type="text" name="radius">
        <button type="submit">Calculate</button>
    </form>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($area !== null): ?>
        <p>Radius: <?= htmlspecialchars($radius) ?></p>
        <p>Area: <strong><?= number_format($area, 2) ?></strong> square units</p>
    <?php endif; ?>
</body>
</html>