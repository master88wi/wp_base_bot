<?php

namespace dev_bots_ru\tg\Common;

class Buttons
{
    /**
     * Добавление еще одной кнопки в массив кнопок - в новый ряд, для добавления в тот же ряд этот код устарел.
     * 
     * - Если позиция не указана или -1, то добавляет в конец.
     * - Если позиция указано 0 (ноль), то добавляет в начало.
     * - Или добавляет в нужную позицию.
     *
     * @param array $buttons
     * @param array $new_button
     * @param integer $position
     * @return array
     */
    public static function add__button(array $buttons, array $new_button, int $position = -1): array
    {
        if ($position == -1) {
            $buttons = array_merge($buttons, $new_button);
        } else {
            $butons_before_position = array_slice($buttons, 0, $position);
            $butons_after_position = array_slice($buttons, $position, null);
            $buttons = array_merge($butons_before_position, $new_button, $butons_after_position);
        }

        return $buttons;
    }
}
