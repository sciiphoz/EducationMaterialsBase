<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // Добавление комментария
    public function store(Request $request, $materialId)
    {
        $material = Material::findOrFail($materialId);

        // Проверка доступа для приватных материалов
        if ($material->isPrivate && $material->id_user !== Auth::id()) {
            abort(403, 'Доступ запрещен');
        }

        $validated = $request->validate([
            'text' => 'required|string|max:1000'
        ]);

        $comment = Comment::create([
            'text' => $validated['text'],
            'id_user' => Auth::id(),
            'id_material' => $materialId
        ]);

        return response()->json($comment->load('user'), 201);
    }

    // Удаление комментария
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        // Только автор, автор материала или админ может удалить
        if ($comment->id_user !== Auth::id() 
            && $comment->material->id_user !== Auth::id()
            && !Auth::user()->isAdmin()) {
            abort(403, 'Недостаточно прав');
        }

        $comment->delete();
        return response()->json(null, 204);
    }
}