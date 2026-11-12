<?php

namespace App\Services;

class CheckoutSkin
{
    public static function all(): array
    {
        return config('checkout.skins', []);
    }

    public static function exists($id): bool
    {
        return is_numeric($id) && isset(self::all()[(int) $id]);
    }

    public static function id(): int
    {
        $id = session('checkout_skin');
        if (self::exists($id)) {
            return (int) $id;
        }

        $default = (int) config('checkout.default_skin', 1);

        return self::exists($default) ? $default : 1;
    }

    public static function flags(): array
    {
        return self::all()[self::id()]['flags'] ?? [];
    }

    public static function has(string $flag): bool
    {
        return in_array($flag, self::flags(), true);
    }

    /**
     * CSS-классы для корневого элемента чекаута: ck-skin-3 ck-f-multistep ...
     */
    public static function cssClasses(): string
    {
        $classes = ['ck-skin-' . self::id()];
        foreach (self::flags() as $flag) {
            $classes[] = 'ck-f-' . $flag;
        }

        return implode(' ', $classes);
    }
}
