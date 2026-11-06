<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoDuvida extends Model
{
    public const STATUS = [
        'aberta' => 'Aberta',
        'em_andamento' => 'Em andamento',
        'respondida' => 'Respondida',
        'fechada' => 'Fechada',
    ];

    protected $table = 'pedido_duvidas';

    protected $fillable = [
        'user_id',
        'pedido_id',
        'mensagem',
        'status',
        'resposta',
        'lida_em',
        'respondida_em',
    ];

    protected $casts = [
        'lida_em' => 'datetime',
        'respondida_em' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? ucfirst($this->status);
    }
}
