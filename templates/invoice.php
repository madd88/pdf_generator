<?php

use App\Application\Service\TaxApi;

$api = new TaxApi();


$invoiceData = [
    'business' => [
        'name' => '',
        'address' => '',
        'town' => '',
        'state' => '',
        'zip' => '',
        'email' => '',
        'phone' => '',
        'einVatId' => ''
    ],
    'customer' => [
        'businessPersonalName' => '',
        'officerPersonalName' => '',
        'address' => '',
        'town' => '',
        'state' => '',
        'zip' => '',
        'shippingAddress' => '',
        'shippingTown' => '',
        'shippingState' => '',
        'shippingZip' => '',
        'email' => ''
    ],
    'items' => [
        [
            'name' => '',
            'description' => '',
            'quantity' => 0,
            'pricePerItem' => 0.0,
            'discount' => 0.0
        ]
    ],
    'invoice' => [
        'date' => '',
        'dueDate' => '',
        'notes' => '',
        'status' => '',
        'projectReference' => ''
    ],
    'paymentMethods' => [
        [
            'type' => '',
            'description' => '',
            'cashDeliveryAddress' => '',
            'cashDeliveryTown' => '',
            'cashDeliveryState' => '',
            'cashDeliveryZip' => '',
            'bankName' => '',
            'accountNumber' => '',
            'routingNumber' => '',
            'account' => '',
            'cryptoName' => '',
            'cryptoAddress' => '',
            'paymentSite' => '',
            'methodName' => '',
            'methodDescription' => ''
        ]
    ]
];
$items = '<table class="invoice-table">
                        <thead>
                            <tr>
                                <th style="width: 45%;">Item & Description</th>
                                <th class="quantity" style="width: 10%;">Qty</th>
                                <th class="text-right" style="width: 15%;">Rate</th>
                                <th class="text-right" style="width: 15%;">Discount</th>
                                <th class="text-right" style="width: 15%;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>';

$subtotal = 0;
$discount = 0;
foreach ($data['items'] as $k => $item) {
    $pc1 = $item['pricePerItem']/100;

    $items .= '<tr>
                                <td>
                                    <div class="item-name">'.$item['name'].'</div>
                                    <div class="item-description">'.$item['description'].'</div>
                                </td>
                                <td class="quantity">'.$item['quantity'].'</td>
                                <td class="text-right">$'.number_format($item['pricePerItem']).'</td>
                                <td class="text-right">$'.number_format($item['discount']*$pc1).'</td>
                                <td class="text-right">$'.(number_format(($item['pricePerItem']*$item['quantity'] - $item['discount']))).'</td>';
    $subtotal += $item['pricePerItem'] * $item['quantity'];
    $discount += $item['discount']*$pc1;
}
if (count($data['items']) === 1) {
    $items .= '<tr>
                                <td>
                                    <div class="item-name">Invoicing</div>
                                    <div class="item-description"></div>
                                </td>
                                <td class="quantity">1</td>
                                <td class="text-right">$0</td>
                                <td class="text-right">$0</td>
                                <td class="text-right">$0</td>';
}

$discountProc = round($discount/($subtotal/100), 1);

$taxData = mapping($data);

$tax = $api->getTax($taxData);

$taxProc = $tax['tax']['rate']*100;
$taxAmount = $tax['tax']['amount_to_collect'];
$total = $subtotal - $discount + $taxAmount;


$items .= '</tbody>
                    </table>';

$ext_id = $this->invoiceRepository->generateExtId(date('Y'));
$year = date('Y');

$placeholders = [
    'notes' => $data['notes'],
    'businessLogo' => '',
    'businessName' => $data['business.name'],
    'businessAddress' => $data['business.address'],
    'businessTown' => $data['business.town'],
    'businessState' => $data['business.state'],
    'businessZip' => $data['business.zip'],
    'businessEmail' => $data['business.email'],
    'business.phone' => $data['business.phone'],
    'invoiceNumber' => "INV-{$year}-{$ext_id}",
    'invoiceDate' => date('F j, Y', strtotime($data['invoice.date'])),
    'invoiceDateDue' => date('F j, Y', strtotime($data['invoice.dueDate'])),
    'invoiceStatusClass' => mb_strtolower($data['invoice.status']),
    'invoiceStatus' => $data['invoice.status'],
    'customerBusinessPersonalName' => $data['customer.businessPersonalName'],
    'customerName' => $data['customer.officerPersonalName'],
    'customerAddress' => $data['customer.address'],
    'customerTown' => $data['customer.town'],
    'customerState' => $data['customer.state'],
    'customerZip' => $data['customer.zip'],
    'customerEmail' => $data['customer.email'],
    'customerAccount' => $data['customer.account'],
    'paymentTerms' => $data['paymentTerms'],
    'paymentAccount' => $data['paymentMethods'][0]['account'],
    'paymentPoNumber' => $data['poNumber'],
    'paymentReference' => $data['invoice.projectReference'],
    'items' => $items,
    'Subtotal' => number_format($subtotal),
    'discountProc' => $discountProc,
    'discount' => number_format($discount),
    'taxProc' => $taxProc,
    'taxAmount' => number_format($taxAmount),
    'total' => number_format($total),
    'bankName' => $data['paymentMethods'][0]['bankName'],
    'accountNumber' => $data['paymentMethods'][0]['accountNumber'],
    'routingNumber' => $data['paymentMethods'][0]['routingNumber'],
    'paymentSite' => $data['paymentMethods'][0]['paymentSite'],
    'cryptoName' => $data['paymentMethods'][0]['cryptoName'],
    'cryptoAddress' => $data['paymentMethods'][0]['cryptoAddress'],
    'methodName' => $data['paymentMethods'][0]['methodName'],
    'methodDescription' => $data['paymentMethods'][0]['methodDescription'],
    'taxId' => $uid,
];

