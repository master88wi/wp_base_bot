<?php

namespace dev_bots_ru;

ini_set("log_errors", 1);
ini_set("error_log", __DIR__ . '/__errors.log');

use dev_bots_ru\tg\General\Config;
use dev_bots_ru\tg\Senders\TG;
use dev_bots_ru\tg\General\Router;

header("Content-Type: text/html; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");

// Только для безбраузерных запросов
// Тут можно/нужно добавить проверку заголовка на Cron запросы, 
// если они нужны
if (strlen($_SERVER['HTTP_USER_AGENT']) > 0) :
    exit('Silent is gold!');
endif;


try {

    /**
     * Загрузка WordPress
     */
    require_once __DIR__ . '/../../../wp-load.php';

    /**
     * Автозагрузка классов
     */
    spl_autoload_register(function ($className) {
        $filePath = str_replace("\\", "/", $className);
        $filePath = str_replace("dev_bots_ru/tg/", "", $filePath);
        require_once $filePath . '.php';
    });

    // Если GET пришел не через веб
    if (isset($argv) && $argv) :
        for ($i = 1; $i < count($argv); $i++) :
            $parts = explode('=', $argv[$i], 2);
            if (count($parts) == 2) :
                $name = $parts[0];
                $value = $parts[1];
                $_GET[$name] = $value;
            else :
                $_GET[$parts[0]] = $parts[0];
            endif;
        endfor;
    endif;

    //
    if (isset($_GET['cron']) && isset($_GET['action'])) :
        Cron::run();
    else :
        new Router();
    endif;
}

//
catch (\Throwable $t) {

    file_put_contents(__DIR__ . '/__errors.log', "\n\n" . date('Y-m-d H:i:s') . "\n\n" . print_r($t->__toString(), 1), FILE_APPEND);
    $owner_tg_is = Config::get__bot_owner_tg_id() ?? '';
    if ($owner_tg_is) :
        TG::sendMessage($owner_tg_is, "Ошибка. Смотреть в логах.");
    endif;
}
