<!-- 
    composer require irazasyed/telegram-bot-sdk
    composer require khanamiryan/qrcode-detector-decoder
-->

<?php

require_once __DIR__ . '/vendor/autoload.php';

use Telegram\Bot\Api;
use Zxing\QrReader;

function extract_command($inputString)
{
    $inputString = substr($inputString, 1);

    $args = explode(' ', $inputString);

    return $args[0];
}

function extract_params($inputString)
{
    $inputString = substr($inputString, 1);

    $args = explode(' ', $inputString);

    $params = array();
    $currentParam = '';
    $inQuotes = false;

    foreach ($args as $arg) {
        if (!$inQuotes && strpos($arg, '"') === 0) {
            $inQuotes = true;
            $currentParam = substr($arg, 1);
        } elseif ($inQuotes && strrpos($arg, '"') === strlen($arg) - 1) {
            $inQuotes = false;
            $currentParam .= ' ' . substr($arg, 0, -1);
            $params[] = $currentParam;
            $currentParam = '';
        } elseif ($inQuotes) {
            $currentParam .= ' ' . $arg;
        } else {
            $params[] = $arg;
        }
    }

    return $params;
}

function image_preprocess_0($path)
{
    $image = imagecreatefromjpeg($path);
    imagefilter($image, IMG_FILTER_GRAYSCALE);
    imagefilter($image, IMG_FILTER_CONTRAST, -25);
    imagefilter($image, IMG_FILTER_BRIGHTNESS, 50);
    imagejpeg($image, $path . ".prep.jpg", 100);
}

function image_preprocess_1($path)
{
    $image = imagecreatefromjpeg($path);

    $matrix = [
        [-1, -1, -1],
        [-1, 16, -1],
        [-1, -1, -1]
    ];

    imageconvolution($image, $matrix, 8, 0);

    imagejpeg($image, $path . ".prep.jpg", 100);
}

function process_commands($telegram, $message)
{
    $commands = [
        'start',
        'support',
        'list',
    ];

    print_r($message);

    $command = extract_command($message->getText());
    $params = extract_params($message->getText());
    if (in_array($command, $commands)) {
        switch ($command) {
            case 'start':
                command_start($telegram, $message, $params);
                break;
            case 'support':
                command_support($telegram, $message, $params);
                break;
            case 'list':
                $output_text = "Список доступных комманд:\n";
                foreach ($commands as $command_name) {
                    $output_text = $output_text . "/" . $command_name . "\n";
                }
                $telegram->sendMessage([
                    'chat_id' => $message->getFrom()->getId(),
                    'text' => $output_text,
                ]);
                break;
            default:
                echo "wtf?\n";
                break;
        }
    } else {
        $telegram->sendMessage([
            'chat_id' => $message->getFrom()->getId(),
            'text' => 'Неизвестная команда /' . $command,
        ]);
    }
}

