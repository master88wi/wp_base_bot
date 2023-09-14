<?php

namespace dev_bots_ru\tg\Common;

class Texts
{
    /**
     * Получим телефон в формате 79999999999
     *
     * @param string $text
     * @return string
     */
    public static function get__phone_only_digits(string $phone): string
    {
        $phone = '';
        return $phone;
    }

    /**
     * Конвертируем разметку MarkDown в HTML
     *
     * @param  string $text
     * @return string
     */
    public static function markdown_to_html(string $text): string
    {
        $text = '';
        return $text;
    }

    /**
     * Конвертируем разметку из HTML в MarkDown 
     *
     * @param  string $html
     * @return string
     */
    public static function html_to_markdown(string $html): string
    {
        $text = '';
        return $html;
    }

    /**
     * Удаляем неподдерживаемые Телеграм теги и атрибуты
     *
     * @param  string $html
     * @return string
     */
    public static function clean_telegram_html(string $html): string
    {
        $text = '';
        return $html;
    }
}
