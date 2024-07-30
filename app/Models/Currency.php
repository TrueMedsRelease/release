<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    public static $prefix = [
        'usd' => '$',
        'eur' => '€',
        'aud' => 'A$',
        'gbp' => '£',
        'jpy' => '¥',
        'cny' => '圓',
        'cad' => 'C$',
        'hkd' => 'HK$',
        'aed' => 'د.إ',
        'chf' => '₣',
        'pln' => 'zł',
        'czk' => 'Kč',
        'bgn' => 'лв.',
        'huf' => 'Ft',
        'dkk' => 'kr.',
        'nok' => 'NKr',
        'sek' => 'kr',
        'ron' => 'L',
        'brl' => 'R$',
        'ars' => 'ARG$',
        'php' => '₱',
        'mxn' => 'MXN$'
    ];

    protected $table = 'currency';
}
