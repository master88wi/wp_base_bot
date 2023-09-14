<?php

namespace dev_bots_ru\tg\General;

class Config
{
    // --------------------------------
    // Папки и ссылки бота
    // --------------------------------

    /**
     * Включен ли режим разработчика
     *
     * @return boolean
     */
    public function get__dev_mode_status(): bool
    {
        return true;
    }

    /**
     * Вернем корневую директорию бота, в данном примере bots/tg/bot_name
     *
     * @return string
     */
    public static function get__bot_root_dir(): string
    {
        return __DIR__ . '/..';
    }

    /**
     * Базовый урл до бота
     *
     * @return string
     */
    public static function get__bot_root_url(): string
    {
        return 'https://' . $_SERVER['SERVER_NAME'] . '/bots/tg/bot_name';
    }

    /**
     * Адрес сайта
     *
     * @return string
     */
    public static function get__site_url(): string
    {
        return 'https://' . $_SERVER['SERVER_NAME'];
    }

    /**
     * Вернем кореневую директорию бота
     *
     * @return string
     */
    public static function get__bot_storage_dir(): string
    {
        return self::get__bot_root_dir() . '/Storage';
    }

    /**
     * Папка со вспомогательными классами
     *
     * @return string
     */
    public static function get__bot_classes_common_dir(): string
    {
        return self::get__bot_root_dir() . '/Common';
    }

    /**
     * Папка со всеми обработчиками ТГ-запросов
     *
     * @return string
     */
    public static function get__bot_handlers_dir(): string
    {
        return self::get__bot_root_dir() . '/Handlers';
    }

    // --------------------------------
    // Технические параметры бота
    // --------------------------------

    /**
     * Получить токен бота
     *
     * @return string
     */
    public static function get__bot_token(): string
    {
        return '';
    }

    /**
     * Получить токен бота
     *
     * @return string
     */
    public static function get__bot_tg_id(): string
    {
        return '';
    }

    /**
     * Получить TG ID разработчика бота
     *
     * @return float
     */
    public static function get__bot_developer_tg_id(): float
    {
        return '';
    }

    /**
     * Получить TG ID владельца сервиса
     *
     * @return float
     */
    public static function get__bot_owner_tg_id(): float
    {
        return '';
    }

    /**
     * Название ключа в GET-параметре 
     *
     * @return string
     */
    public static function get__hook_secret_key_name(): string
    {
        return 'secret_key';
    }

    /**
     * Получить секретный ключ в урл Телеграм-хука
     *
     * @return string
     */
    public static function get__bot_tg_url_hook_secret_key(): string
    {
        return '';
    }

    /**
     * Список доступных действий в боте
     *
     * @return array
     */
    public static function get__bot_allowed_actions(): array
    {
        return [
            'message',
            'callback_query',
        ];
    }

    /**
     * Ограничение, сколько последних действий пользователя хранить в БД
     *
     * @return integer
     */
    public static function get__user_limit_actions_to_save(): int
    {
        return '';
    }

    // --------------------------------
    // Ключи от БД для конкретного проекта - свои специфические
    // --------------------------------

    /**
     * Название ключа в БД для последних действий пользователя
     *
     * @return string
     */
    public static function get__db_key__tg_user_actions(): string
    {
        return 'tg_user_actions';
    }

    /**
     * Название ключа в БД для TG ID пользователя
     *
     * @return string
     */
    public static function get__db_key__tg_user_id(): string
    {
        return 'tg_user_id';
    }

    /**
     * Название ключа в БД для TG имени пользователя
     *
     * @return string
     */
    public static function get__db_key__tg_user_name(): string
    {
        return 'first_name';
    }

    /**
     * Название ключа в БД для хранения времени первого контакта с ботом
     *
     * @return string
     */
    public static function get__db_key__tg_user_first_contact_time(): string
    {
        return 'tg_user_first_contact_time';
    }

    /**
     * Название ключа в БД для хранения статуса бана пользователем бота
     *
     * @return string
     */
    public static function get__db_key__tg_user_was_ban_bot(): string
    {
        return 'tg_user_was_ban_bot';
    }

    /**
     * Название ключа в БД для хранения статуса бана пользователя оператором
     *
     * @return string
     */
    public static function get__db_key__tg_support_was_ban_user(): string
    {
        return 'tg_user_was_baned_by_support';
    }

    /**
     * Название ключа в БД для контроля 30 запросов к боту в секунду
     *
     * @return string
     */
    public static function get__db_key__tg_bot_request_count(): string
    {
        return 'tg_bot_request_count';
    }

    /**
     * Возвращает ключ, по которому можно найти сообщение из ТГ, сохраненное в БД.
     * К этому префиксу добавляется время, когда сообщение было сохранено в БД.
     *
     * @return string
     */
    public static function get__db_key__user_message_prefix(): string
    {
        return 'tg_user_message__';
    }

    /**
     * Ключ в БД, по которому хранится учет последнего время отправки уведомления.
     * Используется или для метаполей пользователя и для метаполей тикета (для операторов).
     *
     * @return string
     */
    public static function get__db_key__notify_frequency_count(): string
    {
        return 'notify_frequency_count';
    }

    // --------------------------------
    // Настройки сайта
    // --------------------------------

    /**
     * Название произвольного типа записи для сохранения
     *
     * @return string
     */
    public static function get__post_type(): string
    {
        return '';
    }

    /**
     * Название произвольного типа таксономии с запросами/тикетами
     *
     * @return string
     */
    public static function get__post_taxonomy(): string
    {
        return '';
    }
}
