<?php

namespace dev_bots_ru\tg\Senders;

use dev_bots_ru\tg\General\Config;

class GPT
{
    private static string $api_key;

    private static string $model_name;

    private static $temperature;

    private static int $max_tokens;

    private static $presence_penalty;

    private static $frequency_penalty;

    private static string|array $data;

    /**
     * 
     *
     * @param  string    $api_key
     * @param  string    $model_name
     * @param  float     $temperature
     * @param  integer   $max_tokens
     * @param  string    $prompt_text
     * @return \stdClass
     */
    public static function send_ai_request(string|array $data): array
    {
        self::$api_key = get_field('token_chatgpt', 'option'); // Пример, как из ACF забрать настройки, которыми управляет клиент
        self::$model_name = get_field('model_chatgpt', 'option'); // Пример, как из ACF забрать настройки, которыми управляет клиент
        self::$temperature = get_field('temperatura_otveta_chatgpt', 'option'); // Пример, как из ACF забрать настройки, которыми управляет клиент
        self::$max_tokens = get_field('maksimalnoe_kolichestvo_tokenov_v_otvete', 'option'); // Пример, как из ACF забрать настройки, которыми управляет клиент
        self::$presence_penalty = get_field('povtorenie_slov_i_fraz_v_otvete_ii', 'option'); // Пример, как из ACF забрать настройки, которыми управляет клиент
        self::$frequency_penalty = get_field('ispolzovanie_rasprostranennyh_slov_i_fraz', 'option'); // Пример, как из ACF забрать настройки, которыми управляет клиент
        self::$data = $data;

        return self::sender();
    }

    /**
     * Отправка запроса
     *
     * @param array $send_data
     * @return array
     */
    private static function sender(): array
    {
        // Формируем данные для запроса
        $data = array(
            'presence_penalty' => 1.0,
            'frequency_penalty' => 1.0,
            'max_tokens' =>  self::$max_tokens,
            'top_p' =>  round(str_replace(",", ".", self::$temperature), 2),
            'model' => self::$model_name,
            'messages' => self::$data,
        );

        $api_url = 'https://api.openai.com/v1/chat/completions';

        // Настройки для cURL
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . self::$api_key
        ));

        // Выполняем запрос
        $response = json_decode(curl_exec($ch), 1);

        // Проверяем наличие ошибок
        if (curl_errno($ch)) {
            $response['ok'] = false;
            $response['date_time'] = date('Y-m-d H:i:s');
            file_put_contents(Config::get__bot_root_dir() . '/__errors_ai.log', print_r($response, 1) . "\n\n", FILE_APPEND);
        } else {
            $response['ok'] = true;
        }

        // Закрываем сессию cURL
        curl_close($ch);

        return $response;
    }
}