foreach ($data['paymentMethods'][0]['type'] as $paymentMethod) {
    switch ($paymentMethod) {
        case 'Cash':
            $placeholders['paymentMethods'] .= '<div class="payment-method">
                        <div class="payment-icon">C</div>
                        <div class="payment-details">
                            <div class="payment-name">Cash</div>
                            <div>'.$data['paymentMethods'][0]['cashDeliveryAddress'].'</div>
                            <div>'.$data['paymentMethods'][0]['cashDeliveryTown'].', '.$data['paymentMethods'][0]['cashDeliveryState'].' '.$data['paymentMethods'][0]['cashDeliveryZip'].'</div>
                        </div>
                    </div>';
            break;
        case 'creditCard':
            $placeholders['paymentMethods'] .= '<div class="payment-method">
                        <div class="payment-icon">C</div>
                        <div class="payment-details">
                            <div class="payment-name">Credit Card</div>
                            <div>Secure online payment</div>
                            <div>'.$data['paymentMethods'][0]['paymentSite'].'</div>
                        </div>
                    </div>';
            break;
        case 'BankTransfer':
            $placeholders['paymentMethods'] .= '<div class="payment-method">
                        <div class="payment-icon">B</div>
                        <div class="payment-details">
                            <div class="payment-name">Bank Transfer</div>
                            <div>'.$data['paymentMethods'][0]['bankName'].'</div>
                            <div>Acc #: '.$data['paymentMethods'][0]['accountNumber'].'</div>
                            <div>Routing: '.$data['paymentMethods'][0]['routingNumber'].'</div>
                        </div>
                    </div>';
            break;
        case 'Crypto':
            $placeholders['paymentMethods'] .= '<div class="payment-method">
                        <div class="payment-icon">C</div>
                        <div class="payment-details">
                            <div class="payment-name">Crypto</div>
                            <div>Name: ' . $data['paymentMethods'][0]['cryptoName'] . '</div>
                            <div>Address: ' . $data['paymentMethods'][0]['cryptoAddress'] . '</div>
                        </div>
                    </div>';
            break;
        case 'Other':
            $placeholders['paymentMethods'] .= '<div class="payment-method">
                        <div class="payment-icon">C</div>
                        <div class="payment-details">
                            <div class="payment-name">Other</div>
                            <div>' . $data['paymentMethods'][0]['methodName'] . '</div>
                            <div>' . $data['paymentMethods'][0]['methodDescription'] . '</div>
                        </div>
                    </div>';
            break;
        case 'Paypal':
        case 'Zelle':
        case 'Venmo':
        case 'CashApp':
            $placeholders['paymentMethods'] .= '<div class="payment-method">
                        <div class="payment-icon">'.$paymentMethod[0].'</div>
                        <div class="payment-details">
                            <div class="payment-name">'. $paymentMethod .'</div>
                            <div>Email or account nick: ' . $data['paymentMethods'][0]['account'] . '</div>
                        </div>
                    </div>';
            break;

    }
}

$invoiceData = $placeholders;


function mapping(array $targetData): array {
    $result = [
        'from_country' => 'US',
        'from_zip' => $targetData['business.zip'] ?? '',
        'from_state' => $targetData['business.state'] ?? '',
        'from_city' => $targetData['business.town'] ?? '',
        'from_street' => $targetData['business.address'] ?? '',
        'to_country' => 'US',
        'to_zip' => $targetData['customer.zip'] ?? '',
        'to_state' => $targetData['customer.state'] ?? '',
        'to_city' => $targetData['customer.town'] ?? '',
        'to_street' => $targetData['customer.address'] ?? '',
        'amount' => 0,
        'shipping' => 0.0,
        'nexus_addresses' => [],
        'line_items' => []
    ];

    // Рассчитываем общую сумму (amount)
    $totalAmount = 0.0;
    if (isset($targetData['items']) && is_array($targetData['items'])) {
        foreach ($targetData['items'] as $index => $item) {
            $quantity = $item['quantity'] ?? 0;
            $pricePerItem = $item['pricePerItem'] ?? 0.0;
            $discount = $item['discount'] ?? 0.0;

            $lineTotal = ($quantity * $pricePerItem) - $discount;
            $totalAmount += $lineTotal;

            $result['line_items'][] = [
                'id' => (string)($index + 1),
                'quantity' => $quantity,
                'product_tax_code' => '',
                'unit_price' => $pricePerItem,
                'discount' => $discount
            ];
        }
    }

    $result['amount'] = $totalAmount;

    // Формируем nexus_addresses из бизнес-адреса
    if (!empty($targetData['business']['address'])) {
        $result['nexus_addresses'][] = [
            'id' => 'Main Location',
            'country' => '',
            'zip' => $targetData['business']['zip'] ?? '',
            'state' => $targetData['business']['state'] ?? '',
            'city' => $targetData['business']['town'] ?? '',
            'street' => $targetData['business']['address'] ?? ''
        ];
    }

    return $result;
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


try {
    $htmlFile = '/var/www/html/public/assets/tpl/invoice.html';
// Путь для сохранения PDF
    $pdfPath = '/var/www/html/public/generated/' . $filename;

    $html = file_get_contents($htmlFile);

    foreach ($placeholders as $placeholder => $value) {
        $html = str_replace('[' . $placeholder . ']', $value, $html);
    }
    if (!convertToPDF($html, $pdfPath)) {
        throw new Exception("Error while generating PDF");
    }
} catch (Exception $exception) {
    throw new Exception($exception->getMessage());
}
