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

        if (config('checkout.mode') === 'random') {
            $pool   = self::randomPool();
            $random = session('checkout_skin_random');
            if (!in_array((int) $random, $pool, true)) {
                $random = $pool[array_rand($pool)];
                session(['checkout_skin_random' => $random]);
            }

            return (int) $random;
        }

        $default = (int) config('checkout.default_skin', 1);

        return self::exists($default) ? $default : 1;
    }

    public static function randomPool(): array
    {
        $pool = array_map('trim', explode(',', (string) config('checkout.random_skins', '')));
        $pool = array_values(array_unique(array_map('intval', array_filter($pool, [self::class, 'exists']))));

        return $pool ?: array_map('intval', array_keys(self::all()));
    }

    public static function flags(): array
    {
        return self::all()[self::id()]['flags'] ?? [];
    }

    public static function has(string $flag): bool
    {
        return in_array($flag, self::flags(), true);
    }

    public static function cssClasses(): string
    {
        $classes = ['ck-skin-' . self::id()];
        foreach (self::flags() as $flag) {
            $classes[] = 'ck-f-' . $flag;
        }

        return implode(' ', $classes);
    }
}
