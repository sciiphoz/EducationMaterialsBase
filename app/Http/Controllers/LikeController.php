<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    // Добавление/удаление лайка
    public function toggle($materialId)
    {
        $material = Material::findOrFail($materialId);

        // Проверка доступа для приватных материалов
        if ($material->isPrivate && $material->id_user !== Auth::id()) {
            abort(403, 'Доступ запрещен');
        }

        $like = Like::where('id_material', $materialId)
            ->where('id_user', Auth::id())
            ->first();

        if ($like) {
            $like->delete();
            $action = 'removed';
        } else {
            Like::create([
                'id_material' => $materialId,
                'id_user' => Auth::id()
            ]);
            $action = 'added';
        }

        $likesCount = $material->likes()->count();

        return response()->json([
            'action' => $action,
            'likes_count' => $likesCount
        ]);
    }
}