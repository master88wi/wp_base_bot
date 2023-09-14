<?php

namespace dev_bots_ru\tg\Common;

class Queries
{
    /**
     * Выделим из запроса (с кнопки или команды боту) команду и вспомогательные данные
     * Ожидаем, что строка запроса выглядит так: 
     * some_command?param1=value1&param2=value2
     * 
     * На выходе получим массив, например:
     * - command => post
     * - action => edit
     * - id => 000
     * 
     * Ключ command узнается везде в коде и является указанием 
     * на то, какой файл подключать, какой экран запрашивать.
     *
     * @param string $query
     * @return array
     */
    public static function actions__string_to_array(string $query): array
    {
        $parameters = [];

        // Определим команду и вспомогательные данные
        $has_matches = preg_match("/^([^\?\s]+)\s*\??(.*)?/iu", $query, $matches);

        // Если что-то начинается как команда, выглядит как команда, 
        // но не подходит под регулярку, то выдаем ошибку
        if ($has_matches == false) :
            return $parameters;
        endif;

        $parameters['command'] = $matches[1];
        $like_get = $matches[2] ?? '';

        if ($like_get != false) :
            $like_get_in_array = explode("&", $like_get);
            foreach ($like_get_in_array as $gets) :
                $raw_parameters = explode("=", $gets);
                $parameters[$raw_parameters[0]] = $raw_parameters[1];
            endforeach;
        endif;

        return $parameters;
    }

    /**
     * Соберем строку-подобие GET из массива
     *
     * @param array $array
     * @return string
     */
    public static function actions__array_to_string(array $array): string
    {
        $string = "";

        if ($array == false) :
            return $string;
        endif;

        $command = array_shift($array);

        $_string = "";
        $iter = 1;
        foreach ($array as $key => $value) :
            $_string .= $key . "=" . $value;
            if ($iter < count($array)) :
                $_string .= "&";
            endif;
        endforeach;

        if ($_string != false) :
            $string = $command . "?" . $_string;
        else :
            $string = $command;
        endif;

        return $string;
    }
}
