<?php

use dev_bots_ru\tg\Common\Screen;

/**
 * Реакция на нажатие кнопки с data start
 *
 * @return void
 */
function start__private()
{
    /**
     * В $screen_data находятся сразу все данные по отправляемому экрану:
     * - Кнопки
     * - Текст
     * - Картинки
     * - и т.д.
     */
    $screen_data = Screen::get__screen();
    Screen::send__screen($screen_data);
}
