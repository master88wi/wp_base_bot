<?php

namespace dev_bots_ru\tg\Senders;

use dev_bots_ru\tg\General\Config;

/**
 * Отправка любых действий в Телеграм
 */
class TG
{

    /**
     * Ообычная отправка сообщений
     *
     * @param float $chat_id
     * @param string $send_what
     * @return array Ответ из TG
     */
    public static function sendMessage(
        float $chat_id,
        string $text = '',
        array $keyboard = [],
        array $footer_keyboard = [],
        int $reply_to_message_id = 0,
        bool $disable_notification = false,
        bool $protect_content = false,
        bool $allow_sending_without_reply = true,
        string $parse_mode = 'html',
    ): array {

        $text = $text ?: "Сопроводительное сообщение не заполнено для данного действия.";

        $send_data = [
            'chat_id' => $chat_id,
            'text'  => $text,
            'parse_mode' => $parse_mode,
            'disable_notification' => $disable_notification,
            'protect_content'  => $protect_content,
            'allow_sending_without_reply' => $allow_sending_without_reply,
        ];

        if ($reply_to_message_id != false) :
            $send_data['reply_to_message_id'] = $reply_to_message_id;
        endif;

        // Если переданы инлайн-кнопки
        if (!empty($keyboard)) :
            $send_data['reply_markup'] = ['inline_keyboard' => $keyboard];
        endif;

        // Если переданы кнопки под строкой чата
        if (!empty($footer_keyboard)) :
            $send_data['reply_markup'] = [
                'keyboard' => $footer_keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => true,
            ];
        endif;

        // Удалим клавиатуру, если не передан ни один вид кнопок
        if (empty($keyboard) && empty($footer_keyboard)) :
            $send_data['reply_markup'] = [
                'keyboard' => [],
                'remove_keyboard' => true,
                'selective' => false,
            ];
        endif;

        // Удалим клавиатуру, если не передан ни один вид кнопок
        if (empty($footer_keyboard)) :
            $send_data['reply_markup'] = [
                'keyboard' => [],
                'remove_keyboard' => true,
                'selective' => false,
                'inline_keyboard' => $keyboard
            ];
        endif;

        // 
        if (isset($send_data['reply_markup'])) :
            $send_data['reply_markup'] = json_encode($send_data['reply_markup']);
        endif;

        $send_result = self::sender(__FUNCTION__, $send_data);

        return $send_result;
    }

