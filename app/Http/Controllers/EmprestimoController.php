<?php

namespace App\Http\Controllers;

use App\Models\Emprestimo;
use App\Models\Livro;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class EmprestimoController extends Controller
{
    /**
     * Listar todos os empréstimos
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Emprestimo::with(['user', 'livro.genero']);

            // Filtros opcionais
            if ($request->has('status')) {
                if ($request->status === 'atrasados') {
                    $query->atrasados();
                } else {
                    $query->where('status', $request->status);
                }
            }

            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            $emprestimos = $query->orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'success' => true,
                'data' => $emprestimos,
                'message' => 'Empréstimos listados com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar empréstimos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar empréstimos atrasados
     */
    public function atrasados(): JsonResponse
    {
        try {
            $emprestimos = Emprestimo::atrasados()->with(['user', 'livro.genero'])->get();
            
            return response()->json([
                'success' => true,
                'data' => $emprestimos,
                'message' => 'Empréstimos atrasados listados com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar empréstimos atrasados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Criar novo empréstimo
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate(Emprestimo::rules());

            // Verificar se o livro está disponível
            $livro = Livro::findOrFail($request->livro_id);
            if ($livro->situacao !== Livro::SITUACAO_DISPONIVEL) {
                return response()->json([
                    'success' => false,
                    'message' => 'Livro não está disponível para empréstimo'
                ], 400);
            }

            // Criar o empréstimo
            $emprestimo = Emprestimo::create([
                'user_id' => $request->user_id,
                'livro_id' => $request->livro_id,
                'data_emprestimo' => $request->data_emprestimo ?? Carbon::now(),
                'data_devolucao_prevista' => $request->data_devolucao_prevista,
                'status' => Emprestimo::STATUS_ATIVO,
                'observacoes' => $request->observacoes,
            ]);

            // Atualizar situação do livro
            $livro->update(['situacao' => Livro::SITUACAO_EMPRESTADO]);

            $emprestimo->load(['user', 'livro.genero']);

            return response()->json([
                'success' => true,
                'data' => $emprestimo,
                'message' => 'Empréstimo criado com sucesso'
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
                'message' => 'Erro ao criar empréstimo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exibir empréstimo específico
     */
    public function show($id): JsonResponse
    {
        try {
            $emprestimo = Emprestimo::with(['user', 'livro.genero'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $emprestimo,
                'message' => 'Empréstimo encontrado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Empréstimo não encontrado'
            ], 404);
        }
    }

    /**
     * Atualizar empréstimo
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $emprestimo = Emprestimo::findOrFail($id);
            $request->validate(Emprestimo::rules($id));

            $emprestimo->update($request->all());
            $emprestimo->load(['user', 'livro.genero']);

            return response()->json([
                'success' => true,
                'data' => $emprestimo,
                'message' => 'Empréstimo atualizado com sucesso'
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
                'message' => 'Erro ao atualizar empréstimo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar empréstimo como devolvido
     */
    public function devolver($id): JsonResponse
    {
        try {
            $emprestimo = Emprestimo::findOrFail($id);

            if ($emprestimo->status === Emprestimo::STATUS_DEVOLVIDO) {
                return response()->json([
                    'success' => false,
                    'message' => 'Empréstimo já foi devolvido'
                ], 400);
            }

            // Atualizar empréstimo
            $emprestimo->update([
                'status' => Emprestimo::STATUS_DEVOLVIDO,
                'data_devolucao_real' => Carbon::now(),
            ]);

            // Atualizar situação do livro
            $emprestimo->livro->update(['situacao' => Livro::SITUACAO_DISPONIVEL]);

            $emprestimo->load(['user', 'livro.genero']);

            return response()->json([
                'success' => true,
                'data' => $emprestimo,
                'message' => 'Livro devolvido com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao devolver livro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar empréstimo como atrasado
     */
    public function marcarAtrasado($id): JsonResponse
    {
        try {
            $emprestimo = Emprestimo::findOrFail($id);

            if ($emprestimo->status === Emprestimo::STATUS_DEVOLVIDO) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível marcar como atrasado um empréstimo já devolvido'
                ], 400);
            }

            $emprestimo->update(['status' => Emprestimo::STATUS_ATRASADO]);
            $emprestimo->load(['user', 'livro.genero']);

            return response()->json([
                'success' => true,
                'data' => $emprestimo,
                'message' => 'Empréstimo marcado como atrasado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao marcar empréstimo como atrasado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Excluir empréstimo
     */
    public function destroy($id): JsonResponse
    {
        try {
            $emprestimo = Emprestimo::findOrFail($id);

            // Se o empréstimo não foi devolvido, liberar o livro
            if ($emprestimo->status !== Emprestimo::STATUS_DEVOLVIDO) {
                $emprestimo->livro->update(['situacao' => Livro::SITUACAO_DISPONIVEL]);
            }

            $emprestimo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Empréstimo excluído com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir empréstimo: ' . $e->getMessage()
            ], 500);
        }
    }
}

