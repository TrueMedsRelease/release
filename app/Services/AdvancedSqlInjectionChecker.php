<?php

namespace App\Services;

class AdvancedSqlInjectionChecker
{
    /**
     *
     * @param string $input
     * @return bool
     */
    public static function hasSqlInjection(string $input): bool
    {
        if (trim($input) === '') {
            return false;
        }


        $patterns = [
            // SELECT ... FROM (ищет select, за которым в пределах 200 символов идет from)
            '/\b(select\s+[\s\S]{1,200}?\s+from)\b/i',

            // INSERT INTO
            '/\b(insert\s+into)\b/i',

            // UPDATE ... SET
            '/\b(update\s+[\s\S]{1,100}?\s+set)\b/i',

            // DELETE FROM
            '/\b(delete\s+from)\b/i',

            // DROP TABLE / ALTER TABLE / CREATE TABLE
            '/\b(drop|alter|create)\s+table\b/i',

            // UNION SELECT
            '/\bunion\s+(all\s+)?select\b/i',

            // Команды выполнения (требуют открывающую скобку, чтобы не ловить слова в тексте)
            '/\b(exec|execute|sp_executesql|xp_cmdshell)\s*\(/i',

            // Классические логические инъекции (1=1, '1'='1')
            '/\b(or|and)\s+[\'"]?\d+[\'"]?\s*=\s*[\'"]?\d+[\'"]?/i',

            // Инъекции на основе времени (MySQL, SQL Server, PostgreSQL)
            '/\b(waitfor\s+delay|sleep|benchmark)\s*\(/i',

            // Опасные завершения строк (комментарии) СТРОГО в SQL-контексте
            '/\b(select|update|delete|insert|drop|exec|union)\b[\s\S]{0,100}?(--|\#|\/\*)/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }


        return false;
    }
}
