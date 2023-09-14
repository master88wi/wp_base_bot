<?php

use dev_bots_ru\tg\General\Parser;
use dev_bots_ru\tg\Senders\TG;

/**
 * Можно, например, использовать, как заглушку при ошибке,
 * направляя из роутера все непонятные запросы на base_action.
 * 
 * В данном случае отправляем пользователю его же текст
 *
 * @return void
 */
function base_action__private()
{
    /**
     * Вместо Parser::$tg_raw['message']['text']; можно
     * прописать вместо Parser::$text; как я сделал в более свежих проектах
     */
    $text = Parser::$tg_raw['message']['text'];
    TG::sendMessage(Parser::$tg_user_id, $text);
}
