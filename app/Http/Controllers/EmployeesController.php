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
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255',],
            'role_id' => ['required','integer'],
            'salary' => ['required','numeric', 'min:100000','max:99999999.999', 'regex:/^\d+(\.\d{1,2})?$/'],
            'work_zone_id' => ['integer'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'lavozimi' => ['string','max:255'],
            'password' => ['required', ],//Rules\Password::defaults()
        ]);
        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->father_name = $request->father_name;
        $user->role_id = $request->role_id;
        $user->lavozimi = $request->lavozimi;
        $user->work_zone_id = $request->work_zone_id;
        $user->username = $request->username;
        $user->salary = $request->salary;
        $user->password = Hash::make($request->password);
        $user->save();
        $salary = Salaries::create([
            'user_id'  =>  $user->id,
            'salary' => $request->salary,
            'from_date' => date('Y-m-d '),
        ]);
        return redirect()->route('employees.list')
            ->with('message','Foydalanuvchi muvaffaqiyatli yaratildi.');
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
            'last_name' => ['required', 'string', 'max:255',],
            'role_id' => ['integer'],
            'work_zone_id' => ['integer'],
            'salary' => ['required','numeric', 'min:1','max:99999999.999', 'regex:/^\d+(\.\d{1,2})?$/'],
            'username' => ['required', 'string', 'max:255',],
            'password' => ['required', ],//Rules\Password::defaults()
            'lavozimi' => ['string','max:255'],
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
