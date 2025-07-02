@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-life-buoy mr-1"></i>&nbsp; Foydalanuvchi ma'lumotlarini tahrirlash
                </li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">
                        @if(session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{ route('employees.update', $user->id) }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                {{-- Username --}}
                                <div class="form-group col-md-6">
                                    <label for="username">Foydalanuvchi nomi</label>
                                    <input type="text" id="username" name="username" class="form-control" value="{{ old('username', $user->username) }}">
                                    @error('username') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                </div>

                                {{-- Password --}}
                                <div class="form-group col-md-6">
                                    <label for="password">Yangi parol (ixtiyoriy)</label>
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Yangi parol kiriting">
                                    @error('password') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                </div>

                                {{-- First Name --}}
                                <div class="form-group col-md-6">
                                    <label for="first_name">Ismi</label>
                                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}">
                                    @error('first_name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                </div>

                                {{-- Last Name --}}
                                <div class="form-group col-md-6">
                                    <label for="last_name">Familiyasi</label>
                                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}">
                                    @error('last_name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                </div>

                                {{-- Father Name --}}
                                <div class="form-group col-md-6">
                                    <label for="father_name">Sharifi</label>
                                    <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $user->father_name) }}">
                                    @error('father_name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                </div>

                                {{-- Role --}}
                                <div class="form-group col-md-6">
                                    <label for="role_id">Roli</label>
                                    <select name="role_id" class="form-control">
                                        <option disabled selected>-- Roli tanlang --</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                </div>

                                {{-- Work Zone --}}
                                <div class="form-group col-md-6">
                                    <label for="work_zone_id">Ish joyi</label>
                                    <select name="work_zone_id" class="form-control">
                                        <option disabled selected>-- Ish joyini tanlang --</option>
                                        @foreach($works as $work)
                                            <option value="{{ $work->id }}" {{ $user->work_zone_id == $work->id ? 'selected' : '' }}>
                                                {{ $work->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('work_zone_id') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                </div>

                                {{-- Position --}}
                                <div class="form-group col-md-6">
                                    <label for="lavozimi">Lavozimi</label>
                                    <input type="text" name="lavozimi" class="form-control" value="{{ old('lavozimi', $user->lavozimi) }}">
                                    @error('lavozimi') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                </div>

                                {{-- Salary --}}
                                <div class="form-group col-md-6">
                                    <label for="salary">Oylik maoshi (so'mda)</label>
                                    <input type="number" step="0.0001" name="salary" class="form-control" value="{{ old('salary', round($user->salary,3)) }}">
                                    @error('salary') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-xs-12 col-sm-12 col-md-6">
                                    <div class="form-group">
                                        <strong>Rasm yuklash:</strong>
                                        <input type="file" name="photo" class="form-control">
                                        @if($user->photo)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . $user->photo) }}" alt="User Photo" width="100">
                                            </div>
                                        @endif
                                        @error('photo')
                                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="form-group col-md-12 text-center mt-4">
                                    <a class="btn btn-secondary" href="{{ URL::previous() }}">Bekor qilish</a>
                                    <button type="submit" class="btn btn-success">Saqlash</button>
                                </div>
                            </div>
                        </form>

                    </div> <!-- card-body -->
                </div> <!-- card -->
            </div>
        </div>
    </div>
@endsection

