<?php

$htmlFile = '/var/www/html/public/assets/tpl/medical.html';
// Путь для сохранения PDF
$pdfPath = '/var/www/html/public/generated/' . $filename;
try {
    $clinics = $this->clinicRepository->findByTownState($data['town'], $data['state']);
    if (!$clinics) {
        throw new Exception('Clinic not found');
    }
    $clinic = ($clinics && count($clinics) > 0) ? $clinics[rand(0, count($clinics) - 1)] : $clinics[0];

    $diseases = $this->diseasesRepository->findByNumber($data['cause']);

    $vitals = json_decode($diseases['vital_signs'], true);

    foreach ($vitals as $k => $vital) {
        $str = trim($vital['value']);
        // Регулярное выражение для разделения числа и единиц
        if (preg_match('/^([0-9.\/]+)\s*(.*?)$/', $str, $parts)) {
            $value = trim($parts[1]);
            $unit = trim($parts[2]);
            $results[] = ['value' => $value, 'unit' => $unit];
        } else {
            $results[] = ['value' => $str, 'unit' => ''];
        }
    }

    $metrics = json_decode($diseases['key_metrics_trend_values'], true);
    foreach ($metrics as $k => $metric) {
        $str = trim($metric['value']);
        // Регулярное выражение для разделения числа и единиц
        if (preg_match('/^([0-9.\/]+)\s*(.*?)$/', $str, $parts)) {
            $value = trim($parts[1]);
            $m[] = $value;
        } else {
            $m[] = $value;
        }
    }

    $medications = json_decode($diseases['current_medications'], true);
    $diagnoses = json_decode($diseases['diagnoses'], true);

    $labs = json_decode($diseases['lab_results'], true);
    foreach ($labs as $k => $lab) {
        $str = trim($lab['from']);
        if (preg_match('/^([0-9.\/]+)\s*(.*?)$/', $str, $parts)) {
            $value = trim($parts[1]);
            $mt[] = $lab['metric'];
            $p[] = $value;
        } else {
            $mt[] = $lab['metric'];
            $p[] = $value;
        }

        $str = trim($lab['to']);
        if (preg_match('/^([0-9.\/]+)\s*(.*?)$/', $str, $parts)) {
            $value = trim($parts[1]);
            $mt[] = $lab['metric'];
            $c[] = $value;
        } else {
            $mt[] = $lab['metric'];
            $c[] = $value;
        }
    }


    $placeholders = [
        'mt1' => $mt[0],
        'p1' => $p[0],
        'c1' => $c[0],
        'mt2' => $mt[1],
        'p2' => $p[1],
        'c2' => $c[1],
        'mt3' => $mt[2],
        'p3' => $p[2],
        'c3' => $c[2],
        'mt4' => $mt[3],
        'p4' => $p[3],
        'c4' => $c[3],
        'Vital Signs' => $diseases['disease_name'],
        'mmin' => (min($m) == 0) ? 0: min($m)-1,
        'mmax' => max($m)+2,
        'm1' => $m[0],
        'm2' => $m[1],
        'm3' => $m[2],
        'm4' => $m[3],
        'm5' => $m[4],
        'm6' => $m[5],
        'Key Metrics Trend' => $diseases['key_metrics_trend_name'],
        'medical name' => $clinic['name'],
        'medical url' => $clinic['site'],
        'medical address' => $clinic['street_address'] . ', ' . $clinic['town'] . ', ' . $clinic['state'] .' ' . $clinic['zip'],
        'logo' => $clinic['logo_high'],
        'medical phone' => $clinic['phone'],
        'generated date' => date('F j, Y'),
        'Patient Name' => $data['name'],
        'DOB' => date('F j, Y', strtotime($data['dob'])),
        'ID Number' => $uid,
        'Phone Number' => $data['phoneNumber'],
        'Phone Number Emergency' => $data['phoneNumberAdd'],
        'Email Address' => $data['email'],
        'Insurance Provider' => $data['insuranceProvider'],
        'Street Address' => $data['streetAddress'],
        'City, State ZIP' => $data['town'] . ', '  . $data['state'] . ' ' . $data['zip'],
        'Name (Relationship)' => $data['nameAdd'],
        'Value1' => $results[0]['value'],
        'Unit1' => $results[0]['unit'],
        'Vital Sign 1' => $vitals[0]['name'],
        'Value2' => $results[1]['value'],
        'Unit2' => $results[1]['unit'],
        'Vital Sign 2' => $vitals[1]['name'],
        'Value3' => $results[2]['value'],
        'Unit3' => $results[2]['unit'],
        'Vital Sign 3' => $vitals[2]['name'],
        'Value4' => $results[3]['value'],
        'Unit4' => $results[3]['unit'],
        'Vital Sign 4' => $vitals[3]['name'],
        'Value5' => $data['weight'],
        'Unit5' => 'lbs',
        'Vital Sign 5' => 'Weight',
        'Medication A' => $medications[0]['name'],
        'DoseA' => $medications[0]['dosage'],
        'FrequencyA' => $medications[0]['frequency'],
        'ActiveA' => $medications[0]['status'],
        'ActiveAClass' => mb_strtolower($medications[0]['status']),
        'Medication B' => $medications[1]['name'],
        'DoseB' => $medications[1]['dosage'],
        'FrequencyB' => $medications[1]['frequency'],
        'ActiveB' => $medications[1]['status'],
        'ActiveBClass' => mb_strtolower($medications[1]['status']),
        'Medication C' => $medications[2]['name'],
        'DoseC' => $medications[2]['dosage'],
        'FrequencyC' => $medications[2]['frequency'],
        'ActiveC' => $medications[2]['status'],
        'ActiveCClass' => mb_strtolower($medications[2]['status']),
        'Medication D' => $medications[3]['name'],
        'DoseD' => $medications[3]['dosage'],
        'FrequencyD' => $medications[3]['frequency'],
        'ActiveD' => $medications[3]['status'],
        'ActiveDClass' => mb_strtolower($medications[3]['status']),
        'A00' => $diagnoses[0]['code'],
        'DatePrimary' => $diagnoses[0]['description'],
        'B00' => $diagnoses[1]['code'],
        'DateSecondary' => $diagnoses[1]['description'],
        'C00' => $diagnoses[2]['code'],
        'DateTertiary' => $diagnoses[2]['description'],
        'Chief Complaint' => $diseases['chief_complaint'],
        'History of Present Illness' => $diseases['history_present_illness'],
        'Examination Findings' => $diseases['examination_findings'],
        'Assessment' => $diseases['assessment'],
        'Plan' => $diseases['plan'],
        'avatar' => createInitials($clinic['doctor_name']),
        'doctorName' => $clinic['doctor_name'],
        'doctorTitle' => $clinic['doctor_position'],
        'doctorLicense' => '',
        'generatedDateTime' => date('F j, Y • g:i A')
    ];

    $html = file_get_contents($htmlFile);

    foreach ($placeholders as $placeholder => $value) {
        $html = str_replace('[' . $placeholder . ']', $value, $html);
    }

} catch (Exception $ex) {
    throw new Exception('Data error: ' . $ex->getMessage());
}

