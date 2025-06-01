<?php

use setasign\Fpdi\Tcpdf\Fpdi;

// Загрузка исходного PDF
$pdf->setSourceFile($assetsPath . '/tpl/geico_original.pdf');
$pageCount = $pdf->setSourceFile($assetsPath . '/tpl/geico_original.pdf');

$name = $data['name'];
$address1 = $data['addressLine1'];
$town = $data['town'];
$state = $data['state'];
$zip = $data['zip'];
$vehicleYear = $data['vehicleYear'];
$vehicleModel = $data['vehicleModel'];
$vin = $data['vin'];
$effectiveDate = $data['effectiveDate'];
$additional_driver = $data['additionalDriver'] ?? '';

$number = 'IDCOVLTR (08-19)';
$number2 = 'U4' . $state . ' (06-20)';

// Обработка каждой страницы
for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    // Импорт страницы
    $templateId = $pdf->importPage($pageNo);
    $size = $pdf->getTemplateSize($templateId);

    // Создание новой страницы
    $pdf->AddPage($size['orientation'], [$size['width'], $size['height'] + 15]);
    $pdf->useTemplate($templateId);

    // Замена текста по координатам
    $pdf->setFont('helvetica', '', 10);
    $pdf->setTextColor(0, 0, 0);
    $pn = "4" . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . "-" . rand(0,9) . rand(0,9) . "-" . rand(0,9) . rand(0,9) . "-" . rand(0,9) . rand(0,9);

    switch ($pageNo) {
        case 1: // Страница 1

            //Policy Number
            $pdf->setXY(150, 10);
            $pdf->Cell(0, 10, 'Policy Number: ' . $pn);
            // Замена адреса
            $pdf->setXY(120, 100);
            $pdf->MultiCell(100, 5, "{$name}\n{$address1}\n{$town} {$state} {$zip}", 0, 'L');

            // Замена имени водителя
            $pdf->setXY(30, 120);
            $pdf->Cell(0, 10, $name);

            $pdf->setXY(120, 120);
            $pdf->Cell(0, 10, $additional_driver);

            //Номер внизу страницы
            $pdf->setXY(10, 260);
            $pdf->Cell(0, 10, $number);
            break;

        case 3: // Страница 3
            $pdf->setFont('helvetica', '', 10);
            $pdf->setXY(16, 62);
            $pdf->MultiCell(0, 10, "Here are your Evidence of Liability Insurance Cards. Two cards have been provided for each vehicle insured. One card must be carried in the proper insured vehicle. Proof of insurance is required to register or renew the registration of your vehicle. A law enforcement officer can ask you to prove that you have liability insurance meeting the basic requirements of California law. A violation of these requirements can result in a fine of up to: $1,000 for the first time; $2,000 for additional times. Also, a judge can have your vehicle impounded. False proof of insurance may result in a fine up to $750 and 30 days in prison", 0, 'L');
            // Первая карточка
            $pdf->setXY(120, 95);
            $pdf->MultiCell(100, 5, "{$name}\n{$address1}\n{$town} {$state} {$zip}", 0, 'L');

            $pdf->setFont('helvetica', 'B', 10);
            $pdf->setXY(16, 163);
            $pdf->Cell(0, 10, "California Evidence of Liability Insurance");

            $pdf->setFont('helvetica', 'B', 8);
            $pdf->setXY(36, 180);
            $pdf->Cell(0, 10, $vehicleYear . " " . $vehicleModel);

            $pdf->setXY(36, 185);
            $pdf->Cell(0, 10, "Vehicle ID No.");

            $pdf->setFont('helvetica', '', 8);
            $pdf->setXY(56, 185);
            $pdf->Cell(0, 10, $vin);

            $pdf->setFont('helvetica', '', 10);
            $pdf->setXY(16, 195);
            $pdf->Cell(0, 10, $pn);

            $pdf->setXY(45, 195);
            $pdf->Cell(0, 10, date("m/d/Y", strtotime($effectiveDate)));

            $dateAt = strtotime('+6 MONTH', strtotime($effectiveDate));

            $newDate = date('m/d/Y', $dateAt);
            $pdf->setXY(70, 195);
            $pdf->Cell(0, 10, $newDate);

            $pdf->setFont('helvetica', '', 8);
            $pdf->setXY(16, 205);
            $pdf->Cell(0, 10, $name);

            $pdf->setXY(60, 208);
            $pdf->MultiCell(100, 5, "{$address1}\n{$town} {$state} {$zip}", 0, 'L');

            $pdf->setFont('helvetica', 'B', 10);
            $pdf->setXY(16, 220);
            $pdf->Cell(0, 10, $vehicleYear . " " . $vehicleModel);

            $pdf->setFont('helvetica', '', 8);
            $pdf->setXY(16, 231);
            $pdf->MultiCell(100, 5, $additional_driver, 0, 'L');

            $pdf->setFont('helvetica', '', 6);
            $pdf->setXY(16, 265);
            $pdf->MultiCell(80, 5, "The coverage provided by this policy meets the minimum requirements of section 16056 or 16500.5 of the California Vehicle Code, minimum liability limits prescribed by law.", 0, 'L');


            // Вторая карточка
            $pdf->setXY(125, 163);
            $pdf->Cell(0, 10, "California Evidence of Liability Insurance");

            $pdf->setFont('helvetica', 'B', 8);
            $pdf->setXY(145, 180);
            $pdf->Cell(0, 10, $vehicleYear . " " . $vehicleModel);

            $pdf->setXY(145, 185);
            $pdf->Cell(0, 10, "Vehicle ID No.");

            $pdf->setFont('helvetica', '', 8);
            $pdf->setXY(165, 185);
            $pdf->Cell(0, 10, $vin);

            $pdf->setFont('helvetica', '', 10);
            $pdf->setXY(125, 195);
            $pdf->Cell(0, 10, $pn);

            $pdf->setXY(154, 195);
            $pdf->Cell(0, 10, date("m/d/Y", strtotime($effectiveDate)));

            $dateAt = strtotime('+6 MONTH', strtotime($effectiveDate));

            $newDate = date('m/d/Y', $dateAt);
            $pdf->setXY(179, 195);
            $pdf->Cell(0, 10, $newDate);

            $pdf->setFont('helvetica', '', 8);
            $pdf->setXY(125, 205);
            $pdf->Cell(0, 10, $name);

            $pdf->setXY(169, 208);
            $pdf->MultiCell(100, 5, "{$address1}\n{$town} {$state} {$zip}", 0, 'L');

            $pdf->setFont('helvetica', 'B', 10);
            $pdf->setXY(125, 220);
            $pdf->Cell(0, 10, $vehicleYear . " " . $vehicleModel);

            $pdf->setFont('helvetica', '', 8);
            $pdf->setXY(125, 231);
            $pdf->MultiCell(100, 5, $additional_driver, 0, 'L');

            $pdf->setFont('helvetica', '', 6);
            $pdf->setXY(125, 265);
            $pdf->MultiCell(80, 5, "The coverage provided by this policy meets the minimum requirements of section 16056 or 16500.5 of the California Vehicle Code, minimum liability limits prescribed by law.", 0, 'L');



            break;

        case 4: // Страница 4
            $pdf->setFont('helvetica', 'B', 10);
            $pdf->setXY(30, 150);
            $pdf->Cell(0, 10, "NEW ACCIDENT PROCEDURE INSTRUCTIONS");

            $pdf->setFont('helvetica', 'B', 10);
            $pdf->setXY(117, 164);
            $pdf->Cell(0, 10, $vehicleYear . " " . $vehicleModel);

            $pdf->setXY(8, 164);
            $pdf->Cell(0, 10, $vehicleYear . " " . $vehicleModel);

            $pdf->setFont('helvetica', '', 10);
            $pdf->setXY(63, 262);
            $pdf->Cell(0, 10, $number2, 'R');

            $pdf->setXY(170, 262);
            $pdf->Cell(0, 10, $number2, 'R');
            break;
    }
}

