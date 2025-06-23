<?php

use setasign\Fpdi\Tcpdf\Fpdi;


$templateFile = $assetsPath . '/tpl/hippo.pdf';

$data = [
    'homeownerName' => $data['homeownerName'] ?? '',
    'homeownerStreet' => $data['homeownerStreet'] ?? '',
    'homeownerTown' => $data['homeownerTown'] ?? '',
    'homeownerState' => $data['homeownerState'] ?? '',
    'homeownerZIP' => $data['homeownerZIP'] ?? '',
    'builtYear' => $data['builtYear'] ?? '',
    'squareFootage' => $data['squareFootage'] ?? '',
    'creationDate' => $data['creationDate'] ?? '',
    'constructionType' => $data['constructionType'] ?? '',
    'property_address' => $data['propertyAddress'] ?? $data['homeownerStreet'],
    'coverage_a' => '$100,000',
    'coverage_b' => '$29,000',
    'coverage_c' => '$100,000',
    'coverage_d' => '$87,000',
    'coverage_e' => '$100,000 (each occurrence)',
    'coverage_f' => '$2,000 (Each person)',
    'special_limits' => [
        ['item' => 'Jewerly, Watches and Furs', 'limit' => '$2,000'],
        ['item' => 'Money', 'limit' => '$2000'],
        ['item' => 'Securities', 'limit' => '$1,500'],
        ['item' => 'Silverware, Goldware and Pewterware', 'limit' => '$2,000'],
        ['item' => 'Firearms', 'limit' => '$2,500'],
        ['item' => 'Electronic Apparatus in motor vehicle', 'limit' => '$2,000'],
        ['item' => 'Computers', 'limit' => '$8,000'],
        ['item' => 'Oriental Rugs', 'limit' => '$2,000 ea / $10,000 aggregate'],
        ['item' => 'Watercraft', 'limit' => '$2,000']
    ],
    'add_coverage_a' => '$100,000 / $500 Deductible',
    'add_coverage_b' => '$10,000 / $500 Deductible',
    'add_coverage_c' => '10% of Coverage A',
    'add_coverage_d' => '$5,000',
    'add_optional_coverages' => [
        ['premium' => 'Included', 'limit' => '25% Of Coverage A'],
        ['premium' => '', 'limit' => ''],
        ['premium' => '$15', 'limit' => '$18,000'],
        ['premium' => '$16', 'limit' => 'Included'],
        ['premium' => 'Included', 'limit' => 'Included'],
        ['premium' => '', 'limit' => ''],
        ['premium' => 'Included', 'limit' => '$10,000'],
        ['premium' => '$13', 'limit' => 'included'],
        ['premium' => '$58', 'limit' => '$203,000'],
    ],
    'optional_coverages' => [
        'Additional Replacement Cost Protection',
        'Mortgage Payment Protection',
        'Personal injury coverage',
        'Water Back-Up and Sump Discharge'
    ],
    'discounts' => [
        'Loss Free',
        'Smart Home - Self Monitored',
        'Fire Extinguisher',
        'Burglar Alarm',
        'Paperless Billing'
    ],
    'deductible' => ['$500', '$500', '$1000'],
    'policy_cost' => '$1,200/year',
    'fees' =>['30', '30']
];

// Проверка существования файла
if (!file_exists($templateFile)) {
    throw new Exception("Template file not found: $templateFile");
}

$pdf = new Fpdi();
$pageCount = $pdf->setSourceFile($templateFile);

