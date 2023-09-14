<?php

use dev_bots_ru\tg\Common\Screen;
use dev_bots_ru\tg\General\Parser;

/**
 * Показываем ответ на нажатие кнопки с data my_id
 *
 * @param array $parameters
 * @return void
 */
function my_id__private()
{
    $screen = Screen::get__screen();
    $screen['text'] .= "Ваш ID: " . Parser::$tg_user_id . $screen['text'];
    Screen::send__screen($screen);
}
