<?php


namespace App\Services;

class AdvancedSqlInjectionChecker
{
    /**
     * Проверяет, содержит ли строка потенциально опасные SQL-инъекции.
     *
     * @param string $input
     * @return bool
     */
    public static function hasSqlInjection(string $input): bool
    {
        $flag = false;
        // Набор шаблонов для обнаружения SQL-инъекций
        // $patterns = [
        //     '/(?:\b(select|union|insert|update|delete|drop|alter|create|truncate)\b)/i', // Ключевые слова SQL
        //     '/(?:--|\#|\;)/', // Комментарии и точка с запятой
        //     '/(?:\b(and|or|xor|not)\b\s+[\w\s]+\s*(=|like|>|<|in|is|between)\s+[\w\s]+)/i', // Условные операторы
        //     '/(?:\b(?:exec|execute|sp_executesql|xp_cmdshell)\b)/i', // Команды выполнения
        //     '/(?:\b(select|union)[\s\S]+(from|join|into|load_file|information_schema|mysql)\b)/i', // Комбинированные шаблоны
        // ];

        $patterns = [
            '/\b(select|union|insert|update|delete|drop|alter|create|truncate)\b.*?\b(from|into|set|values|where|join|table)?\b/i',
            // '/\b(and|or|xor|not)\b\s+[^\n]*?\s*(=|like|>|<|in|is|between)\s+[^\n]*/i',
            '/\b(and|or|xor|not)\b.{0,120}?(=|like|>|<|!=|<>).{0,120}/i',
            '/\b(and|or|xor|not)\b.{0,120}?\bin\s*\(/i',
            '/\b(and|or|xor|not)\b.{0,120}?\bis\s+(not\s+)?null\b/i',
            '/\b(exec|execute|sp_executesql|xp_cmdshell)\b/i',
            '/(\b(select|update|delete|insert|drop|exec|union)\b.*(;|\-\-|\#))/i', // опасные завершения строк
        ];

        foreach ($patterns as $i => $pattern) {
            if (preg_match($pattern, $input, $m)) {
                // var_dump("Matched #$i: $pattern\n");
                // var_dump("Fragment: " . $m[0] . "\n\n");
                return true;
            }
        }

        // Дополнительная проверка на символы, которые часто используются в инъекциях
        // $specialChars = ['\'', '"', ';', '\\', '--', '#'];

        // foreach ($specialChars as $char) {
        //     if (str_contains($input, $char)) {
        //         // Игнорируем символы внутри строковых литералов
        //         if (!preg_match('/(["\']).*?' . preg_quote($char, '/') . '.*?\1/', $input)) {
        //             return true; // Обнаружена SQL-инъекция
        //         }
        //     }
        // }

        // Проверка на специальные символы только в SQL-подобном контексте
        // (например, символ `'`, если не в JavaScript или HTML теге)
        // $sqlChars = ["'", '"', ";", "--", "#"];
        $sqlChars = [";", "--", "#"];
        foreach ($sqlChars as $char) {
            // Проверим, не в HTML/JS ли контексте это
            if (str_contains($input, $char)) {
                // Если символ внутри <script> или "<...>", то безопасно
                if (!preg_match('/<script.*?>.*?' . preg_quote($char, '/') . '.*?<\/script>/is', $input) &&
                    !preg_match('/<[^>]+' . preg_quote($char, '/') . '[^>]*>/i', $input)) {
                    return true;
                }
            }
        }

        return $flag;
    }
}