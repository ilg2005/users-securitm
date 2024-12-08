<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Отображает список пользователей с пагинацией.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $sortField = $request->input('sort_field', 'id');
        $sortOrder = $request->input('sort_order', 'asc');
        $search = $request->input('search', '');

        // Разрешенные для сортировки
        $allowedSortFields = ['id', 'name'];

        // Проверка валидности поля сортировки
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'id';
        }

        $usersQuery = User::query()->orderBy($sortField, $sortOrder);

        if ($search) {
            $usersQuery->where('name', 'like', '%' . $search . '%');
        }

        $users = $usersQuery->paginate(10)->appends($request->all());

        return Inertia::render('Home', [
            'users' => $users,
            'sort' => [
                'field' => $sortField,
                'order' => $sortOrder
            ],
            'search' => $search
        ]);
    }

    /**
     * Показать форму редактирования пользователя.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        return Inertia::render('Home', [
            'users' => User::paginate(10), // Перезагружаем пользователей
            'userToEdit' => $user, // Передаем пользователя для редактирования
            'sort' => [
                'field' => 'id',
                'order' => 'asc'
            ],
            'search' => ''
        ]);
    }

    /**
     * Обновить данные пользователя.
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('Update request:', $request->all());
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'ip' => 'nullable|ip',
                'comment' => 'nullable|string',
                'password' => 'nullable|string|min:6',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('error', 'Ошибка валидации данных')
                    ->withErrors($validator);
            }

            $user->name = $request->input('name');
            $user->ip = $request->input('ip');
            $user->comment = $request->input('comment');

            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
            }

            $user->save();

            return redirect()->route('users.index')
                ->with('success', 'Пользователь успешно обновлен.');

        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Произошла ошибка при обновлении пользователя');
        }
    }

    /**
     * Создать нового пользователя.
     */
    public function store(Request $request)
    {
        try {
            Log::info('Store request:', $request->all());

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'ip' => 'nullable|ip',
                'comment' => 'nullable|string',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('error', 'Ошибка валидации данных')
                    ->withErrors($validator)
                    ->withInput();
            }

            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'ip' => $request->input('ip'),
                'comment' => $request->input('comment'),
                'password' => Hash::make($request->input('password')),
            ]);

            return redirect()->route('users.index')
                ->with('success', 'Пользователь успешно добавлен.');

        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Произошла ошибка при добавлении пользователя');
        }
    }

    /**
     * Удалить пользователя.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return redirect()->route('users.index')
                ->with('success', 'Пользователь успешно удален.');

        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Произошла ошибка при удалении пользователя');
        }
    }
}