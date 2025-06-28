<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Livro extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome',
        'autor',
        'numero_registro',
        'situacao',
        'genero_id',
    ];

    protected $dates = ['deleted_at'];

    const SITUACAO_DISPONIVEL = 'disponivel';
    const SITUACAO_EMPRESTADO = 'emprestado';

    /**
     * Relacionamento com gênero
     */
    public function genero()
    {
        return $this->belongsTo(Genero::class);
    }

    /**
     * Relacionamento com empréstimos
     */
    public function emprestimos()
    {
        return $this->hasMany(Emprestimo::class);
    }

    /**
     * Empréstimo ativo atual
     */
    public function emprestimoAtivo()
    {
        return $this->hasOne(Emprestimo::class)->where('status', '!=', 'devolvido');
    }

    /**
     * Scope para livros disponíveis
     */
    public function scopeDisponiveis($query)
    {
        return $query->where('situacao', self::SITUACAO_DISPONIVEL);
    }

    /**
     * Scope para livros emprestados
     */
    public function scopeEmprestados($query)
    {
        return $query->where('situacao', self::SITUACAO_EMPRESTADO);
    }

    /**
     * Regras de validação
     */
    public static function rules($id = null)
    {
        return [
            'nome' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'numero_registro' => 'required|string|unique:livros,numero_registro,' . $id,
            'situacao' => 'required|in:disponivel,emprestado',
            'genero_id' => 'required|exists:generos,id',
        ];
    }
}

