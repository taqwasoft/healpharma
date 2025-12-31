<?php

namespace Modules\Business\App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;

class UserRoleController extends Controller
{
    public function index()
    {
        $users = User::where('business_id', auth()->user()->business_id)
            ->where('role', 'staff')
            ->latest()
            ->paginate(10);

        return view('business::roles.index', compact('users'));
    }

    public function acnooFilter(Request $request)
    {
        $search = $request->input('search');
        $users = User::where('business_id', auth()->user()->business_id)
            ->where('role', 'staff')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate($request->per_page ?? 10);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('business::roles.datas', compact('users'))->render()
            ]);
        }

        return redirect(url()->previous());
    }

    public function create()
    {
        return view('business::roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:30',
            'password' => 'required|min:4|max:15',
            'email' => 'required|email|unique:users,email',
        ]);

        User::create([
            'role' => 'staff',
            'name' => $request->name,
            'email' => $request->email,
            'visibility' => $request->permissions,
            'password' => Hash::make($request->password),
            'business_id' => auth()->user()->business_id,
        ]);

        return response()->json([
            'message' => __('User role created successfully'),
            'redirect' => route('business.roles.index')
        ]);
    }

    public function edit($id)
    {
        $user = User::where('business_id', auth()->user()->business_id)->findOrFail($id);
        return view('business::roles.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:30',
            'password' => 'nullable|min:4|max:15',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $user = User::where('business_id', auth()->user()->business_id)->findOrFail($id);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'visibility' => $request->permissions,
            'business_id' => auth()->user()->business_id,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return response()->json([
            'message' => __('User role updated successfully'),
            'redirect' => route('business.roles.index')
        ]);
    }

    public function destroy($id)
    {
        User::where('id', $id)->delete();

        return response()->json([
            'message' => __('User role deleted successfully'),
            'redirect' => route('business.roles.index')
        ]);
    }

    public function deleteAll(Request $request)
    {
        User::whereIn('id', $request->ids)->delete();

        return response()->json([
            'message'   => __('Selected role deleted successfully'),
            'redirect'  => route('business.roles.index')
        ]);
    }
}
