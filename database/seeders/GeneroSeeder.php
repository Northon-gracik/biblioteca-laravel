<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Genero;

class GeneroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $generos = [
            [
                'nome' => 'Ficção',
                'descricao' => 'Livros de ficção literária e narrativas imaginárias'
            ],
            [
                'nome' => 'Romance',
                'descricao' => 'Livros de romance e relacionamentos'
            ],
            [
                'nome' => 'Fantasia',
                'descricao' => 'Livros de fantasia, magia e mundos imaginários'
            ],
            [
                'nome' => 'Aventura',
                'descricao' => 'Livros de aventura e ação'
            ],
            [
                'nome' => 'Mistério',
                'descricao' => 'Livros de mistério, suspense e investigação'
            ],
            [
                'nome' => 'Biografia',
                'descricao' => 'Biografias e histórias de vida'
            ],
            [
                'nome' => 'História',
                'descricao' => 'Livros históricos e documentários'
            ],
            [
                'nome' => 'Ciência',
                'descricao' => 'Livros científicos e educacionais'
            ],
            [
                'nome' => 'Autoajuda',
                'descricao' => 'Livros de desenvolvimento pessoal e autoajuda'
            ],
            [
                'nome' => 'Tecnologia',
                'descricao' => 'Livros sobre tecnologia, programação e inovação'
            ]
        ];

        foreach ($generos as $genero) {
            Genero::create($genero);
        }
    }
}

