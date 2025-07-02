<?php

namespace App\Application\Service;

class TaxApi
{

    private string $url = 'https://api.taxjar.com/v2';

    private string $token = 'b655dd07deb9701532aaf9a07164c17f';


    public function __construct()
    {

    }
    public function getTax(array$data) : ?array
    {
        return $this->request($this->url . '/taxes', $this->token, $data);
    }

    private function request($url, $token, $data) {
        // Преобразуем данные в JSON
        $json_data = json_encode($data);

        // Настраиваем HTTP-заголовки
        $headers = [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json_data)
        ];

        // Создаем контекст с опциями
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $json_data,
                'ignore_errors' => true // Для получения ответа при кодах 4xx/5xx
            ]
        ]);

        // Отправляем запрос и получаем ответ
        $response = file_get_contents($url, false, $context);

        // Проверяем на ошибки
        if ($response === false) {
            throw new \Exception("Tax request error: " . error_get_last()['message']);
        }

        $result = json_decode($response, true);
        if (isset($result['status']) && $result['status'] != '200') {
            throw new \Exception("Tax request error (".$result['status']."): " .$result['detail']);
        }
        return $result;
    }
}