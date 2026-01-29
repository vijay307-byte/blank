<?php
function formatCurrency($amount) {
    if ($amount >= 10000000) {
        return "₹" . number_format($amount / 10000000, 2) . " Crores";
    } elseif ($amount >= 100000) {
        return "₹" . number_format($amount / 100000, 2) . " Lakhs";
    } else {
        return "₹" . number_format($amount, 2);
    }
}

$estimatedPrice = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $year         = (int)$_POST["year"];
    $km           = (int)$_POST["km"];
    $engine       = (int)$_POST["engine"];
    $brand        = $_POST["brand"];
    $fuel         = $_POST["fuel"];
    $transmission = $_POST["transmission"];
    $owner        = $_POST["owner"];
    $seats        = (int)$_POST["seats"];

    // ---- Base Price Strategy (₹) ----
    $price = 300000; // 3 lakh baseline

    // Year impact
    $price += ($year - 2010) * 15000;

    // KM depreciation
    $price -= $km * 1;

    // Engine premium
    $enginePremium = [
        796 => -50000, 998 => -20000, 1197 => 0, 1199 => 0,
        1248 => 10000, 1462 => 30000, 1498 => 40000,
        1956 => 80000, 1995 => 90000, 2179 => 120000, 2498 => 150000
    ];
    $price += $enginePremium[$engine] ?? 0;

    // Brand premium
    $brandPremium = [
        "Maruti Suzuki" => 0,
        "Hyundai" => 20000,
        "Tata" => 10000,
        "Mahindra" => 25000,
        "Honda" => 40000,
        "Toyota" => 50000,
        "Kia" => 35000,
        "MG" => 60000,
        "Renault" => 5000
    ];
    $price += $brandPremium[$brand] ?? 0;

    // Fuel premium
    $fuelPremium = ["Petrol" => 0, "Diesel" => 40000, "CNG" => -20000];
    $price += $fuelPremium[$fuel] ?? 0;

    // Transmission premium
    if ($transmission === "Automatic") $price += 30000;

    // Owner depreciation
    if ($owner === "Second") $price -= 30000;
    if ($owner === "Third")  $price -= 60000;

    // Seats premium
    if ($seats == 7) $price += 40000;
    if ($seats == 8) $price += 60000;

    // Floor price safeguard
    $estimatedPrice = max($price, 100000);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Indian Car Price Estimator</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body>

<div class="card card-body mx-auto mt-5 border border-primary rounded-5" style="max-width: 1600px;">
    <h1 class="text-center p-2">Car Price Estimator</h1>

    <form method="post" class="row p-5 " >
        <label class="p-1">Manufacturing Year</label>
        <input type="number" name="year" class="form-control" required min="2010" max="2023">

        <label class="p-1">Kilometers Driven</label>
        <input type="number" name="km" class="form-control" required>

        <label class="p-1">Engine CC</label>
        <select name="engine" class="form-control">
            <option></option>
            <option>796</option>
            <option>998</option>
            <option>1197</option>
            <option>1199</option>
            <option>1248</option>
            <option>1462</option>
            <option>1498</option>
            <option>1956</option>
            <option>1995</option>
            <option>2179</option>
            <option>2498</option>
        </select>

        <label class="p-1">Brand</label>
        <select name="brand" class="form-control">
            <option></option>
            <option>Maruti Suzuki</option>
            <option>Hyundai</option>
            <option>Tata</option>
            <option>Mahindra</option>
            <option>Honda</option>
            <option>Toyota</option>
            <option>Kia</option>
            <option>MG</option>
            <option>Renault</option>
        </select>

        <label class="p-1">Fuel Type</label>
        <select name="fuel" class="form-control">
            <option></option>
            <option>Petrol</option>
            <option>Diesel</option>
            <option>CNG</option>
        </select>

        <label class="p-1">Transmission</label>
        <select name="transmission" class="form-control">
            <option></option>
            <option>Manual</option>
            <option>Automatic</option>
        </select>

        <label class="p-1">Owner Type</label>
        <select name="owner" class="form-control">
            <option></option>
            <option>First</option>
            <option>Second</option>
            <option>Third</option>
        </select>

        <label class="p-1">Seats</label>
        <select name="seats" class="form-control">
            <option></option>
            <option>5</option>
            <option>7</option>
            <option>8</option>
        </select>

        <button type="submit" class="btn btn-primary">Estimate Price</button>
    </form>

    <?php if ($estimatedPrice): ?>
        <div class="result">
            <strong>Estimated Market Value:</strong><br>
            <?= formatCurrency($estimatedPrice); ?>
        </div>
    <?php endif; ?>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

</body>
</html>
