<?php

namespace dev_bots_ru\tg\General;

use dev_bots_ru\tg\Common\Queries;
use dev_bots_ru\tg\Common\User;
use dev_bots_ru\tg\Senders\TG;
use dev_bots_ru\tg\General\Parser;

/**
 * Зависимые обработчики в директории /Handlers/self::$handler
 */
class Router
{
    /**
     * Именование обработчика запроса
     *
     * @var string
     */
    public static string $handler = '';

    /**
     * Параметры из Callback или Command или установленные в коде
     *
     * @var array
     */
    public static array $parameters = [];

    /**
     * Запуск Executor
     */
    public function __construct()
    {
        Parser::run();
        self::set__handler();
        self::run();
    }

    /**
     * Определим именование обработчика действия
     *
     * @return void
     */
    public static function set__handler()
    {
        // Действие для последующих данных
        $action_data = '';

        // Если действие callback_query
        if (Parser::$action_type == 'callback_query') :
            self::$handler = 'Callback';
            $action_data = Parser::$tg_data['callback_query']['data'];

            // Удалить кликнутое сообщение eсли доступен ID чата и сообщения
            if (
                isset(Parser::$tg_data['callback_query']['message']['chat']['id'])
                && isset(Parser::$tg_data['callback_query']['message']['message_id'])
            ) :
                TG::deleteMessage(
                    Parser::$tg_data['callback_query']['message']['chat']['id'],
                    Parser::$tg_data['callback_query']['message']['message_id']
                );
            endif;
        endif;

        // Обработка если это message
        if (Parser::$action_type == 'message') :

            // Определение действия text или bot_command
            if (isset(Parser::$tg_data['message']['text'])) :

                // Если начинается с косой черты, то это команда боту
                if (strpos(trim(Parser::$tg_data['message']['text']), "/") === 0) :
                    self::$handler = 'Command';
                    $action_data = 'base_action?act=' . ltrim(Parser::$tg_data['message']['text'], '/');
                // Иначе, это просто текст
                else :
                    self::$handler = 'Text';
                endif;

            endif;

        endif;

        if ($action_data) :
            User::add__user_action($action_data);
        endif;
    }

    /**
     * Запуск обработчика.
     * Работает так (для тех, кто хочет дописать своё):
     * - постфикс в названии файла __private.php или __supergroup.php определяет обработчик для типа чата
     * - команды вроде /start или из кнопки колбека определяют название файла: start__private.php
     * - В директории каждого обработчика должен быть файл по умолчанию base_action__private.php
     *
     * @return void
     */
    public static function run()
    {
        // Если именование обработчика действия не определено
        if (self::$handler == '') :
            Errors::add__error_message(
                "Обработчик не определен",
                __FILE__,
                __LINE__
            );
            Errors::send__error_message();
            exit;
        endif;

        // Разберем действие на команду и дополнительные параметры, если они есть
        self::$parameters = Queries::actions__string_to_array(User::get__user_action());

        // Определим команду

        $function_name = (trim(self::$parameters['file']) ?: 'base_action') . '__' . Parser::$chat_type;
        $file =  $function_name . '.php';
        $file_path = Config::get__bot_handlers_dir() . '/' . self::$handler . '/' . $file;

        // Если файл не существует, определим обработчик по умолчанию
        if (!file_exists($file_path)) :

            if (Config::get__dev_mode_status()) :
                Errors::add__error_message(
                    "Не найден файл " . $file . " обработчика " . self::$handler,
                    __FILE__,
                    __LINE__
                );
                Errors::send__error_message();
            endif;

            $function_name = 'base_action' . '__' . Parser::$chat_type;
            $file = $function_name . '.php';
            $file_path = Config::get__bot_handlers_dir() . '/' . self::$handler . '/' . $file;

        endif;

        // Пробуем подключить файл. Если же файл по умолчанию не найден, то будет выброшено исключение в логи
        require_once $file_path;

        // Запустим функцию, если она существует
        if (function_exists($function_name)) :

            $function_name();

        // Отправим ошибку, если функция одноименная не найдена
        else :
            Errors::add__error_message(
                "Не найдена функция " . $function_name . " обработчика " . self::$handler,
                __FILE__,
                __LINE__
            );
            Errors::send__error_message();
            exit;

        endif;
    }
}