    /**
     * Ответ колбека
     *
     * @param float $callback_query_id
     * @param string $message
     * @return array
     */
    public static function answerCallbackQuery(float $callback_query_id, string $message): array
    {
        $send_data = [
            'callback_query_id' => $callback_query_id,
            'text' => '',
            'show_alert' => false,
        ];

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Отправка в Телеграм активности, чтобы пользователь видел, что что-то происходит
     *
     * @param float $chat_id
     * @param string $action
     * @return array
     */

    public static function sendChatAction(float $chat_id, string $action = 'typing'): array
    {
        $send_data = [
            'chat_id' => $chat_id,
            'action' => $action,
        ];

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Метод редактирования сообщений
     *
     * @param float $chat_id
     * @param int $message_id
     * @param string $text
     * @param array $keyboard
     * @return array
     */

    public static function editMessageText(
        float $chat_id,
        int $message_id,
        string $text,
        array $keyboard = [],
        string $parse_mode = 'html',
    ): array {
        $send_data = [
            'parse_mode' => $parse_mode,
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => $text,
            'disable_web_page_preview' => true,
        ];

        if (!empty($keyboard)) :
            $send_data['reply_markup'] = json_encode(['inline_keyboard'  => $keyboard]);
        endif;

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Метод редактирования кнопок
     *
     * @param float $chat_id
     * @param integer $message_id
     * @param array $keyboard
     * @return array
     */

    public static function editMessageReplyMarkup(float $chat_id, int $message_id, array $keyboard = []): array
    {
        $send_data = [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'reply_markup' => json_encode(['inline_keyboard'  => array_chunk($keyboard, 3)]),
        ];

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Удаление любого сообщения
     *
     * @param float $chat_id
     * @param integer $message_id
     * @return array
     */
    public static function deleteMessage(float $chat_id, int $message_id): array
    {

        $send_data = [
            'chat_id' => $chat_id,
            'message_id'  => $message_id,
        ];

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Получить ссылку на файл по его ID
     *
     * @param string $file_id
     * @return array
     */
    public static function getFile(string $file_id): array
    {
        $send_data = [
            'file_id' => $file_id,
        ];

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Отправка счет пользователю
     *
     * @param float $chat_id
     * @param array $payment_details
     * @param array $keyboard
     * @return array
     */
    public static function sendInvoice(float $chat_id, array $payment_details, array $keyboard = []): array
    {
        $send_data = [
            'chat_id' => $chat_id,
            'title'  => $payment_details['title'],
            'description'  => $payment_details['description'],
            'payload'  => $payment_details['payload'],
            'provider_token'  => $payment_details['provider_token'],
            'currency'  => $payment_details['currency'],
            'prices'  => $payment_details['prices'],
            'reply_markup'  => json_encode(['inline_keyboard'  => array_chunk($keyboard, 3)]),
        ];

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Отправка фото
     *
     * @param float $chat_id
     * @param string $photo_url
     * @param string $caption
     * @param array $keyboard
     * @return array
     */
    public static function sendPhoto(
        float $chat_id,
        string $photo_url,
        string $caption = '',
        array $keyboard = [],
        int $reply_to_message_id = 0,
        bool $disable_notification = false,
        bool $protect_content = false,
        bool $allow_sending_without_reply = true,
        string $parse_mode = 'html'
    ): array {
        $send_data = [
            'chat_id' => $chat_id,
            'photo'  => $photo_url,
            'parse_mode' => $parse_mode,
            'disable_notification' => $disable_notification,
            'protect_content'  => $protect_content,
            'allow_sending_without_reply' => $allow_sending_without_reply,
        ];

        if ($caption != false) :
            $send_data['caption'] = $caption;
        endif;

        if ($reply_to_message_id != false) :
            $send_data['reply_to_message_id'] = $reply_to_message_id;
        endif;

        if ($keyboard != false) :
            $send_data['reply_markup'] = json_encode(['inline_keyboard'  => $keyboard]);
        endif;

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Отправка видео
     *
     * @param float $chat_id
     * @param string $video_url
     * @param string $caption
     * @param array $keyboard
     * @return array
     */
    public static function sendVideo(
        float $chat_id,
        string $video_url,
        string $caption = '',
        array $keyboard = [],
        int $reply_to_message_id = 0,
        bool $disable_notification = false,
        bool $protect_content = false,
        bool $allow_sending_without_reply = true,
        string $parse_mode = 'html',
    ): array {
        $send_data = [
            'chat_id' => $chat_id,
            'video'  => $video_url,
            'parse_mode' => $parse_mode,
            'disable_notification' => $disable_notification,
            'protect_content'  => $protect_content,
            'allow_sending_without_reply' => $allow_sending_without_reply,
        ];

        if ($caption != false) :
            $send_data['caption'] = $caption;
        endif;

        if ($reply_to_message_id != false) :
            $send_data['reply_to_message_id'] = $reply_to_message_id;
        endif;

        if ($keyboard != false) :
            $send_data['reply_markup'] = json_encode(['inline_keyboard'  => $keyboard]);
        endif;

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Отправка документа
     *
     * @param float $chat_id
     * @param string $document_url
     * @param string $document_caption
     * @return array Answer from TG
     */

    public static function sendDocument(
        float $chat_id,
        string $document_url,
        string $caption = '',
        array $keyboard = [],
        int $reply_to_message_id = 0,
        bool $disable_notification = false,
        bool $protect_content = false,
        bool $allow_sending_without_reply = true,
        bool $disable_content_type_detection = false,
        string $parse_mode = 'html',
    ): array {

        $send_data = [
            'chat_id' => $chat_id,
            'document'  => $document_url,
            'parse_mode' => $parse_mode,
            'disable_notification' => $disable_notification,
            'protect_content'  => $protect_content,
            'allow_sending_without_reply' => $allow_sending_without_reply,
            'disable_content_type_detection' => $disable_content_type_detection,
        ];

        if ($caption != false) :
            $send_data['caption'] = $caption;
        endif;

        if ($reply_to_message_id != false) :
            $send_data['reply_to_message_id'] = $reply_to_message_id;
        endif;

        if ($keyboard != false) :
            $send_data['reply_markup'] = json_encode(['inline_keyboard'  => $keyboard]);
        endif;

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Отправка аудио в Телеграм
     *
     * @param float $chat_id
     * @param string $audio_url
     * @param string $caption
     * @param array $keyboard
     * @return void
     */
    public static function sendAudio(
        float $chat_id,
        string $audio_url,
        string $caption = '',
        array $keyboard = [],
        int $reply_to_message_id = 0,
        bool $disable_notification = false,
        bool $protect_content = false,
        bool $allow_sending_without_reply = true,
        string $parse_mode = 'html',
    ) {
        $send_data = [
            'chat_id' => $chat_id,
            'audio'  => $audio_url,
            'parse_mode' => $parse_mode,
            'disable_notification' => $disable_notification,
            'protect_content'  => $protect_content,
            'allow_sending_without_reply' => $allow_sending_without_reply,
        ];

        if ($caption != false) :
            $send_data['caption'] = $caption;
        endif;

        if ($reply_to_message_id != false) :
            $send_data['reply_to_message_id'] = $reply_to_message_id;
        endif;

        if ($keyboard != false) :
            $send_data['reply_markup'] = json_encode(['inline_keyboard'  => $keyboard]);
        endif;

        return self::sender(__FUNCTION__, $send_data);
    }


    /**
     * Отправка координат для точки на карте
     *
     * @param float $chat_id
     * @param array $media
     * @return void
     */
    public static function sendLocation(float $chat_id, $latitude, $longitude, array $keyboard = [])
    {
        $send_data = [
            'chat_id' => $chat_id,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'horizontal_accuracy' => '50'
        ];

        if (!empty($keyboard)) :
            $send_data['reply_markup'] = json_encode(['inline_keyboard'  => $keyboard]);
        endif;

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Набор медиа одного типа (от 2 до 10)
     *
     * @param float $chat_id
     * @param array $media
     * @return array
     */
    public static function sendMediaGroup(
        float $chat_id,
        array $media,
        int $reply_to_message_id = 0,
        bool $disable_notification = false,
        bool $protect_content = false,
        bool $allow_sending_without_reply  = true,
    ): array {
        //
        $send_data = [
            'chat_id' => $chat_id,
            'media' => json_encode($media),
            'disable_notification' => $disable_notification,
            'protect_content' => $protect_content,
            'allow_sending_without_reply' => $allow_sending_without_reply,
        ];

        //
        if ($reply_to_message_id !== null) :
            $send_data['reply_to_message_id'] = $reply_to_message_id;
        endif;

        return self::sender(__FUNCTION__, $send_data);
    }


    /**
     * Метод ответа на предоплату
     *
     * @param string $pre_checkout_query_id
     * @param boolean $is_ok
     * @param string $error_message
     * @return array
     */
    public static function answerPreCheckoutQuery(string $pre_checkout_query_id, bool $is_ok, string $error_message = ''): array
    {

        $send_data = [
            'pre_checkout_query_id' => $pre_checkout_query_id,
            'ok'  => $is_ok,
            'error_message'  => $error_message,
        ];

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Получим администраторов чата
     *
     * @param float $chat_id
     * @return array
     */
    public static function getChatAdministrators(float $chat_id): array
    {
        $send_data = [
            'chat_id' => $chat_id,
        ];

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Получим количество участников чата
     *
     * @param float $chat_id
     * @return array
     */
    public static function getChatMemberCount(float $chat_id): array
    {
        $send_data = [
            'chat_id' => $chat_id,
        ];

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Получим данные одного пользователя по его TG ID
     *
     * @param float $chat_id
     * @param float $user_id
     * @return array
     */
    public static function getChatMember(float $chat_id, float $user_id): array
    {
        $send_data = [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
        ];

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Покинуть группу
     *
     * @param float $chat_id
     * @return array
     */
    public static function leaveChat(float $chat_id): array
    {
        $send_data = [
            'chat_id' => $chat_id,
        ];

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Получить данные по чату
     *
     * @param float $chat_id
     * @return array
     */
    public static function getChat(float $chat_id): array
    {
        $send_data = [
            'chat_id' => $chat_id,
        ];

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Получить данные о текущем состоянии подключения бота
     *
     * @return array
     */
    public static function getWebhookinfo(): array
    {
        $send_data = [];

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Установка подключения к боту
     *
     * @return array
     */
    public static function setWebhook(): array
    {
        $secret_key_name = Config::get__hook_secret_key_name();
        $secret_key = Config::get__bot_tg_url_hook_secret_key();
        $parameters = [
            'url' => Config::get__bot_root_url() . '/index.php?' . $secret_key_name . "=" . $secret_key,
            'max_connections' => 100,
            'allowed_updates' => [
                'message',
                'edited_message',
                'channel_post',
                'edited_channel_post',
                'inline_query',
                'chosen_inline_result',
                'callback_query',
                'shipping_query',
                'pre_checkout_query',
                'poll',
                'poll_answer',
                'my_chat_member',
                'chat_member',
                'chat_join_request',
            ],
            'drop_pending_updates' => true,
        ];
        $send_data = $parameters;

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Снимем ожидающие запросы (например, в местах обработки видео)
     *
     * @return array
     */
    public static function dropPendingUpdates(): array
    {
        $secret_key_name = Config::get__hook_secret_key_name();
        $secret_key = Config::get__bot_tg_url_hook_secret_key();
        $parameters = [
            'url' => Config::get__bot_root_url() . '/index.php?' . $secret_key_name . "=" . $secret_key,
            'drop_pending_updates' => true,
        ];

        $send_data = $parameters;

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Забанить участника
     *
     * @param float $chat_id
     * @param float $user_id
     * @return array
     */
    public static function banChatMember(float $chat_id, float $user_id): array
    {
        $send_data = [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
        ];

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Установить запреты для участника
     *
     * @param float $chat_id
     * @param float $user_id
     * @param array $new_permissions
     * @return array
     */
    public static function restrictChatMember(float $chat_id, float $user_id, array $new_permissions): array
    {
        $send_data = [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
            'permissions' => json_encode($new_permissions),
        ];

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Установить разрешения для участника
     *
     * @param float $chat_id
     * @param array $permissions
     * @return array
     */
    public static function setChatPermissions(float $chat_id, array $permissions): array
    {
        $send_data = [
            'chat_id' => $chat_id,
            'permissions' => json_encode($permissions),
        ];

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Отправка стикера
     *
     * @param float $chat_id
     * @param string $sticker_id
     * @return array
     */
    public static function sendSticker(float $chat_id, string $sticker_id): array
    {
        $send_data = [
            'chat_id' => $chat_id,
            'sticker' => $sticker_id,
        ];

        return self::sender(__FUNCTION__, $send_data);
    }

    /**
     * Отправка запроса в Телеграм
     * 
     * @param string $method
     * @param array $send_data
     * @return array $result answer from Telegram on sended request
     */
    private static function sender(string $method, array $send_data): array
    {

        /**
         * Инициализация запроса в Телеграм
         */
        $curl = curl_init();
        curl_setopt_array(
            $curl,
            [
                CURLOPT_POST => 1,
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => 'https://api.telegram.org/bot' . Config::get__bot_token() . '/' . $method,
                CURLOPT_POSTFIELDS => json_encode($send_data),
                CURLOPT_HTTPHEADER => array("Content-Type: application/json"),
            ]
        );

        /**
         * Результат запроса в Телеграм
         */
        $result = curl_exec($curl);
        $result = json_decode($result, 1);
        curl_close($curl);

        if ($result['ok'] == false) :
            $result['date_time'] = date('Y-m-d H:i:s');
            file_put_contents(Config::get__bot_root_dir() . '/__errors_tg.log', print_r($result, 1) . "\n\n", FILE_APPEND);
        endif;

        return $result;
    }
}
