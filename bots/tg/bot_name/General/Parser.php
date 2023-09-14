<?php

namespace dev_bots_ru\tg\General;

use dev_bots_ru\tg\Senders\TG;

/**
 * Валидатор всего и вся - его мы таскаем по всему коду
 */
class Parser
{

    /**
     * Входящие данные из Телеграма
     * @var array
     */
    public static array $tg_raw = [];

    /**
     * Результат обнаружения данных пользователя
     *
     * @var array default empty array
     */
    public static array $user_object = [];

    /**
     * Определенное скриптом действие бота = ''
     *
     * @var string default empty string
     */
    public static string $action_type = '';

    /**
     * TG ID чата
     *
     * @var float
     */
    public static float $chat_id;

    /**
     * Храним тип чата
     *
     * @var string  default empty string
     */
    public static string $chat_type = '';

    /**
     * TG ID сообщения внутри канала группы
     *
     * @var int
     */
    public static int $message_id;

    /**
     * Приватный ли чат 
     *
     * @var string по-умолчанию "private", иначе - "public"
     */
    public static string $is_private_chat = 'private';

    /**
     * Храним username пользователя
     *
     * @var string
     */
    public static string $tg_user_username;

    /**
     * Храним имя пользователя
     *
     * @var string
     */
    public static string $tg_user_first_name;

    /**
     * Храним фамилию пользователя
     *
     * @var string
     */
    public static string $tg_user_last_name;

    /**
     * Храним TG-ID пользователя
     *
     * @var float
     */
    public static float $tg_user_id;

    /**
     * ID пользователя в WP DB на основе TG ID
     *
     * @var integer
     */
    public static int $wp_user_id;

    /**
     * Обращение к пользователю
     *
     * @var string
     */
    public static string $tg_user_appeal;

    /**
     * Хранит данные о типе пользователя, кто инициировал
     *
     * @var string  default empty string
     */
    public static string $initiator_type = '';

    /**
     * Тип пользователя (сотрудник компании или клиент)
     *
     * @var string
     */
    public static string $user_type = '';

    /**
     * ID оновления
     *
     * @var string
     */
    public static string $update_id = '';

    /**
     * В данном боте играет роль ID тикета
     *
     * @var integer
     */
    public static int $post_id;

    /**
     * 
     */
    public function run()
    {
        self::$tg_raw = file_get_contents('php://input');

        self::$update_id = self::$tg_raw['update_id'];

        self::check_secret();
        self::allowed_actions();
        self::clear_message();
        self::define_chat_data();
        self::is_user_object_exists();

        if (self::$user_object != false) :
            self::is_our_bot();
            self::define_initiator();
            self::set_tg_user_data();
            if (self::$user_object['is_bot'] == false) :
                self::check_user_in_db();
            else :
                exit;
            endif;
        endif;
    }

    /**
     * Проверка секретного слова из УРЛ
     *
     * @return void
     */
    private static function check_secret()
    {
        if ($_GET[Config::get__hook_secret_key_name()] != Config::get__bot_tg_url_hook_secret_key()) {
            exit('Secret key is wrong!');
        }
    }

    /**
     * Проверим, разрешено ли запрошенное действие и установим его
     *
     * @return void
     */
    private static function allowed_actions()
    {
        $is_action_allowed = false;

        foreach (Config::get__bot_allowed_actions() as $v) :
            if (isset(self::$tg_raw[$v])) :
                $is_action_allowed = true;
                self::$action_type = $v;
            endif;
        endforeach;

        if ($is_action_allowed == false) :
            exit();
        endif;
    }

    /**
     * Если текстовое, то очистим от вероятных тегов
     *
     * @return void
     */
    private static function clear_message()
    {
        if (isset(self::$tg_raw['message']['text'])) :
            self::$tg_raw['message']['text'] = trim(strip_tags(self::$tg_raw['message']['text']));
        endif;
    }

    /**
     * Определим данные чата
     *
     * @return void
     */
    private static function define_chat_data()
    {
        // TODO: что-то бред какой-то написан тут
        if (!isset(self::$tg_raw[self::$action_type]['message'])) :
            self::$chat_type = self::$tg_raw[self::$action_type]['chat']['type'];
            self::$chat_id = self::$tg_raw[self::$action_type]['chat']['id'];
        else :
            self::$chat_type = self::$tg_raw[self::$action_type]['message']['chat']['type'];
            self::$chat_id = self::$tg_raw[self::$action_type]['message']['chat']['id'];
            self::$message_id = (int)self::$tg_raw[self::$action_type]['message']['message_id'];
        endif;

        if (self::$chat_type != 'private') :
            self::$is_private_chat = 'public';
        endif;
    }

