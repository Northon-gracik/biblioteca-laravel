<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome',
        'email',
        'numero_cadastro',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Relacionamento com empréstimos
     */
    public function emprestimos()
    {
        return $this->hasMany(Emprestimo::class);
    }

    /**
     * Empréstimos ativos (não devolvidos)
     */
    public function emprestimosAtivos()
    {
        return $this->hasMany(Emprestimo::class)->where('status', '!=', 'devolvido');
    }

    /**
     * Validação de email único
     */
    public static function rules($id = null)
    {
        return [
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'numero_cadastro' => 'required|string|unique:users,numero_cadastro,' . $id,
        ];
    }
}

