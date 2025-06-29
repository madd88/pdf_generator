<?php

use setasign\Fpdi\Tcpdf\Fpdi;

// Данные из запроса
$businessName = $data['businessName'] ?? '';
$businessType = $data['businessType'] ?? '';
$businessAddress = $data['businessAddress'] ?? '';
$businessTown = $data['businessTown'] ?? '';
$businessState = $data['businessState'] ?? '';
$businessZip = $data['businessZip'] ?? '';
$incorporationDate = $data['incorporationDate'] ?? '';
$ein = $data['ein'] ?? '';
$ownerName = $data['ownerName'] ?? '';

// Определяем форму на основе типа бизнеса
// Загрузка исходного PDF
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Отключите автоматические границы
$pdf->SetAutoPageBreak(false);


$formNumber = ($businessType == 'Profit LLC') ? '1065' : '1120';
$formText = ($businessType == 'Profit LLC') ? '1065 (Partnership)' : '1120 (Corporation)';
// Определяем шаблон на основе типа бизнеса
$templateFile = ($data['businessType'] == 'Profit LLC')
    ? $assetsPath . '/tpl/llc_ein.pdf'
    : $assetsPath . '/tpl/inc_ein.pdf';


// Устанавливаем источник PDF
$pageCount = $pdf->setSourceFile($templateFile);

// Вычисляем name control (первые 4 буквы названия бизнеса)
$nameControl = substr(preg_replace('/[^A-Za-z]/', '', $data['businessName']), 0, 4);
if (strlen($nameControl) < 4) {
    $nameControl = str_pad($nameControl, 4, 'X');
}
$nameControl = strtoupper($nameControl);
$pdf->setFontSpacing(-0.1);

