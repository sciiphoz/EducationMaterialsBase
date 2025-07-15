<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    // Просмотр всех публичных материалов
    public function index()
    {
        $materials = Material::where('isPrivate', false)
            ->with(['user', 'tags', 'sections'])
            ->orderBy('date', 'desc')
            ->paginate(10);

        return response()->json($materials);
    }

    // Создание нового материала
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'isPrivate' => 'boolean',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'sections' => 'required|array|min:1'
        ]);

        $material = Material::create([
            'name' => $validated['name'],
            'isPrivate' => $validated['isPrivate'] ?? false,
            'id_user' => Auth::id(),
            'date' => now()
        ]);

        if (isset($validated['tags'])) {
            $material->tags()->attach($validated['tags']);
        }

        foreach ($validated['sections'] as $sectionData) {
            $material->sections()->create($sectionData);
        }

        return response()->json($material->load('tags', 'sections'), 201);
    }

    // Просмотр конкретного материала
    public function show($id)
    {
        $material = Material::with(['user', 'tags', 'sections', 'comments.user'])
            ->findOrFail($id);

        // Проверка доступа для приватных материалов
        if ($material->isPrivate && $material->id_user !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Доступ запрещен');
        }

        return response()->json($material);
    }

    // Обновление материала
    public function update(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        // Проверка прав на редактирование
        if ($material->id_user !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Недостаточно прав');
        }

        $validated = $request->validate([
            'name' => 'string|max:255',
            'isPrivate' => 'boolean',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id'
        ]);

        $material->update($validated);

        if (isset($validated['tags'])) {
            $material->tags()->sync($validated['tags']);
        }

        return response()->json($material->fresh()->load('tags', 'sections'));
    }

    // Удаление материала
    public function destroy($id)
    {
        $material = Material::findOrFail($id);

        // Только автор или админ может удалить
        if ($material->id_user !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Недостаточно прав');
        }

        $material->delete();
        return response()->json(null, 204);
    }

    // Поиск материалов по тегам
    public function searchByTag($tagId)
    {
        $tag = Tag::findOrFail($tagId);
        $materials = $tag->materials()
            ->where(function($query) {
                $query->where('isPrivate', false)
                    ->orWhere('id_user', Auth::id());
            })
            ->with(['user', 'tags'])
            ->paginate(10);

        return response()->json($materials);
    }
}