<?php

use dev_bots_ru\tg\General\Parser;
use dev_bots_ru\tg\General\Router;
use dev_bots_ru\tg\Senders\TG;

function base_action__private()
{
    /**
     * Какое-то базовое действие, например, приветствие и стартовые кнопки
     * Также можно использовать, как заглушку при ошибке.
     */

    /**
     * Можно оставить только этот файл и направлять через switch/case на другой обработчик
     */
    switch (Router::$parameters['action']):

        case 'post':
            TG::sendMessage(Parser::$tg_user_id, "Вы запросили запись");
            break;

        case 'username':
            TG::sendMessage(Parser::$tg_user_id, "Вы запросили имя пользователя");
            break;

        default:
            TG::sendMessage(Parser::$tg_user_id, "Возникла непредвиденная ситуация.");
            exit;

    endswitch;
}
