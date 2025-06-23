<?php

use setasign\Fpdi\Tcpdf\Fpdi;

// Загрузка исходного PDF
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Отключите автоматические границы
$pdf->SetAutoPageBreak(false);

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
$number2 = 'U4' . mb_strtoupper($state) . ' (06-20)';
$penalty = [];
if (($handle = fopen($assetsPath . "/tpl/insurance_fines.csv", "r")) !== FALSE) {
    while (($datas = fgetcsv($handle, 1000, ";")) !== FALSE) {
        if (mb_strtolower($datas[0]) == mb_strtolower($state)) {
            $penalty = $datas;
        }
    }

    fclose($handle);
}


$pageCount = $pdf->setSourceFile($assetsPath . '/tpl/geico_original.pdf');

// Обработка каждой страницы
for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    // Импорт страницы
    $templateId = $pdf->importPage($pageNo, '/CropBox');
    $size = $pdf->getTemplateSize($templateId);

    // Создание новой страницы
    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
    $pdf->useTemplate($templateId);

    // Замена текста по координатам
    $pdf->setFont('helvetica', '', 10);
    $pdf->setTextColor(0, 0, 0);
    $pn = "4" . rand(0,9) . rand(0,9) . rand(0,9) . "-" . rand(0,9) . rand(0,9) . "-" . rand(0,9) . rand(0,9) . "-" . rand(0,9) . rand(0,9);

    switch ($pageNo) {
        case 1: // Страница 1

            //Policy Number
            $pdf->setXY(145, 11.2);
            $pdf->Cell(0, 10, 'Policy Number: ' . $pn, 0 );
            // Замена адреса
            $pdf->setXY(115.6, 97.2);
            $pdf->setCellHeightRatio(1.15);
            $pdf->MultiCell(100, 2, mb_strtoupper("{$name}\n{$address1}\n{$town} {$state}  {$zip}"), 0, 'L');
            $pdf->setCellHeightRatio(1);
            // Замена имени водителя

            $pdf->setXY(11.5, 119.8);
            $pdf->Cell(0, 10, mb_strtoupper($name));

            $pdf->setXY(64, 123);
            $pdf->MultiCell(50, 10, $additional_driver, 0 , 'R');

            //Номер внизу страницы
            $pdf->setXY(11.7, 260.5);
            $pdf->Cell(0, 10, $number);
            break;

        case 3: // Страница 3
            $pdf->setFont('helvetica', '', 10);
            $pdf->setXY(15.6, 61.7);
            $pdf->setCellHeightRatio(1.16);
            $pdf->MultiCell(160.5, 10, "Here are your Evidence of Liability Insurance Cards. Two cards have been provided for each vehicle insured. One card must be carried in the proper insured vehicle. Proof of insurance is required to register or renew the registration of your vehicle. A law enforcement officer can ask you to prove\nthat you have liability insurance meeting the basic requirements of {$penalty[1]} law. A violation of these requirements can result in a fine of up to: {$penalty[2]} for the first time; {$penalty[3]} for additional times. Also, a judge can have your vehicle impounded. False proof of insurance may result in a fine up to {$penalty[4]} and {$penalty[5]} days in prison.", 0, 'L');
            // Первая карточка
            $pdf->setXY(101.3, 97.4);
            $pdf->setCellHeightRatio(1.15);
            $pdf->MultiCell(100, 5, mb_strtoupper("{$name}\n{$address1}\n{$town} {$state}  {$zip}"), 0, 'L');
            $pdf->setCellHeightRatio(1.25);

            $pdf->setFont('helvetica', 'B', 10);
            $pdf->setXY(16.5, 163);
            $pdf->Cell(0, 10, "{$penalty[1]} Evidence of Liability Insurance");

            $pdf->setFont('helvetica', 'B', 8);
            $pdf->setXY(36, 180.3);
            $pdf->Cell(0, 10, $vehicleYear . " " . mb_strtoupper($vehicleModel));

            $pdf->setXY(36, 185);
            $pdf->Cell(0, 10, "Vehicle ID No.");

            $pdf->setFont('helvetica', '', 8);
            $pdf->setXY(55, 185);
            $pdf->Cell(0, 10, $vin);

            $pdf->setFont('helvetica', '', 10);
            $pdf->setXY(16, 195.3);
            $pdf->Cell(0, 10, $pn);

            $pdf->setXY(44.5, 195.2);
            $pdf->Cell(0, 10, date("m/d/y", strtotime($effectiveDate)));

            $dateAt = strtotime('+6 MONTH', strtotime($effectiveDate));

            $newDate = date('m/d/y', $dateAt);
            $pdf->setXY(70, 195);
            $pdf->Cell(0, 10, $newDate);

            $pdf->setFont('helvetica', '', 8);
            $pdf->setXY(16, 205);
            $pdf->Cell(0, 10, $name);

            $pdf->setXY(60, 208);
            $pdf->MultiCell(100, 5, "{$address1}\n{$town} {$state} {$zip}", 0, 'L');

            $pdf->setFont('helvetica', 'B', 10);
            $pdf->setXY(16, 219.4);
            $pdf->Cell(0, 10, $vehicleYear . " " . mb_strtoupper($vehicleModel));

            $pdf->setFont('helvetica', '', 8);
            $pdf->setXY(16, 230.7);
            $pdf->MultiCell(100, 5, $additional_driver, 0, 'L');

            $pdf->setFont('helvetica', '', 6);
            $pdf->setXY(16, 265);
            $pdf->MultiCell(80, 5, "The coverage provided by this policy meets the minimum requirements of section {$penalty[6]} or {$penalty[7]} of the {$penalty[1]} Vehicle Code, minimum liability limits prescribed by law.", 0, 'L');


            // Вторая карточка
            $pdf->setFont('helvetica', 'B', 10);
            $pdf->setXY(125, 163);
            $pdf->Cell(0, 10, "{$penalty[1]} Evidence of Liability Insurance");

            $pdf->setFont('helvetica', 'B', 8);
            $pdf->setXY(144.7, 180.3);
            $pdf->Cell(0, 10, $vehicleYear . " " . mb_strtoupper($vehicleModel));

            $pdf->setXY(144.6, 185);
            $pdf->Cell(0, 10, "Vehicle ID No.");

            $pdf->setFont('helvetica', '', 8);
            $pdf->setXY(164, 185);
            $pdf->Cell(0, 10, $vin);

            $pdf->setFont('helvetica', '', 10);
            $pdf->setXY(124.5, 195.3);
            $pdf->Cell(0, 10, $pn);

            $pdf->setXY(153, 195.2);
            $pdf->Cell(0, 10, date("m/d/y", strtotime($effectiveDate)));

            $dateAt = strtotime('+6 MONTH', strtotime($effectiveDate));

            $newDate = date('m/d/y', $dateAt);
            $pdf->setXY(178, 195);
            $pdf->Cell(0, 10, $newDate);

            $pdf->setFont('helvetica', '', 8);
            $pdf->setXY(124.5, 205);
            $pdf->Cell(0, 10, $name);

            $pdf->setXY(168.7, 208);
            $pdf->MultiCell(100, 5, "{$address1}\n{$town} {$state} {$zip}", 0, 'L');

            $pdf->setFont('helvetica', 'B', 10);
            $pdf->setXY(124.5, 219.3);
            $pdf->Cell(0, 10, $vehicleYear . " " . mb_strtoupper($vehicleModel));

            $pdf->setFont('helvetica', '', 8);
            $pdf->setXY(124.5, 230.7);
            $pdf->MultiCell(100, 5, $additional_driver, 0, 'L');

            $pdf->setFont('helvetica', '', 6);
            $pdf->setXY(125, 265);
            $pdf->MultiCell(80, 5, "The coverage provided by this policy meets the minimum requirements of section {$penalty[5]} or {$penalty[6]} of the {$penalty[1]} Vehicle Code, minimum liability limits prescribed by law.", 0, 'L');



            break;

        case 4: // Страница 4
/*            $pdf->setFont('helvetica', 'B', 10);
            $pdf->setXY(30, 150);
            $pdf->Cell(0, 10, "NEW ACCIDENT PROCEDURE INSTRUCTIONS");*/

            $pdf->setFont('helvetica', 'B', 8);
            $pdf->setXY(116.5, 164.2);
            $pdf->Cell(0, 10, $vehicleYear . " " . mb_strtoupper($vehicleModel));

            $pdf->setXY(7.9, 164.2);
            $pdf->Cell(0, 10, $vehicleYear . " " . mb_strtoupper($vehicleModel));

            $pdf->setFont('helvetica', '', 10);
            $pdf->setXY(62, 261.3);
            $pdf->Cell(0, 10, $number2, 'R');

            $pdf->setXY(170.6, 261.4);
            $pdf->Cell(0, 10, $number2, 'R');
            break;
    }
}

