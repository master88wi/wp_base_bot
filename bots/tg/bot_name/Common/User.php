<?php

namespace dev_bots_ru\tg\Common;

use dev_bots_ru\tg\General\Config;
use dev_bots_ru\tg\General\Parser;

class User
{

    public static $user_parameters = [];

    /**
     * Запомним любой параметр пользователя, чтобы установить и забрать его в любой части кода.
     *
     * @param [type] $parameter_name
     * @param [type] $parameter_value
     * @return array
     */
    public static function set__special_parameters($parameter_name, $parameter_value): array
    {
        self::$user_parameters[$parameter_name] = $parameter_value;
        return self::$user_parameters;
    }

    /**
     * Очистка текущего действия пользователя
     *
     * @return void
     */
    public static function clear__current_user_action()
    {
        self::add__user_action(Queries::actions__array_to_string(['']));
    }

    /**
     * Установим (запомним) действие пользователя
     *
     * @param integer $step_number
     * @param string $action
     * @return void
     */
    public static function add__user_action(string $action)
    {
        $all_actions = get_user_meta(Parser::$wp_user_id, Config::get__db_key__tg_user_actions(), true);
        array_unshift($all_actions, $action);
        $all_actions = array_slice($all_actions, 0, Config::get__user_limit_actions_to_save(), false);
        update_user_meta(Parser::$wp_user_id, Config::get__db_key__tg_user_actions(), $all_actions);
    }

    /**
     * Получим действие пользователя текущее (0) или предыдущие (1, 2, 3...).
     * По умолчанию возвращает текущее действие.
     *
     * @param integer $step_number
     * @return string|array
     */
    public static function get__user_action(int $step_number = 0, $as_type = 'array')
    {
        $all_actions = get_user_meta(Parser::$wp_user_id, Config::get__db_key__tg_user_actions(), true) ?: ['start'];

        if ($all_actions != false) :
            if ($as_type == 'array') :
                $action = Queries::actions__string_to_array($all_actions[$step_number]);
            else :
                $action = $all_actions[$step_number];
            endif;
        endif;

        return $action;
    }

    /**
     * Получим ID тикета пользователя. На каждого пользователя может быть один уникальный тикет.
     * У пользователя не может отсутствовать тикет т.к. он создается при создании пользователя
     *
     * @return integer
     */
    public static function get__user_ticket_id(): int
    {
        return Parser::$post_id;
    }
}
