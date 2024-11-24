<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\UserAddress;
use App\Models\UserPhoneNumber; // Импортируем модель UserPhoneNumber
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerification;

class AuthController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Валидация для email, password, username, адреса и номера телефона
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                function ($attribute, $value, $fail) {
                    // Проверка на наличие хотя бы одной заглавной буквы
                    if (!preg_match('/[A-Z]/', $value)) {
                        $fail('Пароль должен содержать хотя бы одну заглавную букву.');
                    }
    
                    // Проверка на наличие хотя бы одного спецсимвола
                    if (!preg_match('/[\W_]/', $value)) {
                        $fail('Пароль должен содержать хотя бы один спецсимвол.');
                    }
                },
            ],
            'username' => 'required|string|max:255',
            'address_line' => 'required|string|max:255',
            'city' => ['required', 'string'],
            'phone' => ['required', 'string', 'max:19', 'unique:users_phone_number,number_1'],
            'user_status' => 'required|integer', // Добавляем валидацию для user_status
        ], [
            'email.required' => 'Поле Email обязательно для заполнения.',
            'email.email' => 'Поле Email должно быть действительным электронным адресом.',
            'email.unique' => 'Этот Email уже занят.',
            'password.required' => 'Поле Пароль обязательно для заполнения.',
            'password.min' => 'Пароль должен содержать не менее 8 символов.',
            'password.confirmed' => 'Пароли не совпадают.',
            'username.required' => 'Поле Имя пользователя обязательно для заполнения.',
            'address_line.required' => 'Поле Адрес обязательно для заполнения.',
            'city.required' => 'Поле Город обязательно для заполнения.',
            'phone.required' => 'Поле Телефон обязательно для заполнения.',
            'phone.unique' => 'Этот номер телефона уже занят.',
            'user_status.required' => 'Поле Статус пользователя обязательно для заполнения.',
        ]);
    
        // Создание пользователя
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'username' => $request->username,
            'city' => $request->city,
            'user_status' => $request->user_status, // Устанавливаем значение user_status
            'email_verified_at' => null, // По умолчанию email не подтвержден
        ]);
    
        // Создание записи в UserAddress
        UserAddress::create([
            'user_id' => $user->id,
            'address_line' => $request->address_line,
            'city' => $request->city,
        ]);
    
        // Создание записи в UserPhoneNumber
        UserPhoneNumber::create([
            'user_id' => $user->id,
            'number_1' => $request->phone,
        ]);
    
        // Авторизация пользователя
        Auth::login($user);
    
        // Перенаправление на страницу index
        return redirect()->route('adverts.index')->with('success', 'Вы успешно зарегистрировались и вошли в систему!');
    }


    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Валидация для email и password
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember'); // Получаем значение чекбокса "Запомнить меня"

        if (Auth::attempt($credentials, $remember)) {
            // Перенаправление на страницу профиля
            return redirect()->route('user.show', ['id' => Auth::id()])->with('success', 'Вы успешно вошли в систему!');
        }
    
        return back()->withErrors(['email' => 'Неверные учетные данные.']);
    }


    public function logout(Request $request)
{
    Auth::logout();
    
    // Перенаправление на главную страницу или другую страницу
    return redirect('/')->with('success', 'Вы вышли из системы.');
}
}