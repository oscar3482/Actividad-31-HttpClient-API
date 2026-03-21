<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Credenciales incorrectas'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
    // Ver perfil
public function perfil(Request $request)
{
    return response()->json($request->user());
}

// Editar datos generales
public function actualizarPerfil(Request $request)
{
    $user = $request->user();

    $request->validate([
        'name'  => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'phone' => 'nullable|string|max:20',
    ]);

    $user->update([
        'name'  => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
    ]);

    return response()->json([
        'message' => 'Perfil actualizado correctamente',
        'user'    => $user
    ]);
}

// Actualizar imagen de perfil
public function actualizarImagen(Request $request)
{
    $request->validate([
        'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $user = $request->user();
    $path = $request->file('foto')->store('perfiles', 'public');
    $user->update(['foto' => asset('storage/' . $path)]);

    return response()->json([
        'message' => 'Imagen actualizada correctamente',
        'foto'    => $user->foto
    ]);
}

// Actualizar contraseña
public function actualizarPassword(Request $request)
{
    $request->validate([
        'password_actual' => 'required',
        'password_nuevo'  => 'required|min:6|confirmed',
    ]);

    $user = $request->user();

    if (!Hash::check($request->password_actual, $user->password)) {
        return response()->json(['error' => 'La contraseña actual es incorrecta'], 400);
    }

    $user->update(['password' => Hash::make($request->password_nuevo)]);

    return response()->json(['message' => 'Contraseña actualizada correctamente']);
}
}