function createInitials(string $name): string {
    // Разбиваем строку на слова, удаляя лишние пробелы
    $words = array_filter(explode(' ', trim($name)));

    // Если нет слов - возвращаем пустую строку
    if (empty($words)) return '';

    // Берём первую букву первого слова в верхнем регистре
    $firstInitial = mb_strtoupper(mb_substr($words[0], 0, 1));
    $lastInitial = mb_strtoupper(mb_substr($words[1], 0, 1));
    // Если есть второе слово - берём первую букву последнего слова
   /* $lastInitial = (count($words) > 1)
        ? mb_strtoupper(mb_substr(end($words), 0, 1))
        : '';*/

    return $firstInitial . $lastInitial;
}


function convertToPDF($htmlContent, $outputPath) {
    $tempScript = '/var/www/html/tmp/pdf_script_' . uniqid() . '.js';

    $nodeScript = <<<EOT
    const puppeteer = require('puppeteer');
    const fs = require('fs');
    const path = require('path');
    const htmlContent = `$htmlContent`;
    
    (async () => {
        const browser = await puppeteer.launch({
            executablePath: process.env.PUPPETEER_EXECUTABLE_PATH,
            headless: true,
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-gpu',
                '--font-render-hinting=none', // Улучшение рендеринга шрифтов
                '--force-color-profile=srgb' // Корректные цвета
            ],
            ignoreHTTPSErrors: true,
            protocolTimeout: 600000
        });
        
        const page = await browser.newPage();
        
        // Установка viewport для корректного рендеринга
        await page.setViewport({
            width: 1200,
            height: 1000,
            deviceScaleFactor: 0.8 // High DPI для четкого текста
        });
        
        // Загрузка HTML из файла
        
        // Установка контента с ожиданием сетевых запросов
        await page.setContent(htmlContent, {
            waitUntil: 'networkidle0',
            timeout: 300000
        });
        
        // Ожидание полной загрузки страницы
        await page.evaluate(async () => {
            // Дождаться загрузки всех ресурсов
            await new Promise((resolve) => {
                if (document.readyState === 'complete') {
                    resolve();
                } else {
                    window.addEventListener('load', resolve, {once: true});
                }
            });
            
            // Дождаться отрисовки графиков
            if (typeof Chart !== 'undefined') {
                const charts = Chart.instances;
                for (let i = 0; i < charts.length; i++) {
                    await new Promise(resolve => setTimeout(resolve, 3000));
                }
            }
        });
        
        // Дополнительное ожидание для стабилизации рендера
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        // Генерация PDF
        await page.pdf({
            path: '$outputPath',
            format: 'letter',
            printBackground: true,
            margin: { top: '0', right: '0', bottom: '0', left: '0' },
            preferCSSPageSize: false,
            scale: 0.8 // Избегаем масштабирования
        });
        
        await browser.close();
        console.log('PDF successfully generated at: ' + new Date().toISOString());
    })()
    .catch(error => {
        console.error('PDF generation failed:', error);
        process.exit(1);
    });
    EOT;

    file_put_contents($tempScript, $nodeScript);

    set_time_limit(600);
    exec("node $tempScript 2>&1", $output, $return);
    error_log("PDF conversion output: " . implode("\n", $output));
    unlink($tempScript);

    return $return === 0;
}


// Вызов функции конвертации
if (!convertToPDF($html, $pdfPath)) {
    throw new Exception("Error while generating PDF");
}