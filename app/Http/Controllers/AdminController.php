<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    // Просмотр всех материалов (включая приватные)
    public function getAllMaterials()
    {
        $materials = Material::with(['user', 'tags', 'sections'])
            ->orderBy('date', 'desc')
            ->paginate(20);

        return response()->json($materials);
    }

    // Редактирование любого материала
    public function updateMaterial(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'isPrivate' => 'boolean',
            'rating' => 'integer|min:0|max:5'
        ]);

        $material->update($validated);
        return response()->json($material);
    }

    // Удаление любого материала
    public function deleteMaterial($id)
    {
        $material = Material::findOrFail($id);
        $material->delete();
        return response()->json(null, 204);
    }

    // Одобрение материала (снятие приватности)
    public function approveMaterial($id)
    {
        $material = Material::findOrFail($id);
        $material->update(['isPrivate' => false]);
        return response()->json($material);
    }

    // Просмотр всех пользователей
    public function getAllUsers()
    {
        $users = User::with('role')->paginate(20);
        return response()->json($users);
    }
}