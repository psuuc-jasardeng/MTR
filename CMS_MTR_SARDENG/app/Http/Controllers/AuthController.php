<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Insert default admin user if it doesn't exist
        $adminExists = DB::table('users')->where('email', 'admin')->first();
        if (!$adminExists) {
            DB::table('users')->insert([
                'name' => 'Admin',
                'email' => 'admin',
                'password' => 'admin',
                'is_admin' => 1, // Set the default admin user as an admin
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (session()->has('user')) {
            return redirect()->route('blog.index');
        }

        return view('auth.login');
    }

    public function showAdminLoginForm()
    {
        if (session()->has('user')) {
            return redirect()->route('blog.index');
        }

        return view('auth.admin-login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        $userExists = DB::table('users')->where('email', $email)->first();
        if ($userExists) {
            return redirect()->back()->with('error', 'Email already exists. Please use a different email.');
        }

        DB::table('users')->insert([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'is_admin' => 0, // New users are clients by default
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('login')->with('success', 'Registration successful! Please log in.');
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $isAdmin = $request->input('is_admin', 0); // Default to 0 (client) if not set

        $user = DB::table('users')
            ->where('email', $email)
            ->where('password', $password)
            ->where('is_admin', $isAdmin)
            ->first();

        if ($user) {
            // Store both email, name, and is_admin in the session as an array
            $request->session()->put('user', [
                'email' => $user->email,
                'name' => $user->name,
                'is_admin' => $user->is_admin,
            ]);
            return redirect()->route('blog.index')->with('success', 'Logged in successfully!');
        }

        return redirect()->back()->with('error', 'Invalid credentials or user type.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('user');
        return redirect()->route('login')->with('success', 'Logged out successfully!');
    }
}