    /**
     * Проверим, есть ли в запросе данные о пользователе
     *
     * @return mixed
     */
    private static function is_user_object_exists()
    {
        foreach (self::$tg_raw as $k => $v) :
            if (isset($v['from'])) :
                self::$user_object = $v['from'];
                break;
            endif;
        endforeach;
    }

    /**
     * Проверим, что это сообщение, но не от нашего бота и не от автоматизации чата
     *
     * @return void
     */
    private static function is_our_bot()
    {
        if (self::$user_object['is_bot'] == true) :
            if (self::$user_object['id'] == Config::get__bot_tg_id() || self::$user_object['username'] == 'GroupAnonymousBot') :
                exit();
            endif;
        endif;
    }

    /**
     * Определим от какого типа пользователя запрос
     *
     * @return void
     */
    private static function define_initiator()
    {
        $chat_admins = [];

        if (self::$is_private_chat == 'public') :
            $chat_admins = TG::getChatAdministrators(self::$chat_id);
        endif;

        // Попробуем установить, что запрос от одного из админов
        if (isset($chat_admins['result'])) :
            foreach ($chat_admins as $user) {
                if (isset($user['id']) && $user['id'] == self::$user_object['id'] && $user['status'] != 'creator') :
                    self::$initiator_type = 'administrator';
                    break;
                endif;
            }
        endif;

        // Если не админ
        if (self::$initiator_type == false) :
            // Если запрос от владельца
            if (self::$user_object['id'] == Config::get__bot_developer_tg_id()) :
                self::$initiator_type = 'creator';
            // Если запрос от чужого бота (нашего бота отсекаем в self::$is_our_bot)
            elseif (self::$user_object['is_bot'] == true) :
                self::$initiator_type = 'bot';
            // Если запрос обычного пользователя
            else :
                self::$initiator_type = 'member';
            endif;
        endif;
    }

    /**
     * Определим данные пользователя
     *
     * @return void
     */
    private static function set_tg_user_data()
    {
        self::$tg_user_id = self::$user_object['id'];
        self::$tg_user_first_name = self::$user_object['first_name'];
        self::$tg_user_username = self::$user_object['username'] ?? self::$tg_user_first_name;
        self::$tg_user_last_name = self::$user_object['last_name'] ?? self::$tg_user_first_name;
        self::$tg_user_appeal = self::$tg_user_first_name;
    }

    /**
     * Проверим, что пользователь есть в WP DB или создадим его
     *
     * @return void
     */
    public static function check_user_in_db()
    {
        if (self::$chat_type == 'private') :

            global $wpdb;
            $wp_user_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'tg_user_id' AND meta_value = %s",
                    self::$tg_user_id
                )
            );

            if ($wp_user_id == false) :

                $user_login = 'tg_id__' . self::$tg_user_id;
                $user_password = md5(self::$tg_user_id, uniqid(time(), true));

                $user_insert_result = wp_insert_user([
                    'user_login' => $user_login,
                    'user_pass' => $user_password,
                    'first_name' => self::$tg_user_first_name,
                    'last_name' => self::$tg_user_last_name,
                    'user_nicename' => self::$tg_user_username,
                    'display_name' => self::$tg_user_username,
                    'user_email' => '',
                    'role' => 'subscriber',
                ]);

                if (is_wp_error($user_insert_result)) :
                    $text = "<code>";
                    $text .= "Пользователь не определен и его аккаунт не может быть создан: ";
                    $text .= print_r($user_login, 1);
                    $text .= "</code>";
                    TG::sendMessage(Config::get__bot_owner_tg_id(), $text);
                    exit();
                endif;

                /**
                 * Установим ID из БД WP
                 */
                self::$wp_user_id = $user_insert_result;

                /**
                 * Создадим мета-поле с TG-ID пользователя
                 */
                update_user_meta(self::$wp_user_id, Config::get__db_key__tg_user_id(), self::$tg_user_id);

                /**
                 * Создадим поле под запоминание последних действий пользователя
                 */
                update_user_meta(self::$wp_user_id, Config::get__db_key__tg_user_actions(), ['start']);

                /**
                 * Создадим поле под время первого контакта с ботом
                 */
                update_user_meta(self::$wp_user_id, Config::get__db_key__tg_user_first_contact_time(), time());

            else : // Если пользователь уже существует в WP BD

                /**
                 * Установим ID из BD WP
                 */
                self::$wp_user_id = $wp_user_id;

            endif;

        else : // Если не из привата с ботом

            exit;

        endif;
    }
}
