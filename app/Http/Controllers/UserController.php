<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Listar todos os usuários
     */
    public function index(): JsonResponse
    {
        try {
            $users = User::with('emprestimosAtivos')->get();
            
            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => 'Usuários listados com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar usuários: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Criar novo usuário
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate(User::rules());

            $user = User::create($request->all());

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Usuário criado com sucesso'
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
                'message' => 'Erro ao criar usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exibir usuário específico
     */
    public function show($id): JsonResponse
    {
        try {
            $user = User::with(['emprestimos.livro', 'emprestimosAtivos.livro'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Usuário encontrado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ], 404);
        }
    }

    /**
     * Atualizar usuário
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $request->validate(User::rules($id));

            $user->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Usuário atualizado com sucesso'
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
                'message' => 'Erro ao atualizar usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Excluir usuário
     */
    public function destroy($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            // Verificar se o usuário possui empréstimos ativos
            if ($user->emprestimosAtivos()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível excluir usuário com empréstimos ativos'
                ], 400);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuário excluído com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir usuário: ' . $e->getMessage()
            ], 500);
        }
    }
}

