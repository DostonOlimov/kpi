<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RelevantUser;
use Illuminate\Http\Request;

class RelevantUserController extends Controller
{
    public function index()
    {
        $users = User::all();
        $relevantUsers = RelevantUser::with(['user', 'boss'])->get();
        return view('admin.relevant_users.index', compact('users', 'relevantUsers'));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'boss_id' => 'required|exists:users,id',
            'step' => 'required|integer',
        ]);

        RelevantUser::create([
            'user_id' => $request->user_id,
            'boss_id' => $request->boss_id,
            'step' => $request->step,
        ]);

        return redirect()->route('admin.relevant-users.index')->with('success', 'Biriktirish muvaffaqiyatli!');
    }
    public function destroy($id)
    {
        \App\Models\RelevantUser::findOrFail($id)->delete();
        return redirect()->route('admin.relevant-users.index')->with('success', 'Biriktirish o‘chirildi!');
    }

    public function edit($id)
    {
        $relevantUser = \App\Models\RelevantUser::findOrFail($id);
        $users = \App\Models\User::all();
        return view('admin.relevant_users.edit', compact('relevantUser', 'users'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'boss_id' => 'required|exists:users,id',
            'step' => 'required|integer',
        ]);
        $relevantUser = \App\Models\RelevantUser::findOrFail($id);
        $relevantUser->update($request->only('user_id', 'boss_id', 'step'));
        return redirect()->route('admin.relevant-users.index')->with('success', 'Biriktirish yangilandi!');
    }
}
