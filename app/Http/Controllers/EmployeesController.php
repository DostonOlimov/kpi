<?php


namespace App\Http\Controllers;

use App\Models\EmployeeDays;
use App\Models\Month;
use App\Models\Salaries;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use app\Models\User;
use App\Models\Role;
use App\Models\WorkZone;

class EmployeesController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('role')->with('work_zone')->latest('id')->paginate(20);
        return view('employees.list', compact('users'));
    }
    public function create()
    {
        return view('employees.create', [
            'roles' => Role::All(),
            'works' => WorkZone::All()
        ]);

    }

   /**
    * Store a newly created resource in storage.
    *
    * @param Request $request
    * @return RedirectResponse
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'work_zone_id' => ['nullable', 'integer', 'exists:work_zones,id'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'lavozimi' => ['nullable', 'string', 'max:255'],
            'salary' => ['required', 'numeric', 'min:100000', 'max:99999999.99', 'regex:/^\d+(\.\d{1,2})?$/'],
            'password' => ['required', 'string', 'min:6'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $user = new User($validated);
        $user->password = Hash::make($validated['password']);

        if ($request->hasFile('photo')) {
            $filename = time() . '_' . uniqid() . '.' . $request->photo->extension();
            $request->photo->storeAs('public/users', $filename);
            $user->photo = $filename;
        }

        $user->save();

        Salaries::create([
            'user_id' => $user->id,
            'salary' => $validated['salary'],
            'from_date' => now()->format('Y-m-d'),
        ]);

        return redirect()->route('employees.list')->with('message', 'Foydalanuvchi muvaffaqiyatli yaratildi.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function show($id)
    {
        $user = User::find($id);
        $roles = Role::all();
        $userHasRoles = array_column(json_decode($user->roles, true), 'id');
        return view('employees.show', compact('user', 'roles', 'userHasRoles'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {

        $user = User::find($id);
        $roles = Role::all();
        $works = WorkZone::all();
        return view('employees.edit', compact('user', 'roles','works'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $old_salary = $user->salary;
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'role_id' => ['integer'],
            'work_zone_id' => ['integer'],
            'salary' => ['required', 'numeric', 'min:1', 'max:99999999.999', 'regex:/^\d+(\.\d{1,2})?$/'],
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required'], // Optional: use 'nullable'
            'lavozimi' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // <= add this
        ]);
        $user -> update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'father_name' => $request->father_name,
            'role_id' => $request->role_id,
            'lavozimi' => $request->lavozimi,
            'salary' => $request->salary,
            'work_zone_id' => $request->work_zone_id,
            'username' => $request->username,
        ]);
        if($request->password){
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }
        if ($request->hasFile('photo')) {
            // Remove old photo if exists
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $path = $request->file('photo')->store('users', 'public');
            $user->update(['photo' => $path]);
        }

        if($old_salary != $request->salary) {
            $salary = Salaries::where('user_id', '=', $id)
                ->where('to_date', '=', '9999-01-01')
                ->first();
            $salary->to_date = date('Y-m-d ');
            $salary->save();
            Salaries::create([
                'user_id' => $id,
                'salary' => $request->salary,
                'from_date' => date('Y-m-d'),
            ]);
        }


        return redirect()->route('employees.list')
                        ->with('message','User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect()->route('employees.list')
                        ->with('message','Foydalanuvchi muvaffaqiyatli o\'chirildi');
    }
}