function process_webhooks($telegram, $updates_offset)
{
    echo __FUNCTION__ . "\n";
    $updates = $telegram->getUpdates([
        'offset' => $updates_offset,
    ]);

    foreach ($updates as $update) {
        if ($updates_offset == 0)
            $updates_offset = $update['update_id'];
        $updates_offset += 1;
        $php_ini = fopen("./php.ini", "w");
        fwrite($php_ini, "updates_offset = " . $updates_offset);

        if ($update->has('message') && $update->getMessage()->has('photo')) {
            $photo = $update->getMessage()->getPhoto()[sizeof($update->getMessage()->getPhoto()) - 1]->getFileId();

            $file = $telegram->getFile(['file_id' => $photo]);

            $imagePath = $telegram->downloadFile($file->getFileId(), './images/');

            $qrcode = new QrReader($imagePath);
            $text = $qrcode->text();

            if (!empty($text)) {
                $telegram->sendMessage([
                    'chat_id' => $update->getMessage()->getChat()->getId(),
                    'parse_mode' => 'HTML',
                    'text' => "<a href=\"" . $text . "\">Перейдите по ссылке</a>, чтобы узнать всю информацию о продукте\n\nЗаботьесь о своем здоровье и питании вместе с EcoTrace. Подробнее по <a href=\"https://primitives.ru/calculator.html\">ссылке</a>",
                ]);
            } else {
                image_preprocess_0($imagePath);

                $qrcode = new QrReader($imagePath . ".prep.jpg");
                $text = $qrcode->text();

                if (!empty($text)) {
                    $telegram->sendMessage([
                        'chat_id' => $update->getMessage()->getChat()->getId(),
                        'parse_mode' => 'HTML',
                        'text' => "<a href=\"" . $text . "\">Перейдите по ссылке</a>, чтобы узнать всю информацию о продукте\n\nЗаботьесь о своем здоровье и питании вместе с EcoTrace. Подробнее по <a href=\"https://primitives.ru/calculator.html\">ссылке</a>",
                    ]);
                } else {
                    image_preprocess_1($imagePath);

                    $qrcode = new QrReader($imagePath . ".prep.jpg");
                    $text = $qrcode->text();

                    if (!empty($text)) {
                        $telegram->sendMessage([
                            'chat_id' => $update->getMessage()->getChat()->getId(),
                            'parse_mode' => 'HTML',
                            'text' => "<a href=\"" . $text . "\">Перейдите по ссылке</a>, чтобы узнать всю информацию о продукте\n\nЗаботьесь о своем здоровье и питании вместе с EcoTrace. Подробнее по <a href=\"https://primitives.ru/calculator.html\">ссылке</a>",
                        ]);
                    } else {
                        $telegram->sendMessage([
                            'chat_id' => $update->getMessage()->getChat()->getId(),
                            'text' => 'Увы, не удалось, отсканировать QR-код. Попробуйте ещё раз',
                        ]);
                    }
                }

                unlink($imagePath . ".prep.jpg");
            }

            unlink($imagePath);

            continue;
        }

        if ($update->has('message') && $update->getMessage()->getText()[0] == '/')
            process_commands($telegram, $update->getMessage());
    }

    return $updates_offset;
}

function command_start($telegram, $message, $params)
{
    $telegram->sendMessage([
        'chat_id' => $message->getFrom()->getId(),
        'parse_mode' => 'HTML',
        'text' => $message->getFrom()->getFirstName() . ", приветствую! Я бот <a href=\"http://primitives.ru/\">EcoTrace</a>. Сделайте фотографию QR-кода, чтобы я мог сообщить информацию о продукте",
    ]);
}

function command_support($telegram, $message, $params)
{
    // Устанавливаем соединение с базой данных
    try {
        $pdo = new PDO('sqlite:./support_database.db');
    } catch (PDOException $e) {
        die('Не удалось установить соединение с базой данных: ' . $e->getMessage());
    }

    // Получаем данные из формы
    $customer_id = $message->getFrom()->getId();
    $first_name = $message->getFrom()->getFirstName();
    $last_name = $message->getFrom()->getLastName();
    $email = $message->getFrom()->getUsername();

    echo 'SELECT * FROM customers WHERE customer_id = '.$customer_id."\n";
    $result = $pdo->prepare('SELECT * FROM customers WHERE customer_id = ?');
    $result->execute([$customer_id]);

    if ($result->rowCount() == 0)
    {
        // Запрос на добавление нового клиента
        $stmt = $pdo->prepare("INSERT INTO customers (customer_id, first_name, last_name, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$customer_id, $first_name, $last_name, $email]);
    }
    $subject = $params[0];
    $message = $params[1];

    // Запрос на добавление нового вопроса
    $stmt = $pdo->prepare("INSERT INTO questions (customer_id, subject, message, created_at, status) VALUES (?, ?, ?, datetime('now'), 'open')");
    $stmt->execute([$customer_id, $subject, $message]);
}

$token = "Здесь лежит наш токен :)";

$telegram = new Api($token);

$response = $telegram->getMe();

echo $response . "\n";

$updates_offset = 0;

while (TRUE) {
    try {
        $updates_offset = process_webhooks($telegram, $updates_offset);
    } catch (\Throwable $th) {
        echo $th->getMessage() . "\n";
    }
}
