<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Возвращает список всех пользователей с пагинацией, поиском и сортировкой.
     */
    public function index(Request $request)
    {
       $query = User::query()->orderBy('id', 'asc');

        // Поиск по имени
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Сортировка по имени
        $sortOrder = $request->get('sort', 'asc');
        $query->orderBy('name', $sortOrder);

        $users = $query->paginate(10);

        return response()->json($users);
    }

    /**
     * Возвращает данные конкретного пользователя по ID.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        if (!$user) {
            return response()->json(['message' => 'Пользователь не найден'], 404);
        }

        return response()->json($user);
    }

    /**
     * Создание нового пользователя.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'ip' => 'nullable|ip',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'ip' => $request->ip,
            'comment' => $request->comment,
        ]);

        return response()->json($user, 201);
    }

    /**
     * Обновляет данные существующего пользователя по ID. Только name, ip, comment и password.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->only(['name', 'ip', 'comment']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json($user);
    }

    /**
     * Удаляет пользователя по ID.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (!$user) {
            return response()->json(['message' => 'Пользователь не найден'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Пользователь успешно удален']);
    }
}
