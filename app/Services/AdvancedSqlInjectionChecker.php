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
        $patterns = [
            '/(?:\b(select|union|insert|update|delete|drop|alter|create|truncate)\b)/i', // Ключевые слова SQL
            '/(?:--|\#|\;)/', // Комментарии и точка с запятой
            '/(?:\b(and|or|xor|not)\b\s+[\w\s]+\s*(=|like|>|<|in|is|between)\s+[\w\s]+)/i', // Условные операторы
            '/(?:\b(?:exec|execute|sp_executesql|xp_cmdshell)\b)/i', // Команды выполнения
            '/(?:\b(select|union)[\s\S]+(from|join|into|load_file|information_schema|mysql)\b)/i', // Комбинированные шаблоны
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                $flag = true;
            }
        }

        // Дополнительная проверка на символы, которые часто используются в инъекциях
        $specialChars = ['\'', '"', ';', '\\', '--', '#'];

        foreach ($specialChars as $char) {
           if (str_contains($input, $char)) {
                $flag = true;
           }
        }

        return $flag;
    }
}