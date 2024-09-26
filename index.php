<?php

require_once "./spring.php";

$springCourier = new SpringCourier();
$randomRef = "Ref_" . bin2hex(random_bytes(8/2));

$order = [
    "sender" => [
        "Name" => "Jan Kowalski",
        "Company" => "BaseLinker",
        "AddressLine1" => "Kopernika 10",
        "City" => "Gdansk",
        "State" => "",
        "Zip" => "80208",
        "Country" => "PL",
        "Phone" => "666666666",
        "Email" => "jankowalski@test.com",
    ],
    "recipient" => [
        "Name" => "Maud Driant",
        "Company" => "Spring GDS",
        "AddressLine1" => "Strada Foisorului, Nr. 16, Bl. F11C, Sc. 1, Ap. 10",
        "City" => "Bucuresti, Sector 3",
        "State" => "",
        "Zip" => "031179",
        "Country" => "RO",
        "Phone" => "555555555",
        "Email" => "mauddriant@test.com",
    ]
];

$params = [
    "apiKey" => "f16753b55cac6c6e",
    "labelFormat" => "PDF",
    "service" => "PPTT",
    "shipperReference" => $randomRef
];

$newPackage = $springCourier->newPackage($order, $params);

if (isset($newPackage["error"]) && $newPackage["error"]) {
    echo $newPackage["message"];
} 

if (isset($newPackage["Shipment"]["TrackingNumber"]) && is_string($newPackage["Shipment"]["TrackingNumber"])) {
    $packagePDF = $springCourier->packagePDF($newPackage["Shipment"]["TrackingNumber"]);

    if (isset($packagePDF["error"]) && $packagePDF["error"]) {
        echo "Error: {$packagePDF["message"]}";
    } else {
        header("Content-type: application/pdf");
        echo base64_decode($packagePDF["Shipment"]["LabelImage"]);
    }
}