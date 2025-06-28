<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Emprestimo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'livro_id',
        'data_emprestimo',
        'data_devolucao_prevista',
        'data_devolucao_real',
        'status',
        'observacoes',
    ];

    protected $dates = [
        'data_emprestimo',
        'data_devolucao_prevista',
        'data_devolucao_real',
        'deleted_at'
    ];

    const STATUS_ATIVO = 'ativo';
    const STATUS_DEVOLVIDO = 'devolvido';
    const STATUS_ATRASADO = 'atrasado';

    /**
     * Relacionamento com usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com livro
     */
    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }

    /**
     * Verifica se o empréstimo está atrasado
     */
    public function isAtrasado()
    {
        return $this->status !== self::STATUS_DEVOLVIDO && 
               $this->data_devolucao_prevista < Carbon::now();
    }

    /**
     * Scope para empréstimos ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('status', self::STATUS_ATIVO);
    }

    /**
     * Scope para empréstimos atrasados
     */
    public function scopeAtrasados($query)
    {
        return $query->where('status', self::STATUS_ATRASADO)
                    ->orWhere(function($q) {
                        $q->where('status', self::STATUS_ATIVO)
                          ->where('data_devolucao_prevista', '<', Carbon::now());
                    });
    }

    /**
     * Scope para empréstimos devolvidos
     */
    public function scopeDevolvidos($query)
    {
        return $query->where('status', self::STATUS_DEVOLVIDO);
    }

    /**
     * Regras de validação
     */
    public static function rules($id = null)
    {
        return [
            'user_id' => 'required|exists:users,id',
            'livro_id' => 'required|exists:livros,id',
            'data_emprestimo' => 'required|date',
            'data_devolucao_prevista' => 'required|date|after:data_emprestimo',
            'data_devolucao_real' => 'nullable|date',
            'status' => 'required|in:ativo,devolvido,atrasado',
            'observacoes' => 'nullable|string|max:500',
        ];
    }
}

