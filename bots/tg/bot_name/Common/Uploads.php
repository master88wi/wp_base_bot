<?php

namespace dev_bots_ru\tg\Common;

use dev_bots_ru\tg\General\Config;
use dev_bots_ru\tg\General\Parser;
use dev_bots_ru\tg\Senders\TG;

class Uploads
{
    /**
     * Получение файла из ТГ, обработка его названия и загрузка файла в WP
     *
     * @param  string $file_id
     * @return string
     */
    public static function get__file(string $file_id): string
    {
        // Получим ссылку на изображение
        $file = 'https://api.telegram.org/file/bot' . Config::get__bot_token() . '/' . TG::getFile($file_id)['result']['file_path'] ?? '';

        if (!$file) :
            return false;
        endif;

        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        // // Уникализируем имя загружаемого файла
        add_filter('wp_unique_filename', function ($filename, $ext) {
            return md5($filename . time()) . $ext;
        }, 10, 2);

        return $file;
    }

    /**
     * Загрузка конкретно файлов картинок
     *
     * @param  string $file
     * @return string|bool
     */
    public static function wp_upload_media_file(string $file): string|bool
    {
        // Загрузка файла на сайт
        $file__local_id = media_sideload_image($file, User::get__user_ticket_id(), null, 'id');

        // Удалим метаполе, поскольку содержит в себе токен бота
        delete_post_meta($file__local_id, '_source_url', $file);

        // Если ошибка, то вставим сообщение и удалим загруженное фото
        if (is_wp_error($file__local_id)) :
            wp_delete_attachment($file__local_id, true);
            return false;
        endif;

        return $file__local_id;
    }

    /**
     *  Загрузка прочих (не медиа) файлов
     *
     * @param  string $file
     * @return string|bool
     */
    public static function wp_upload_any_file(string $file): string|bool
    {
        $tmp = download_url($file);

        if (is_wp_error($tmp)) :
            return false;
        endif;

        $file_array = [
            'name'     => basename($file),
            'tmp_name' => $tmp,
            'error'    => 0,
            'size'     => filesize($tmp),
        ];

        // Загрузка файла на сайт
        $file__local_id = media_handle_sideload($file_array, User::get__user_ticket_id());

        // Удалим метаполе, поскольку содержит в себе токен бота
        delete_post_meta($file__local_id, '_source_url', $file);

        // Если ошибка, то вставим сообщение и удалим загруженное фото
        if (is_wp_error($file__local_id)) :
            @unlink($file_array['tmp_name']);
            @unlink($tmp);
            return false;
        endif;

        if (file_exists($tmp)) :
            @unlink($tmp);
        endif;

        return $file__local_id;
    }
}
