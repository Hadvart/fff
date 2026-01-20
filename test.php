<?php
// === Настройки ===
$api_url = "http://10.120.4.100:5000";
$api_key = "K13CbMhtGkaTdm4Xdpg59BdjHCqKxRp0TdDykWJpKjrWW8qAYjU6BbwI3Fk05XrwFaNsyoS4FEmnPQns1ZcGktvw4V75DSNzjFLzBJB5U6HhhZdwd1boHpea6TKaKNEW"; // <-- ЗАМЕНИТЕ на ваш настоящий API ключ
$db_host = "localhost";
$db_user = "root";
$db_pass = "pfrpCnd47Dz75VX7";
$db_name = "infong7e_test";

$headers = [
    "X-API-Key: $api_key"
];

// === Проверка доступности сервиса ===
function check_service($api_url, $headers) {
    echo "[*] Проверка доступности API...<br>";
    $ch = curl_init("$api_url/Control");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        echo "[!] Ошибка подключения: " . curl_error($ch) . "<br>";
    } else {
        echo "-> Код ответа: $http_code<br>";
        echo "-> Ответ сервера: $response<br>";
    }
    curl_close($ch);
}

// === Получение и вывод заказов ===
function get_orders($api_url, $headers) {
    echo "<br>[*] Получение списка заказов...<br>";
    $ch = curl_init("$api_url/Orders");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        echo "[!] Ошибка при получении заказов: " . curl_error($ch) . "<br>";
        curl_close($ch);
        return;
    }

    echo "-> Код ответа: $http_code<br>";

    if ($http_code == 200) {
        $orders = json_decode($response, true);

        if (empty($orders)) {
            echo "-> Список заказов пуст.<br>";
            curl_close($ch);
            return;
        }

        if (isset($orders['Number'])) {
            $orders = [$orders]; // если возвращён один объект
        }

        foreach ($orders as $order) {
            echo "<br>[Заказ #" . $order['Number'] . "] " . $order['Name'] . " (Заказчик: " . $order['Customer'] . ")<br>";
            foreach ($order['Products'] ?? [] as $product) {
                $stage = $product['CurrentStage'] ?? [];
                echo "  └─ Изделие №" . $product['Number'] . ", стадия: " . ($stage['Name'] ?? '') .
                    " (" . ($stage['Description'] ?? '') . ")<br>";
                echo "     Дата начала стадии: " . $product['CurrentStageStartDate'] . "<br>";
            }
        }
    } else {
        echo "[!] Ошибка: $http_code — $response<br>";
    }

    curl_close($ch);
}

// === Точка входа ===
check_service($api_url, $headers);
get_orders($api_url, $headers);
?>