// Обработка каждой страницы
for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    $templateId = $pdf->importPage($pageNo);
    $size = $pdf->getTemplateSize($templateId);

    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
    $pdf->useTemplate($templateId);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);

    // Замены на первой странице
    if ($pageNo == 1) {
        // Замена имени застрахованного
        $pdf->setFillColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 8);

        $pdf->Rect(
            149,
            80,
            50,
            10,
            'F' // F = заливка
        );

        $pdf->SetXY(148.4, 62.7);
        $pdf->Write(50, 'NEW POLICY');

        $pdf->SetXY(30, 90);
        $pdf->MultiCell(50, 5, $data['homeownerName'], 1, 'C');

        $pdf->SetXY(30, 94);
        $pdf->MultiCell(50, 5, $data['homeownerStreet'], 1, 'C');

        $pdf->SetXY(30, 98);
        $pdf->MultiCell(50, 5, $data['homeownerTown'] . "," . $data['homeownerState'] . "," . $data['homeownerZIP'], 1, 'C');


        //Property
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetXY(118, 100);
        $pdf->MultiCell(70, 5, "Address: {$data['property_address']}
        Paid By: Escrow
        Policy Type: HO3
        Year built: {$data['builtYear']}
        Square footage: {$data['squareFootage']}
        Construction: {$data['constructionType']}
        ", 1, 'C');

        //Policy Information

        $dateString = $data['creationDate'];
        list($year, $month, $day) = explode('-', $dateString);
        $timestamp = mktime(0, 0, 0, (int)$month, (int)$day, (int)$year);
        $nextDay = $timestamp + 86400;
        $dayOfWeek = date('w', $nextDay);
        if ($dayOfWeek == 0) {
            $nextDay += 86400; // добавляем 86400 секунд (1 день)
        }
        $finalDate = $nextDay + 9 * 3600; // 9 часов в секундах
        $result = date('m/d/Y g:ia', $finalDate);

        $dateAt = date('m/d/Y g:ia', strtotime('+1 YEAR', $finalDate));

        $p = "TGD-3" . rand(0,9). rand(0,9). rand(0,9). rand(0,9). rand(0,9). rand(0,9) . "-". rand(0,9). rand(0,9);
        $pdf->SetXY(15, 120);
        $pdf->MultiCell(80, 5, "Policy: {$p}
        Policy Created Date: " . date("m/d/Y", strtotime($data['creationDate'])) . "
        Policy Effective Date: {$result}
        Policy Expiration Date: {$dateAt}
        ", 1, 'C');
        $pdf->setFont('helvetica', 'B', 6);
        $pdf->SetXY(35, 134.5);
        $pdf->Write(0, '*Standard timemezone at property location');
        $pdf->setFont('helvetica', 'B', 8);
       /* // Замена адреса имущества
        $pdf->SetXY(40, 85);
        $pdf->Write(0, $data['property_address']);*/

        // Замена лимитов покрытий
        $pdf->SetFillColor(255, 255, 255); // RGB белый
        $pdf->Rect(
            10,
            151,
            200,
            5,
            'F' // F = заливка
        );
        $pdf->SetFont('helvetica', 'B', 9);

        $pdf->SetXY(33.5, 152); // Coverage A text
        $pdf->Write(0, 'Insurance is provided for the following coverages only when a limit is shown.');
        $pdf->SetFont('helvetica', 'B', 8);


        $pdf->SetFillColor(255, 255, 255); // RGB белый
        $pdf->Rect(
            36,
            160,
            130,
            10,
            'F' // F = заливка
        );

        $pdf->Rect(
            36,
            160,
            56,
            27,
            'F' // F = заливка
        );

        $pdf->Rect(
            36,
            192,
            56,
            9.5,
            'F' // F = заливка
        );
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetXY(35.5, 160); // Coverage A text
        $text = 'POLICY COVERAGE:';
        $pdf->Write(0, $text);
        $x = $pdf->GetX()+1;
        $y = $pdf->GetY();
        $text_width = $pdf->GetStringWidth($text);

// Рисуем линию с отступом
        $line_y = $y + 4.9; // +2 мм от текста
        $pdf->SetDrawColor(0, 0, 0); // Синий цвет линии (RGB)
        $pdf->SetLineWidth(0.4);
        $pdf->Line($x - $text_width, $line_y, $x, $line_y);

        //дlimit of liabiliuty
        $pdf->SetXY(125, 160); // Coverage A text
        $text = 'LIMIT OF LIABILITY';
        $pdf->Write(0, $text);
        $x = $pdf->GetX()+1;
        $y = $pdf->GetY();
        $text_width = $pdf->GetStringWidth($text);
// Рисуем линию с отступом
        $line_y = $y + 4.9;
        $pdf->SetDrawColor(0, 0, 0); // Синий цвет линии (RGB)
        $pdf->SetLineWidth(0.4);
        $pdf->Line($x - $text_width, $line_y, $x, $line_y);


        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY(35.5, 166); // Coverage A
        $pdf->setFontSpacing(-0.2);


        $pdf->Write(0, '(Secton I - Property):');

        $pdf->SetXY(35.5, 170);
        $pdf->Write(0, 'Coverage A – Dwelling (your home)');

        $pdf->SetXY(35.5, 174);
        $pdf->Write(0, 'Coverage B – Other structures');

        $pdf->SetXY(35.5, 178);
        $pdf->Write(0, 'Coverage C – Personal property');

        $pdf->SetXY(35.5, 182);
        $pdf->Write(0, 'Coverage D – Loss of use');
        $pdf->SetXY(35.5, 192);
        $pdf->Write(0, 'Coverage E – Personal liability');

        $pdf->SetXY(35.5, 196);
        $pdf->Write(0, 'Coverage F – Medical payment to others');
        $pdf->setFontSpacing(0);


        $pdf->SetXY(125, 170); // Coverage A
        $pdf->Write(0, $data['coverage_a']);
        $pdf->SetXY(125, 174); // Coverage B
        $pdf->Write(0, $data['coverage_b']);
        $pdf->SetXY(125, 178); // Coverage C
        $pdf->Write(0, $data['coverage_c']);
        $pdf->SetXY(125, 182); // Coverage D
        $pdf->Write(0, $data['coverage_d']);



        $pdf->SetXY(125, 192); // Coverage E
        $pdf->Write(0, $data['coverage_e']);
        $pdf->SetXY(125, 196); // Coverage F
        $pdf->Write(0, $data['coverage_f']);

        // Замена лимитов для специальных категорий
        $pdf->SetFillColor(255, 255, 255); // RGB белый
        $pdf->Rect(
            36,
            210,
            130,
            5,
            'F' // F = заливка
        );

        $pdf->Rect(
            36,
            210,
            80,
            42.5,
            'F' // F = заливка
        );
        $pdf->SetFont('helvetica', 'B', 11);

        $pdf->SetXY(35.4, 209.5); // Coverage A text
        $text = 'PROPERTY SUBJECT TO SPECIAL LIMIT:';
        $pdf->Write(0, $text);
        $x = $pdf->GetX()+1;
        $y = $pdf->GetY();
        $text_width = $pdf->GetStringWidth($text);
// Рисуем линию с отступом
        $line_y = $y + 4.9;
        $pdf->SetDrawColor(0, 0, 0); // Синий цвет линии (RGB)
        $pdf->SetLineWidth(0.4);
        $pdf->Line($x - $text_width, $line_y, $x, $line_y);

        $pdf->SetXY(125, 209.5); // Coverage A text
        $text = 'LIMIT OF LIABILITY';
        $pdf->Write(0, $text);
        $x = $pdf->GetX()+1;
        $y = $pdf->GetY();
        $text_width = $pdf->GetStringWidth($text);
// Рисуем линию с отступом
        $line_y = $y + 4.9;
        $pdf->SetDrawColor(0, 0, 0); // Синий цвет линии (RGB)
        $pdf->SetLineWidth(0.4);
        $pdf->Line($x - $text_width, $line_y, $x, $line_y);


        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY(35.5, 166); // Coverage A
        $pdf->setFontSpacing(-0.2);
        $pdf->setFontSpacing(0);

        $pdf->SetXY(35.5, 217);
        $pdf->Write(0, 'Jewelry, Watches and Furs');

        $pdf->SetXY(35.5, 221);
        $pdf->Write(0, 'Money');
        $pdf->SetXY(35.5, 225);
        $pdf->Write(0, 'Securities');
        $pdf->SetXY(35.5, 229);
        $pdf->Write(0, 'Silverware, Goldware and Pewterware');
        $pdf->SetXY(35.5, 233);
        $pdf->Write(0, 'Firearms');
        $pdf->SetXY(35.5, 237);
        $pdf->Write(0, 'Electronic Apparatus in or upon a motor vehicle');
        $pdf->SetXY(35.5, 241);
        $pdf->Write(0, 'Computers');
        $pdf->SetXY(35.5, 245);
        $pdf->Write(0, 'Oriental Rugs');
        $pdf->SetXY(35.5, 249);
        $pdf->Write(0, 'Watercraft');

        $yPosition = 217;


        foreach ($data['special_limits'] as $item) {
            $pdf->SetXY(125, $yPosition);
            $pdf->Write(0, $item['limit']);
            $yPosition += 4;
        }
    }

    // Замены на второй странице
    if ($pageNo == 2) {

        $pdf->SetFillColor(255, 255, 255); // RGB белый
        $pdf->Rect(
            24,
            34,
            130,
            5,
            'F' // F = заливка
        );

        $pdf->Rect(
            24,
            34,
            80,
            28,
            'F' // F = заливка
        );
        $pdf->SetFont('helvetica', 'B', 11);

        $pdf->SetXY(23, 34); // Coverage A text
        $text = 'ADDITIONAL COVERAGES INCLUDED:';
        $pdf->Write(0, $text);
        $x = $pdf->GetX()+1;
        $y = $pdf->GetY();
        $text_width = $pdf->GetStringWidth($text);
// Рисуем линию с отступом
        $line_y = $y + 4.9;
        $pdf->SetDrawColor(0, 0, 0); // Синий цвет линии (RGB)
        $pdf->SetLineWidth(0.4);
        $pdf->Line($x - $text_width, $line_y, $x, $line_y);

        $pdf->SetXY(110, 34); // Coverage A text
        $text = 'LIMIT OF LIABILITY';
        $pdf->Write(0, $text);
        $x = $pdf->GetX()+1;
        $y = $pdf->GetY();
        $text_width = $pdf->GetStringWidth($text);
// Рисуем линию с отступом
        $line_y = $y + 4.9;
        $pdf->SetDrawColor(0, 0, 0); // Синий цвет линии (RGB)
        $pdf->SetLineWidth(0.4);
        $pdf->Line($x - $text_width, $line_y, $x, $line_y);



        $pdf->SetFont('helvetica', 'B', 9);
        // Замена лимитов покрытий
        $pdf->SetLineWidth(0.4);

        $pdf->SetXY(23, 40);
        $pdf->MultiCell(0, 4, 'Equipment Breakdown Enhancement Endorsement
(HO475)');
        $pdf->SetXY(23, 48);
        $pdf->Write(0, 'Service Line Enhancement Endorsement (HO210) ');
        $pdf->SetXY(23, 52);
        $pdf->Write(0, 'Ordinance Or Law');
        $pdf->SetXY(23, 56);
        $pdf->Write(0, 'Limited Fungi, Other Microbes or Rot');
        $pdf->SetLineWidth(0);

        $pdf->SetXY(110, 40); // Coverage A
        $pdf->Write(0, $data['coverage_a']);
        $pdf->SetXY(110, 48); // Coverage B
        $pdf->Write(0, $data['coverage_b']);
        $pdf->SetXY(110, 52); // Coverage C
        $pdf->Write(0, $data['coverage_c']);
        $pdf->SetXY(110, 56); // Coverage D
        $pdf->Write(0, $data['coverage_d']);



        $pdf->SetFillColor(255, 255, 255); // RGB белый
        $pdf->Rect(
            24,
            67,
            160,
            7,
            'F' // F = заливка
        );

        $pdf->Rect(
            24,
            67,
            80,
            44,
            'F' // F = заливка
        );

        $pdf->SetFont('helvetica', 'B', 11);

        $pdf->SetXY(23, 67); // Coverage A text
        $text = 'OPTIONAL COVERAGES PURCHASED:';
        $pdf->Write(0, $text);
        $x = $pdf->GetX()+1;
        $y = $pdf->GetY();
        $text_width = $pdf->GetStringWidth($text);
// Рисуем линию с отступом
        $line_y = $y + 4.9;
        $pdf->SetDrawColor(0, 0, 0); // Синий цвет линии (RGB)
        $pdf->SetLineWidth(0.4);
        $pdf->Line($x - $text_width, $line_y, $x, $line_y);

        $pdf->SetXY(122, 67); // Coverage A text
        $text = 'LIMIT OF LIABILITY';
        $pdf->Write(0, $text);
        $x = $pdf->GetX()+1;
        $y = $pdf->GetY();
        $text_width = $pdf->GetStringWidth($text);
// Рисуем линию с отступом
        $line_y = $y + 4.9;
        $pdf->SetDrawColor(0, 0, 0); // Синий цвет линии (RGB)
        $pdf->SetLineWidth(0.4);
        $pdf->Line($x - $text_width, $line_y, $x, $line_y);

        $pdf->SetXY(164, 67); // Coverage A text
        $text = 'PREMIUM';
        $pdf->Write(0, $text);
        $x = $pdf->GetX()+1;
        $y = $pdf->GetY();
        $text_width = $pdf->GetStringWidth($text);
// Рисуем линию с отступом
        $line_y = $y + 4.9;
        $pdf->SetDrawColor(0, 0, 0); // Синий цвет линии (RGB)
        $pdf->SetLineWidth(0.4);
        $pdf->Line($x - $text_width, $line_y, $x, $line_y);


        $pdf->SetFont('helvetica', 'B', 9);
        // Замена лимитов покрытий
        $pdf->SetLineWidth(0.4);
        $pdf->SetXY(23, 75);
        $pdf->Write(0, 'Additional Replacement Cost Protection Specified');

        $pdf->SetXY(23, 79);
        $pdf->Write(0, 'Amount (HO420)');

        $pdf->SetXY(23, 83);
        $pdf->Write(0, 'Mortgage Payment Protection (HO920)');

        $pdf->SetXY(23, 87);
        $pdf->Write(0, 'Personal injury (HO082)');

        $pdf->SetXY(23, 91);
        $pdf->Write(0, 'Personal Property Replacement Cost Loss');

        $pdf->SetXY(23, 95);
        $pdf->Write(0, 'Settlement (HO290)');

        $pdf->SetXY(23, 99);
        $pdf->Write(0, 'Water Back-Up and Sump Discharge or Overflow (HO208)');

        $pdf->SetXY(23, 103);
        $pdf->Write(0, 'Special Limits Of Liability Increased Limits');

        $pdf->SetXY(23, 107);
        $pdf->Write(0, 'Personal Property Increased Limit');


        $yPosition = 75;
        foreach ($data['add_optional_coverages'] as $item) {
            $pdf->SetXY(122, $yPosition);
            $pdf->Write(0, $item['limit']);
            $pdf->SetXY(164, $yPosition);
            $pdf->Write(0, $item['premium']);
            $yPosition += 4;
        }


        $pdf->SetFillColor(255, 255, 255); // RGB белый
        $pdf->Rect(
            22,
            121,
            160,
            15,
            'F' // F = заливка
        );
        $pdf->SetXY(22, 121);
        $pdf->MultiCell(0, 0, 'Loss Free, Fire Alarm Smart Home – Self Monitored, Fire Extinguisher, Burglar Alarm Smart Home – Self
Monitored, Smoke Alarm, Deadbolt, Paperless, Affinity, Water Leak Detection Device Smart Home Verified,
Water Leak Detection, Companion Policy, Smart Home.', 0, 'L');


        //Deductible
        $pdf->SetXY(22, 145);
        $pdf->Write(0, "Equipment Breakdown Deductible: {$data['deductible'][0]}");

        $pdf->SetXY(22, 149);
        $pdf->Write(0, "Service Line Deductible: {$data['deductible'][1]}");

        $pdf->SetXY(22, 153);
        $pdf->Write(0, "All-Perlis Deductible: {$data['deductible'][2]}");

        //COST OF POLICY:
        $pdf->SetFont('helvetica', '', 10);

        $pdf->SetXY(10, 165);
        $pdf->MultiCell(80, 5, "
                Policy Premium*
             +Optional Coverage
         = Total Policy Premium\n        
                   + Policy Fee
               + Inspection Fee
        ", 1, 'R');
        $r1 = rand(800, 950);
        $r2 = rand(95, 120);
        $pdf->SetXY(90, 169);
        $pdf->Write(0, "\${$r1}");
        $pdf->SetXY(90, 174);
        $pdf->Write(0, "\${$r2}");
        $pdf->SetXY(90, 179);
        $pdf->Write(0, "$" . ($r1+$r2));
        $pdf->SetXY(90, 187);
        $pdf->Write(0, "\${$data['fees'][0]}");
        $pdf->SetXY(90, 191);
        $pdf->Write(0, "\${$data['fees'][1]}");

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetXY(10, 192);
        $pdf->MultiCell(80, 5, "
                =TOTAL
        ", 1, 'R');

        $pdf->SetXY(90, 196);
        $pdf->Write(0, "$" . ($data['fees'][0]+$data['fees'][1] +$r1+$r2));

        $pdf->SetFont('helvetica', '', 6);


        $pdf->Rect(
            20,
            205,
            150,
            10,
            'F' // F = заливка
        );

        $pdf->SetXY(21, 205);
        $pdf->Write(0, "*The additional cost for any additional coverage is contained in the Policy Premium amount.");

        // Опциональные покрытия
        /*$optionalCoverages = implode(', ', $data['optional_coverages']);
        $pdf->SetXY(40, 120);
        $pdf->MultiCell(150, 5, $optionalCoverages);

        // Скидки
        $discounts = implode(', ', $data['discounts']);
        $pdf->SetXY(40, 160);
        $pdf->MultiCell(150, 5, $discounts);

        // Франшиза
        $pdf->SetXY(40, 210);
        $pdf->Write(0, $data['deductible']);

        // Стоимость полиса
        $pdf->SetXY(40, 230);
        $pdf->Write(0, $data['policy_cost']);*/
    }
}
