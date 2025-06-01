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
$formNumber = ($businessType == 'Profit LLC') ? '1065' : '1120';
$formText = ($businessType == 'Profit LLC') ? '1065 (Partnership)' : '1120 (Corporation)';
// Определяем шаблон на основе типа бизнеса
$templateFile = ($data['businessType'] == 'Profit LLC')
    ? $assetsPath . '/tpl/llc_ein.pdf'
    : $assetsPath . '/tpl/inc_ein.pdf';

// Создаем FPDI объект
$pdf = new Fpdi();

// Устанавливаем источник PDF
$pageCount = $pdf->setSourceFile($templateFile);

// Вычисляем name control (первые 4 буквы названия бизнеса)
$nameControl = substr(preg_replace('/[^A-Za-z]/', '', $data['business_name']), 0, 4);
if (strlen($nameControl) < 4) {
    $nameControl = str_pad($nameControl, 4, 'X');
}
$nameControl = strtoupper($nameControl);

// Обрабатываем каждую страницу
for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    $templateId = $pdf->importPage($pageNo);
    $size = $pdf->getTemplateSize($templateId);

    // Добавляем страницу
    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
    $pdf->useTemplate($templateId);

    // Устанавливаем шрифт
    $pdf->SetFont('Courier', '', 10);
    $pdf->SetTextColor(0, 0, 0);

    // Заменяем данные в зависимости от страницы и типа документа
    if ($businessType == 'Profit LLC') {
        // Обработка для LLC
        switch ($pageNo) {
            case 1:

                $pdf->SetXY(130, 30);
                $pdf->Write(0, "Date of this notice: " . date('m-d-y', strtotime($incorporationDate)));

                $pdf->SetXY(130, 38);
                $pdf->Write(0, "Employer Identification Number:");
                $pdf->SetXY(130, 42);
                $pdf->Write(0, $ein);

                $pdf->SetXY(130, 51);
                $pdf->Write(0, "Form: SS-4");

                $pdf->SetXY(130, 59);
                $pdf->Write(0, "Number of this notice: CP 575 G");

                $pdf->SetXY(130, 68);
                $pdf->Write(0, "For assistance you may call us at:");
                $pdf->SetXY(130, 72);
                $pdf->Write(0, '1-800-829-4933');

                $pdf->SetXY(130, 80);
                $pdf->Write(0, "IF YOU WRITE, ATTACH THE");
                $pdf->SetXY(130, 84);
                $pdf->Write(0, 'STUB AT THE END OF THIS NOTICE.');

                //address
                $pdf->SetXY(40, 63);
                $pdf->Write(0, $businessName);

                $pdf->SetXY(40, 67);
                $pdf->Write(0, $ownerName . ' SOLE MBR');

                $pdf->SetXY(40, 71);
                $pdf->Write(0, $businessAddress);

                $pdf->SetXY(40, 75);
                $pdf->Write(0, $businessTown . ", " . $businessState . " " . $businessZip);

                $pdf->SetXY(30, 100);
                $pdf->setCellHeightRatio(1);
                $pdf->MultiCell(0, 1, "WE ASSIGNED YOU AN EMPLOYER IDENTIFICATION NUMBER\n", 0, 'C');

$pdf->MultiCell(0, 1, "     Thank you for applying for an Employer Identification Number (EIN). We assigned you EIN {$ein}. This EIN will identify you, your business accounts, tax returns, and documents, even if you have no employees. Please keep this notice in your permanent records.\n
     When filing tax documents, payments, and related correspondence, it is very important that you use your EIN and complete name and address exactly as shown above. Any variation may cause a delay in processing, result in incorrect information in your account, or even cause you to be assigned more than one EIN. If the information is not correct as shown above, please make the correction using the attached tear off stub and return it to us.\n
     A limited liability company (LLC) may file Form 8832, Entity Classification Election, and elect to be classified as an association taxable as a corporation. If the LLC is eligible to be treated as a corporation that meets certain tests and it will be electing S corporation status, it must timely file Form 2553, Election by a Small Business Corporation. The LLC will be treated as a corporation as of the effective date of the S corporation election and does not need to file Form 8832.\n
     To obtain tax forms and publications, including those referenced in this notice, visit our Web site at www.irs.gov. If you do not have access to the Internet, call 1-800-829-3676 (TTY/TDD 1-800-829-4059) or visit your local IRS office.", 0, 'L');
                $pdf->SetFont('Courier', 'B', 10);
                $pdf->Ln(5);
                $pdf->MultiCell(0, 5, "IMPORTANT REMINDERS:", 0, 'L');

                break;

            case 2:
                // Замена номера EIN
                $pdf->SetXY(110, 12);
                $pdf->Write(0, date('m-d-y', strtotime($incorporationDate)) . ' '. mb_strtoupper(substr($businessName, 0, 4)) .' O 9999999999 SS-4');

                // Замена даты
                $pdf->SetXY(155, 218);
                $pdf->Write(0, date('m-d-y', strtotime($incorporationDate)));
                $pdf->SetXY(175, 222);
                $pdf->Write(0, $ein);

                break;
        }
    } else {
        // Обработка для INC
        switch ($pageNo) {
            case 1:
                $pdf->SetXY(130, 30);
                $pdf->Write(0, "Date of this notice: " . date('m-d-y', strtotime($incorporationDate)));

                $pdf->SetXY(130, 38);
                $pdf->Write(0, "Employer Identification Number:");
                $pdf->SetXY(130, 42);
                $pdf->Write(0, $ein);

                $pdf->SetXY(130, 51);
                $pdf->Write(0, "Form: SS-4");

                $pdf->SetXY(130, 59);
                $pdf->Write(0, "Number of this notice: CP 575 A");

                $pdf->SetXY(130, 68);
                $pdf->Write(0, "For assistance you may call us at:");
                $pdf->SetXY(130, 72);
                $pdf->Write(0, '1-800-829-4933');

                $pdf->SetXY(130, 80);
                $pdf->Write(0, "IF YOU WRITE, ATTACH THE");
                $pdf->SetXY(130, 84);
                $pdf->Write(0, 'STUB AT THE END OF THIS NOTICE.');

                //address
                $pdf->SetXY(40, 63);
                $pdf->Write(0, $businessName);

                /*$pdf->SetXY(40, 67);
                $pdf->Write(0, $ownerName . ' SOLE MBR');*/

                $pdf->SetXY(40, 67);
                $pdf->Write(0, $businessAddress);

                $pdf->SetXY(40, 71);
                $pdf->Write(0, $businessTown . ", " . $businessState . " " . $businessZip);

                $pdf->SetXY(20, 105);
                $pdf->setCellHeightRatio(1);
                $pdf->MultiCell(0, 1, "WE ASSIGNED YOU AN EMPLOYER IDENTIFICATION NUMBER\n\n", 0, 'C');
                $pdf->SetX(20);

                $pdf->MultiCell(0, 1, "     Thank you for applying for an Employer Identification Number (EIN). We assigned you EIN {$ein}. This EIN will identify you, your business accounts, tax returns, and documents, even if you have no employees. Please keep this notice in your permanent records.\n
     When filing tax documents, payments, and related correspondence, it is very important that you use your EIN and complete name and address exactly as shown above. Any variation may cause a delay in processing, result in incorrect information in your account, or even cause you to be assigned more than one EIN. If the information is not correct as shown above, please make the correction using the attached tear off stub and return it to us.\n
     Based on the information received from you or your representative, you must file the following form(s) by the date(s) shown", 0, 'L');
                $pdf->SetX(120);
                $pdf->Write(0,  "04/15/2019");
                break;

            case 2:
                // Замена номера EIN
                $pdf->SetXY(110, 12);
                $pdf->Write(0, date('m-d-y', strtotime($incorporationDate)) . ' '. mb_strtoupper(substr($businessName, 0, 4)) .' O 9999999999 SS-4');


                break;
                case 3:
                    $pdf->SetXY(110, 12);
                    $pdf->Write(0, date('m-d-y', strtotime($incorporationDate)) . ' '. mb_strtoupper(substr($businessName, 0, 4)) .' O 9999999999 SS-4');
                    // Замена даты
                    $pdf->SetXY(155, 218);
                    $pdf->Write(0, date('m-d-y', strtotime($incorporationDate)));
                    $pdf->SetXY(175, 222);
                    $pdf->Write(0, $ein);
                    break;
        }
    }
}