// Обрабатываем каждую страницу
for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    $templateId = $pdf->importPage($pageNo);
    $size = $pdf->getTemplateSize($templateId);

    // Добавляем страницу
    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
    $pdf->useTemplate($templateId);

    // Устанавливаем шрифт
    $pdf->SetFont('Courier', '', 10.418);
    $pdf->SetTextColor(0, 0, 0);

    // Заменяем данные в зависимости от страницы и типа документа
    if ($businessType == 'Profit LLC') {
        // Обработка для LLC
        switch ($pageNo) {
            case 1:
                $pdf->setFontStretching(93.2);
                $pdf->SetFont('Courier', '', 10.55);
                $pdf->SetXY(132.7, 30.61);
                $pdf->Write(0, "Date of this notice:  " . date('m-d-Y', strtotime($incorporationDate)));

                $pdf->SetXY(132.7, 37.5);
                $pdf->Write(0, "Employer Identification Number:");
                $pdf->SetXY(132.7, 41.1);
                $pdf->Write(0, $ein);

                $pdf->SetXY(132.7, 48.1);
                $pdf->Write(0, "Form:  SS-4");

                $pdf->SetXY(132.7, 55.3);
                $pdf->Write(0, "Number of this notice:  CP 575 G");

                $pdf->SetXY(132.7, 65.8);
                $pdf->Write(0, "For assistance you may call us at:");
                $pdf->SetXY(132.7, 69.2);
                $pdf->Write(0, '1-800-829-4933');

                $pdf->SetXY(132.7, 79.8);
                $pdf->Write(0, "IF YOU WRITE, ATTACH THE");
                $pdf->SetXY(132.7, 83.4);
                $pdf->Write(0, 'STUB AT THE END OF THIS NOTICE.');

                //address
                $pdf->SetXY(37.5, 58.7);
                $pdf->Write(0, mb_strtoupper($businessName));

                $pdf->SetXY(37.5, 62.2);
                $pdf->Write(0, mb_strtoupper($ownerName) . ' SOLE MBR');

                $pdf->SetXY(37.5, 65.8);
                $pdf->Write(0, mb_strtoupper($businessAddress));

                $pdf->SetXY(37.5, 69.3);
                $pdf->Write(0, mb_strtoupper($businessTown) . ", " . mb_strtoupper($businessState) . "  " . $businessZip);

                $pdf->SetXY(43, 104);

                $pdf->SetXY(43.1, 105.1);
                $pdf->setCellHeightRatio(0.95);
                $pdf->MultiCell(120, 1, "WE ASSIGNED YOU AN EMPLOYER IDENTIFICATION NUMBER\n", 0, 'C');
                $pdf->SetFillColor(255, 255, 255); // RGB белый
                $pdf->Rect(
                    125,
                    244,
                    9,
                    6,
                    'F' // F = заливка
                );
                $pdf->setXY(125, 245.5);
                $pdf->Cell(0, 5, mb_strtoupper(substr($businessName, 0, 4)));
                $pdf->SetXY(23.5, 112.1);
                $pdf->MultiCell(200, 1, "     Thank you for applying for an Employer Identification Number (EIN).  We assigned you\nEIN {$ein}.  This EIN will identify you, your business accounts, tax returns, and\ndocuments, even if you have no employees.  Please keep this notice in your permanent\nrecords.", 0, 'L');
                $pdf->Ln(3.5);
                $pdf->SetX(23.5);

                $pdf->MultiCell(187, 1, "     When filing tax documents, payments, and related correspondence, it is very important\nthat you use your EIN and complete name and address exactly as shown above.  Any variation\nmay cause a delay in processing, result in incorrect information in your account, or even\ncause you to be assigned more than one EIN.  If the information is not correct as shown\nabove, please make the correction using the attached tear off stub and return it to us.", 0, 'L');
                $pdf->Ln(3.5);
                $pdf->SetX(23.5);
                $pdf->MultiCell(187, 1, "     A limited liability company (LLC) may file Form 8832, Entity Classification Election,\nand elect to be classified as an association taxable as a corporation.  If the LLC is\neligible to be treated as a corporation that meets certain tests and it will be electing S\ncorporation status, it must timely file Form 2553, Election by a Small Business\nCorporation.  The LLC will be treated as a corporation as of the effective date of the S\ncorporation election and does not need to file Form 8832.", 0, 'L');
                $pdf->Ln(3.5);
                $pdf->SetX(23.5);
                $pdf->MultiCell(187, 1, "     To obtain tax forms and publications, including those referenced in this notice,\nvisit our Web site at www.irs.gov.  If you do not have access to the Internet, call\n1-800-829-3676 (TTY/TDD 1-800-829-4059) or visit your local IRS office.", 0, 'L');
                $pdf->SetFont('Courier', 'B', 10.55);
                $pdf->Ln(5);


                $pdf->setFontStretching(100);
                $pdf->SetFont('Courier', 'B', 10);
                $pdf->SetXY(23.5,190);
                $pdf->MultiCell(0, 5, "IMPORTANT REMINDERS:", 0, 'L');

                break;

            case 2:

                //address
                $pdf->setFontStretching(93.2);

                $pdf->SetXY(128.8, 242.5);
                $pdf->Write(0, mb_strtoupper($businessName));

                $pdf->SetXY(128.8, 246.2);
                $pdf->Write(0, mb_strtoupper($ownerName) . ' SOLE MBR');

                $pdf->SetXY(128.8, 249.5);
                $pdf->Write(0, mb_strtoupper($businessAddress));

                $pdf->SetXY(128.8, 253.2);
                $pdf->Write(0, mb_strtoupper($businessTown) . ", " . mb_strtoupper($businessState) . "  " . $businessZip);

                // Замена номера EIN
                $pdf->SetXY(99, 13.5);
                $pdf->Write(0, date('m-d-Y', strtotime($incorporationDate)) . '  '. mb_strtoupper(substr($businessName, 0, 4)) .'  O  9999999999  SS-4');

                // Замена даты
                $pdf->SetXY(154.5, 218);
                $pdf->Write(0, date('m-d-Y', strtotime($incorporationDate)));
                $pdf->SetXY(176.5, 221.7);
                $pdf->Write(0, $ein);

                break;
        }
    } else {
        // Обработка для INC
        switch ($pageNo) {
            case 1:
                $pdf->setFontStretching(93.2);
                $pdf->SetFont('Courier', '', 10.55);
                $pdf->SetXY(132.7, 30.61);
                $pdf->Write(0, "Date of this notice:  " . date('m-d-Y', strtotime($incorporationDate)));

                $pdf->SetXY(132.7, 37.5);
                $pdf->Write(0, "Employer Identification Number:");
                $pdf->SetXY(132.7, 41.1);
                $pdf->Write(0, $ein);

                $pdf->SetXY(132.7, 48.1);
                $pdf->Write(0, "Form:  SS-4");

                $pdf->SetXY(132.7, 55.3);
                $pdf->Write(0, "Number of this notice:  CP 575 A");

                $pdf->SetXY(132.7, 65.8);
                $pdf->Write(0, "For assistance you may call us at:");
                $pdf->SetXY(132.7, 69.2);
                $pdf->Write(0, '1-800-829-4933');

                $pdf->SetXY(132.7, 79.8);
                $pdf->Write(0, "IF YOU WRITE, ATTACH THE");
                $pdf->SetXY(132.7, 83.4);
                $pdf->Write(0, 'STUB AT THE END OF THIS NOTICE.');

                //address

                $pdf->SetXY(37.5, 58.7);
                $pdf->Write(0, mb_strtoupper($businessName));

                /*$pdf->SetXY(40, 67);
                $pdf->Write(0, $ownerName . ' SOLE MBR');*/

                $pdf->SetXY(37.5, 62.3);
                $pdf->Write(0, mb_strtoupper($businessAddress));

                $pdf->SetXY(37.5, 65.8);
                $pdf->Write(0, mb_strtoupper($businessTown) . ", " . mb_strtoupper($businessState) . "  " . $businessZip);

                $pdf->SetXY(24.1, 105.1);
                $pdf->setCellHeightRatio(0.95);
                $pdf->MultiCell(158, 1, "WE ASSIGNED YOU AN EMPLOYER IDENTIFICATION NUMBER\n\n", 0, 'C');
                $pdf->SetXY(23.5, 112.1);
                $pdf->MultiCell(200, 1, "     Thank you for applying for an Employer Identification Number (EIN).  We assigned you\nEIN {$ein}.  This EIN will identify you, your business accounts, tax returns, and\ndocuments, even if you have no employees.  Please keep this notice in your permanent\nrecords.", 0, 'L');
                $pdf->Ln(3.5);
                $pdf->SetX(23.5);
                $pdf->MultiCell(187, 1, "     When filing tax documents, payments, and related correspondence, it is very important\nthat you use your EIN and complete name and address exactly as shown above.  Any variation\nmay cause a delay in processing, result in incorrect information in your account, or even\ncause you to be assigned more than one EIN.  If the information is not correct as shown\nabove, please make the correction using the attached tear off stub and return it to us.", 0, 'L');
                $pdf->Ln(3.5);

                $pdf->SetX(23.5);

                $pdf->MultiCell(187, 1, "     Based on the information received from you or your representative, you must file\nthe following form(s) by the date(s) shown.", 0, 'L');
                $date = findNearestApril15Compact($incorporationDate);
                $pdf->SetXY(128.8, 161.7);
                $pdf->Write(0,  $date);
//                $pdf->setFontStretching(100);
                break;

            case 2:
                // Замена номера EIN
                $pdf->SetXY(99, 13.5);
                $pdf->setFontStretching(95);
                $pdf->Write(0, date('m-d-Y', strtotime($incorporationDate)) . '  '. mb_strtoupper(substr($businessName, 0, 4)) .'  O  9999999999  SS-4');
                $pdf->SetFillColor(255, 255, 255); // RGB белый
                $pdf->Rect(
                    125,
                    170,
                    9,
                    6,
                    'F' // F = заливка
                );
                $pdf->setXY(125, 171.5);
                $pdf->Cell(0, 5, mb_strtoupper(substr($businessName, 0, 4)));

                break;
                case 3:
                    $pdf->setFontStretching(93.2);

                    $pdf->SetXY(99, 14);
                    $pdf->Write(0, date('m-d-Y', strtotime($incorporationDate)) . '  '. mb_strtoupper(substr($businessName, 0, 4)) .'  O  9999999999  SS-4');
                    // Замена даты
                    $pdf->SetXY(154.5, 218);
                    $pdf->Write(0, date('m-d-Y', strtotime($incorporationDate)));
                    $pdf->SetXY(176.5, 221.5);
                    $pdf->Write(0, $ein);

                    //address
                    $pdf->SetXY(128.75, 242.55);
                    $pdf->Write(0, mb_strtoupper($businessName));

                    /*$pdf->SetXY(40, 67);
                    $pdf->Write(0, $ownerName . ' SOLE MBR');*/

                    $pdf->SetXY(128.75, 246.15);
                    $pdf->Write(0, mb_strtoupper($businessAddress));

                    $pdf->SetXY(128.75, 249.6);
                    $pdf->Write(0, mb_strtoupper($businessTown) . ", " . mb_strtoupper($businessState) . "  " . $businessZip);

                    break;
        }
    }
}

function findNearestApril15Compact(string $dateString): string {
    $input = new DateTime($dateString);
    $year = (int)$input->format('Y');
    $april15 = new DateTime("$year-04-15");

    return ($input <= $april15)
        ? $april15->format('m/d/Y')
        : (new DateTime(($year + 1) . '-04-15'))->format('m/d/Y');
}

