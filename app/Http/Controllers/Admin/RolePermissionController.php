<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RolePermissionController extends Controller
{
    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => collect([]),
            'permissions' => collect([])
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.create', [
            'permissions' => collect([])
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        return back()->with('hata', 'Bu özellik henüz aktif değil.');
    }

    public function edit($role): View
    {
        return view('admin.roles.edit', [
            'role' => null,
            'permissions' => collect([])
        ]);
    }

    public function update(Request $request, $role): RedirectResponse
    {
        return back()->with('hata', 'Bu özellik henüz aktif değil.');
    }

    public function destroy($role): RedirectResponse
    {
        return back()->with('hata', 'Bu özellik henüz aktif değil.');
    }
}
