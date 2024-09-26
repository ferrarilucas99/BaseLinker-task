<?php

require_once "./spring.php";

session_start();

$randomRef = "Ref_" . bin2hex(random_bytes(8/2));
$success = $_SESSION["flash"]["success"] ?? false;
$error = $_SESSION["flash"]["error"] ?? false;
$message = $_SESSION["flash"]["message"] ?? "";
$trackingNumber = $_SESSION["flash"]["trackingNumber"] ?? "";
$labelImage = $_SESSION["flash"]["labelImage"] ?? "";
$hasLabelImage = !empty($_SESSION["flash"]["labelImage"]);

unset($_SESSION["flash"]);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_REQUEST["action"])) {
    switch ($_REQUEST["action"]) {
        case "create-shipment":
            createShipment();
            break;
        case "get-label":
            getLabel();
            break;
        default:
            $_SESSION["flash"] = [
                "error" => true,
                "message" => "Invalid or not permitted action."
            ];
            break;
    }

    header("location: /");
    exit;
}

function createShipment(): void
{
    $springCourier = new SpringCourier();

    $order = [
        "sender" => $_POST["sender"],
        "recipient" => $_POST["recipient"]
    ];
    
    $params = [
        "apiKey" => $_POST["params"]["apikey"],
        "labelFormat" => "PDF",
        "service" => "PPTT",
        "shipperReference" => $_POST["params"]["shipperReference"]
    ];

    $newPackage = $springCourier->newPackage($order, $params);

    if (!empty($newPackage["error"])) {
        $_SESSION["flash"] = [
            "error" => true,
            "message" => $newPackage["message"]
        ];

        return;
    } 

    $_SESSION["flash"] = [
        "success" => true,
        "message" => "New shipment created successfully. Tracking number: {$newPackage["trackingNumber"]}",
        "trackingNumber" => $newPackage["trackingNumber"],
    ];

    return;
}

