<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'pergunta',
        'resposta',
        'categoria',
        'ordem',
        'ativo',
    ];

    protected $casts = [
        'ordem' => 'integer',
        'ativo' => 'boolean',
    ];

    public function scopeAtivas(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }

    public function scopeOrdenadas(Builder $query): Builder
    {
        return $query->orderBy('ordem')->orderBy('id');
    }
}
