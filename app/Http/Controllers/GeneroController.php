<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class GeneroController extends Controller
{
    /**
     * Listar todos os gêneros
     */
    public function index(): JsonResponse
    {
        try {
            $generos = Genero::withCount('livros')->get();
            
            return response()->json([
                'success' => true,
                'data' => $generos,
                'message' => 'Gêneros listados com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar gêneros: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Criar novo gênero
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate(Genero::rules());

            $genero = Genero::create($request->all());

            return response()->json([
                'success' => true,
                'data' => $genero,
                'message' => 'Gênero criado com sucesso'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar gênero: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exibir gênero específico
     */
    public function show($id): JsonResponse
    {
        try {
            $genero = Genero::with('livros')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $genero,
                'message' => 'Gênero encontrado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gênero não encontrado'
            ], 404);
        }
    }

    /**
     * Atualizar gênero
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $genero = Genero::findOrFail($id);
            $request->validate(Genero::rules($id));

            $genero->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $genero,
                'message' => 'Gênero atualizado com sucesso'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar gênero: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Excluir gênero
     */
    public function destroy($id): JsonResponse
    {
        try {
            $genero = Genero::findOrFail($id);

            // Verificar se o gênero possui livros
            if ($genero->livros()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível excluir gênero que possui livros cadastrados'
                ], 400);
            }

            $genero->delete();

            return response()->json([
                'success' => true,
                'message' => 'Gênero excluído com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir gênero: ' . $e->getMessage()
            ], 500);
        }
    }
}