function getLabel(): void
{
    $springCourier = new SpringCourier();

    $packagePDF = $springCourier->packagePDF($_POST["trackingNumber"]);

    if (!empty($packagePDF["error"])) {
        $_SESSION["flash"] = [
            "error" => true,
            "message" => $packagePDF["message"]
        ];

        return;
    } 

    $_SESSION["flash"] = [
        "success" => true,
        "message" => "Success! Download your label.",
        "labelImage" => $packagePDF["labelImage"],
        "trackingNumber" => $packagePDF["trackingNumber"],
    ];

    return;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipment Manager</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        .required {
            color: #FF0000;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <h1 class="text-center">Shipment Manager</h1>

    <main class="container">
        <?php if($success && $message): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if($error && $message): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form action="/?action=create-shipment" method="POST" class="mb-3">
            <div class="mb-2 d-flex row">
                <div class="col-12 col-sm-6 col-md-3 mb-2">
                    <label for="apikey">API Key <span class="required">*</span></label>
                    <input type="text" name="params[apikey]" id="apikey" class="form-control form-control-sm" value="f16753b55cac6c6e">
                </div>
                <div class="col-12 col-sm-6 col-md-3 mb-2">
                    <label for="shipperReference">Shipper Reference <span class="required">*</span></label>
                    <input type="text" name="params[shipperReference]" id="shipperReference" class="form-control form-control-sm" value="<?= htmlspecialchars($randomRef) ?>">
                </div>
            </div>

            <hr>

            <div class="mb-2 d-flex row">
                <div class="col-12 col-md-4 col-xl-3 mb-2">
                    <label for="sender-name">Sender's Name</label>
                    <input type="text" name="sender[Name]" id="sender-name" class="form-control form-control-sm" value="Jan Kowalski">
                </div>
                <div class="col-12 col-md-4 col-xl-3 mb-2">
                    <label for="sender-company">Sender's Company</label>
                    <input type="text" name="sender[Company]" id="sender-company" class="form-control form-control-sm" value="BaseLinker">
                </div>
                <div class="col-12 col-md-4 col-xl-3 mb-2">
                    <label for="sender-address">Sender's Address</label>
                    <input type="text" name="sender[AddressLine1]" id="sender-address" class="form-control form-control-sm" value="Kopernika 10">
                </div>
                <div class="col-12 col-md-4 col-xl-3 mb-2">
                    <label for="sender-city">Sender's City</label>
                    <input type="text" name="sender[City]" id="sender-city" class="form-control form-control-sm" value="Gdansk">
                </div>
                <div class="col-12 col-md-4 col-xl-2 mb-2">
                    <label for="sender-state">Sender's State</label>
                    <input type="text" name="sender[State]" id="sender-state" class="form-control form-control-sm" value="">
                </div>
                <div class="col-12 col-md-4 col-xl-2 mb-2">
                    <label for="sender-zip">Sender's Zip Code <span class="required">*</span></label>
                    <input type="text" name="sender[Zip]" id="sender-zip" class="form-control form-control-sm" value="80208">
                </div>
                <div class="col-12 col-md-4 col-xl-2 mb-2">
                    <label for="sender-country">Sender's Country</label>
                    <input type="text" name="sender[Country]" id="sender-country" class="form-control form-control-sm" value="PL">
                </div>
                <div class="col-12 col-md-4 col-xl-3 mb-2">
                    <label for="sender-phone">Sender's Phone</label>
                    <input type="text" name="sender[Phone]" id="sender-phone" class="form-control form-control-sm" value="666666666">
                </div>
                <div class="col-12 col-md-4 col-xl-3 mb-2">
                    <label for="sender-email">Sender's Email</label>
                    <input type="text" name="sender[Email]" id="sender-email" class="form-control form-control-sm" value="jankowalski@test.com">
                </div>
            </div>

            <hr>

            <div class="mb-2 d-flex row">
                <div class="col-12 col-md-4 col-xl-3 mb-2">
                    <label for="recipient-name">Recipient's Name <span class="required">*</span></label>
                    <input type="text" name="recipient[Name]" id="recipient-name" class="form-control form-control-sm" value="Maud Driant">
                </div>
                <div class="col-12 col-md-4 col-xl-3 mb-2">
                    <label for="recipient-company">Recipient's Company</label>
                    <input type="text" name="recipient[Company]" id="recipient-company" class="form-control form-control-sm" value="Spring GDS">
                </div>
                <div class="col-12 col-md-4 col-xl-3 mb-2">
                    <label for="recipient-address">Recipient's Address <span class="required">*</span></label>
                    <input type="text" name="recipient[AddressLine1]" id="recipient-address" class="form-control form-control-sm" value="Strada Foisorului, Nr. 16, Bl. F11C, Sc. 1, Ap. 10">
                </div>
                <div class="col-12 col-md-4 col-xl-3 mb-2">
                    <label for="recipient-city">Recipient's City <span class="required">*</span></label>
                    <input type="text" name="recipient[City]" id="recipient-city" class="form-control form-control-sm" value="Bucuresti, Sector 3">
                </div>
                <div class="col-12 col-md-4 col-xl-2 mb-2">
                    <label for="recipient-state">Recipient's State</label>
                    <input type="text" name="recipient[State]" id="recipient-state" class="form-control form-control-sm" value="">
                </div>
                <div class="col-12 col-md-4 col-xl-2 mb-2">
                    <label for="recipient-zip">Recipient's Zip Code <span class="required">*</span></label>
                    <input type="text" name="recipient[Zip]" id="recipient-zip" class="form-control form-control-sm" value="031179">
                </div>
                <div class="col-12 col-md-4 col-xl-2 mb-2">
                    <label for="recipient-country">Recipient's Country <span class="required">*</span></label>
                    <input type="text" name="recipient[Country]" id="recipient-country" class="form-control form-control-sm" value="RO">
                </div>
                <div class="col-12 col-md-4 col-xl-3 mb-2">
                    <label for="recipient-phone">Recipient's Phone</label>
                    <input type="text" name="recipient[Phone]" id="recipient-phone" class="form-control form-control-sm" value="555555555">
                </div>
                <div class="col-12 col-md-4 col-xl-3 mb-2">
                    <label for="recipient-email">Recipient's Email</label>
                    <input type="text" name="recipient[Email]" id="recipient-email" class="form-control form-control-sm" value="mauddriant@test.com">
                </div>
            </div>
    
            <button type="submit" class="btn btn-primary">Create Shipment</button>
        </form>
    
        <form action="/?action=get-label" method="POST" class="mb-3">
            <div class="d-flex row">
                <div class="col-12 col-md-4">
                    <div class="mb-2">
                        <label for="tracking-number">Tracking Number <span class="required">*</span></label>
                        <input type="text" id="tracking-number" name="trackingNumber" placeholder="Enter tracking number" value="<?= htmlspecialchars($trackingNumber) ?>" class="form-control form-control-sm" >
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Download Label</button>
                </div>
            </div>
        </form>
        
        <small class="mb-3">Fields with <span class="required">*</span> are required</small>
    </main>

    <div class="modal" id="myModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Shipment label preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if ($labelImage): ?>
                        <iframe 
                            src="data:application/pdf;base64,<?= $labelImage ?>" 
                            width="100%" 
                            height="650"
                        >
                    </iframe>
                <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <a href="#" id="downloadLabel" class="d-none" download="label.pdf"></a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        const myModal = document.getElementById("myModal");
        const modal = new bootstrap.Modal(myModal);
        const downloadLink = document.getElementById('downloadLabel');
        const labelImage = <?= json_encode($labelImage) ?>;
        const hasLabelImage = <?= json_encode($hasLabelImage) ?>;

        if (hasLabelImage && labelImage) {
            modal.show();

            const pdfBlob = new Blob([new Uint8Array(atob(labelImage).split("").map(char => char.charCodeAt(0)))], { type: 'application/pdf' });
            const pdfUrl = URL.createObjectURL(pdfBlob);

            downloadLink.href = pdfUrl;
            downloadLink.click();

            URL.revokeObjectURL(pdfUrl);
        }
    </script>
</body>
</html>