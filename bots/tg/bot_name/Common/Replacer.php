<?php

namespace dev_bots_ru\tg\Common;

use dev_bots_ru\tg\General\Parser;

class Replacer
{
    /**
     * Замена псевдокода вида %%CODE%% на соответствующие текстовые данные.
     *
     * @param string $text
     * @return string
     */
    public static function replace_pseudo_code(string $text): string
    {
        $text = self::insert_user_name($text);
        $text = self::insert_user_tg_id($text);

        return $text;
    }

    /**
     * Вставим имя пользователя из Телеграма %%USER_NAME%%
     *
     * @param string $text
     * @return string
     */
    private static function insert_user_name(string $text): string
    {
        return str_replace('%%USER_NAME%%', Parser::$tg_user_appeal, $text);
    }

    /**
     * Вставим ID пользователя %%USER_TG_ID%%
     *
     * @param string $text
     * @return string
     */
    private static function insert_user_tg_id(string $text): string
    {
        return str_replace('%%USER_TG_ID%%', Parser::$tg_user_id, $text);
    }
}
