<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genero extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome',
        'descricao',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Relacionamento com livros
     */
    public function livros()
    {
        return $this->hasMany(Livro::class);
    }

    /**
     * Regras de validação
     */
    public static function rules($id = null)
    {
        return [
            'nome' => 'required|string|max:255|unique:generos,nome,' . $id,
            'descricao' => 'nullable|string|max:500',
        ];
    }
}

