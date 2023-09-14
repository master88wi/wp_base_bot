<?php

namespace dev_bots_ru\tg\Common;

use dev_bots_ru\tg\Common\Replacer;
use dev_bots_ru\tg\General\Parser;
use dev_bots_ru\tg\General\Router;
use dev_bots_ru\tg\Senders\TG;

class Screen
{

    /**
     * Сформируем данные экрана. Экраны могут быть прописаны в админке или в файле.
     *
     * @return array|bool
     */
    public static function get__screen(): array|bool
    {
        // Название экрана
        $screen_name = '';

        // Храним данные текстового поля экрана
        $text = '';

        // Храним кнопки экрана
        $buttons = [];

        // Храним ссылки на изображения
        $media = [];

        // Храним подписи к медиа
        $media_type = '';

        // Храним подписи к медиа
        $caption = '';

        // Параметры для экрана местоположение
        $location = '';

        // Храним все данные
        $screen_data = [];

        // Получим данные всех экранов
        $data_collection = get_post_meta(00000, 'screen_data', 'option');

        foreach ($data_collection as $num => $data) :
        // Ваша логика обработки данных экрана
        endforeach;

        $screen_data = [
            'screen_type' => $data['tip_ekrana'] ?? '',
            'screen_name' => $screen_name,
            'text' => $text,
            'buttons' => $buttons,
            'media' => $media,
            'media_type' => $media_type,
            'captions' => $caption,
            'location' => $location,
            'parameters' => Router::$parameters,
        ];

        return $screen_data;
    }

    /**
     * Отправим экран пользователю в Телеграм
     *
     * @param array $screen_data
     * @return void
     */
    public static function send__screen(array $screen_data)
    {
        // Кнопки, если таковые есть
        $buttons = $screen_data['buttons'] ?? [];

        // Кнопки под строкой чата, если таковые есть
        $buttons_footer = $screen_data['buttons_footer'] ?? [];

        // Медиа, включая описания к нему
        $media = $screen_data['media'] ?? [];

        // Местоположение
        $location = $screen_data['location'] ?? '';

        // Обычный текст
        $text = $screen_data['text'] ?? '';

        /**
         * Прочие примеры отправки удалил, например, фото группой или фото одно, отправку документов и т.д.
         */

        // Если есть текст в экране
        if ($text != false) :

            // Заменяем все псевдокоды
            $text = Replacer::replace_pseudo_code($text);

            if ($buttons == false) :
                $buttons = [];
            endif;

            if ($buttons_footer == false) :
                $buttons_footer = [];
            endif;

            $res = TG::sendMessage(Parser::$tg_user_id, $text, $buttons, $buttons_footer);

            // Что делаем, если ошибка доставки в ТГ
            if (isset($res['ok']) && $res['ok'] == false) :
            endif;

        endif;

        return $res;
    }
}
