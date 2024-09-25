<?php

require_once "./spring.php";

$springCourier = new SpringCourier();

$springCourier->newPackage([], []);
echo "<br>";
$springCourier->packagePDF("");