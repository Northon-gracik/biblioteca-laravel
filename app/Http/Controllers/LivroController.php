<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class LivroController extends Controller
{
    /**
     * Listar todos os livros
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Livro::with(['genero', 'emprestimoAtivo.user']);

            // Filtros opcionais
            if ($request->has('genero_id')) {
                $query->where('genero_id', $request->genero_id);
            }

            if ($request->has('situacao')) {
                $query->where('situacao', $request->situacao);
            }

            if ($request->has('autor')) {
                $query->where('autor', 'like', '%' . $request->autor . '%');
            }

            $livros = $query->get();
            
            return response()->json([
                'success' => true,
                'data' => $livros,
                'message' => 'Livros listados com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar livros: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar apenas livros disponíveis
     */
    public function disponiveis(): JsonResponse
    {
        try {
            $livros = Livro::disponiveis()->with('genero')->get();
            
            return response()->json([
                'success' => true,
                'data' => $livros,
                'message' => 'Livros disponíveis listados com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar livros disponíveis: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Criar novo livro
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate(Livro::rules());

            $livro = Livro::create($request->all());
            $livro->load('genero');

            return response()->json([
                'success' => true,
                'data' => $livro,
                'message' => 'Livro criado com sucesso'
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
                'message' => 'Erro ao criar livro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exibir livro específico
     */
    public function show($id): JsonResponse
    {
        try {
            $livro = Livro::with(['genero', 'emprestimos.user', 'emprestimoAtivo.user'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $livro,
                'message' => 'Livro encontrado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Livro não encontrado'
            ], 404);
        }
    }

    /**
     * Atualizar livro
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $livro = Livro::findOrFail($id);
            $request->validate(Livro::rules($id));

            $livro->update($request->all());
            $livro->load('genero');

            return response()->json([
                'success' => true,
                'data' => $livro,
                'message' => 'Livro atualizado com sucesso'
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
                'message' => 'Erro ao atualizar livro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Excluir livro
     */
    public function destroy($id): JsonResponse
    {
        try {
            $livro = Livro::findOrFail($id);

            // Verificar se o livro possui empréstimos ativos
            if ($livro->emprestimoAtivo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível excluir livro que está emprestado'
                ], 400);
            }

            $livro->delete();

            return response()->json([
                'success' => true,
                'message' => 'Livro excluído com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir livro: ' . $e->getMessage()
            ], 500);
        }
    }
}

