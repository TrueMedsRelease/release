<?php

namespace Mews\Captcha;

use Exception;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Hashing\BcryptHasher as Hasher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Intervention\Image\Gd\Font;
use Intervention\Image\Geometry\Factories\LineFactory;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Illuminate\Session\Store as Session;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Response;


class Captcha
{
    protected function text(): void
    {
        $marginTop = $this->image->height() / $this->length;
        if ($this->marginTop !== 0) {
            $marginTop = $this->marginTop;
        }

        $text = $this->text;
        if (is_string($text)) {
            $text = str_split($text);
        }

        foreach ($text as $key => $char) {
            $marginLeft = $this->textLeftPadding + ($key * ($this->image->width() - $this->textLeftPadding) / $this->length);

            $this->image->text($char, $marginLeft, $marginTop, function ($font) {
                /* @var Font $font */
                $font->file($this->font());
                $font->size($this->fontSize());
                $font->color($this->fontColor());
                $font->align('left');
                $font->valign('top');
                $font->angle($this->angle());
            });
        }
    }

    public function check(string $value): bool
    {
        if (!$this->session->has('captcha')) {
            return false;
        }

        $key = $this->session->get('captcha.key');
        $sensitive = $this->session->get('captcha.sensitive');
        $encrypt = $this->session->get('captcha.encrypt');

        if (!Cache::pull($this->get_cache_key($key))) {
            $this->session->remove('captcha');
            return false;
        }

        if (!$sensitive) {
            $value = $this->str->lower($value);
        }

        if($encrypt) $key = Crypt::decrypt($key);
        $check = $this->hasher->check($value, $key);
        // if verify pass,remove session
        if ($check) {
            $this->session->remove('captcha');
        }

        return $check;
    }
}