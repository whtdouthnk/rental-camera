<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // tampil halaman login
    public function index()
    {
        return view('welcome');
    }


    // proses login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);


        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];


        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            $request->session()->regenerate();


            // cek status akun
            if (Auth::user()->status !== 'Aktif') {

                Auth::logout();

                $request->session()->invalidate();

                $request->session()->regenerateToken();

                return back()->with(
                    'error',
                    'Akun Anda telah dinonaktifkan, hubungi admin.'
                );

            }


            LogAktivitas::record('login', 'Auth', "User {$request->email} berhasil login");


            // cek role
            if(Auth::user()->role == 'Admin'){

                return redirect('/admin/dashboard');

            }elseif(Auth::user()->role == 'Petugas'){

                return redirect('/petugas/dashboard');

            }else{

                return redirect('/user/dashboard');

            }

        }


        return back()->with(
            'error',
            'Email atau password salah'
        );
    }


    // logout
    public function logout(Request $request)
    {
        LogAktivitas::record('logout', 'Auth', 'User berhasil logout');

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();


        return redirect('/login');
    }
}