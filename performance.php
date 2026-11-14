<?php
// Learning areas: label => form field name
$areas = [
    "English"            => "english",
    "Math"               => "math",
    "Kiswahili"          => "kiswahili",
    "Integrated Science" => "science",
    "Creative Arts"      => "arts",
    "Social Studies"     => "social",
];

// Classify a mark (1-100) into code, description and points
function classify(int $marks): array {
    if ($marks >= 90) return ["EE1", "Exceeding Expectation 1", 8];
    if ($marks >= 75) return ["EE2", "Exceeding Expectation 2", 7];
    if ($marks >= 58) return ["ME1", "Meeting Expectation 1",   6];
    if ($marks >= 41) return ["ME2", "Meeting Expectation 2",   5];
    if ($marks >= 31) return ["AE1", "Approaching Expectation 1", 4];
    if ($marks >= 21) return ["AE2", "Approaching Expectation 2", 3];
    if ($marks >= 11) return ["BE1", "Below Expectation 1",    2];
    return                   ["BE2", "Below Expectation 2",    1];
}

// Classify general performance from total points.
// Method: average points per learning area, rounded, then mapped to a level.
function classifyGeneral(int $totalPoints, int $numAreas): array {
    $avg = (int) round($totalPoints / $numAreas);
    $avg = max(1, min(8, $avg));
    $levels = [
        8 => ["EE1", "Exceeding Expectation 1"],
        7 => ["EE2", "Exceeding Expectation 2"],
        6 => ["ME1", "Meeting Expectation 1"],
        5 => ["ME2", "Meeting Expectation 2"],
        4 => ["AE1", "Approaching Expectation 1"],
        3 => ["AE2", "Approaching Expectation 2"],
        2 => ["BE1", "Below Expectation 1"],
        1 => ["BE2", "Below Expectation 2"],
    ];
    return $levels[$avg];
}

$results = [];
$totalPoints = 0;
$errors = [];
$general = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    foreach ($areas as $label => $field) {
        $input = trim($_POST[$field] ?? "");
        if (!is_numeric($input) || $input < 1 || $input > 100) {
            $errors[] = "$label: enter a mark between 1 and 100.";
            continue;
        }
        $marks = (int) $input;
        [$code, $desc, $points] = classify($marks);
        $results[$label] = [$marks, $code, $desc, $points];
        $totalPoints += $points;
    }

    if (empty($errors)) {
        $general = classifyGeneral($totalPoints, count($areas));
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Grade 6 Performance</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #999; padding: 6px 12px; text-align: left; }
        th { background: #eee; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>Grade 6 Learner Performance</h2>

    <form method="post">
        <?php foreach ($areas as $label => $field): ?>
            <p>
                <?= htmlspecialchars($label) ?> (marks):
                <input type="text" name="<?= $field ?>"
                       value="<?= htmlspecialchars($_POST[$field] ?? "") ?>">
            </p>
        <?php endforeach; ?>
        <button type="submit">Calculate</button>
    </form>

    <?php foreach ($errors as $e): ?>
        <p class="error"><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>

    <?php if ($general !== null): ?>
        <table>
            <tr>
                <th>Learning Area</th><th>Marks</th>
                <th>Performance</th><th>Description</th><th>Points</th>
            </tr>
            <?php foreach ($results as $label => [$marks, $code, $desc, $points]): ?>
                <tr>
                    <td><?= htmlspecialchars($label) ?></td>
                    <td><?= $marks ?></td>
                    <td><?= $code ?></td>
                    <td><?= $desc ?></td>
                    <td><?= $points ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="4">Total Points (out of <?= count($areas) * 8 ?>)</th>
                <th><?= $totalPoints ?></th>
            </tr>
        </table>

        <p><strong>General Performance:</strong>
           <?= $general[0] ?> - <?= $general[1] ?></p>
    <?php endif; ?>
</body>
</html>