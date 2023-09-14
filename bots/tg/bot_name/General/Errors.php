<?php

namespace dev_bots_ru\tg\General;

use dev_bots_ru\tg\Senders\TG;

class Errors
{
    private static array $errors_list = [];

    /**
     * Запись ошибки.
     * Полный путь отрезается - сохраняется только название файла.
     *
     * @param  string $message
     * @param  string $file
     * @param  string $file
     * @return void
     */
    public static function add__error_message(string $message, string $file, string $line, string $recipient = '')
    {
        $file = basename($file);
        if (!Config::get__dev_mode_status()) :
            $file = "ND";
            $line = "ND";
        endif;

        self::$errors_list[] = [
            'error_text' => $message,
            'error_file' => $file,
            'error_line' => $line,
            'recipient' => $recipient
        ];
    }

    /**
     * Отправка накопленных сообщений об ошибке
     *
     * @param mixed $message
     * @return void
     */
    public static function send__error_message()
    {
        foreach (self::$errors_list as $key => &$error) :
            $recipient = $error['recipient'] ?: Parser::$tg_user_id;
            $error['recipient'] = '';
            $error = array_filter($error);
            TG::sendMessage($recipient, '<pre>' .  print_r($error, 1) . '</pre>');
        endforeach;
    }
}
