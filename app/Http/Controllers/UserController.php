<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin')->only(['create', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(10);
        return view('pages.user.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);
        try {
            User::create([
                'name' => $request->name, 
                'email' => $request->email, 
                'password' => Hash::make($request->password), 
                'is_admin' => $request->is_admin ? true : false
            ]);
            return redirect()->route('users.index')
                ->with('success', 'İstifadəçi uğurla yaradıldı.');
        } catch (Throwable $th) {
            return redirect()->route('users.index')
                ->with('error', 'Xəta baş verdi.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if(Auth::user()->id !== $user->id) return redirect()->route('users.index')->with('error', 'Düzəliş etmək mümkün deyil.');
        return view('pages.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if(Auth::user()->id !== $user->id) return redirect()->route('users.index')->with('error', 'Düzəliş etmək mümkün deyil.');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'required',
            'password' => 'nullable|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Şifrə yalnışdır.']);
        }



        $user->name = $request->name;
        $user->email = $request->email;
        if($request->password) $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('users.index')->with('success', 'Uğurla düzəliş olundu.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function activate(User $user)
    {
        $user->is_active = true;
        $user->save();
        return redirect()->route('users.index')->with('success', 'Uğurla aktiv  edildi.');
    }
     public function deactivate(User $user)
    {
        $user->is_active = false;
        $user->save();
        return redirect()->route('users.index')->with('success', 'Uğurla deaktiv edildi.');
    }
}
