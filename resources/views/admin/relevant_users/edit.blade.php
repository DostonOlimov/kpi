{{-- filepath: resources/views/admin/relevant_users/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Biriktirishni tahrirlash</h3>
    <form action="{{ route('admin.relevant-users.update', $relevantUser->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col">
                <label>Xodim:</label>
                <select name="user_id" class="form-control" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @if($relevantUser->user_id == $user->id) selected @endif>
                            {{ $user->first_name }} {{ $user->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <label>Rahbar:</label>
                <select name="boss_id" class="form-control" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @if($relevantUser->boss_id == $user->id) selected @endif>
                            {{ $user->first_name }} {{ $user->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <button type="submit" class="btn btn-success mt-4">Saqlash</button>
            </div>
        </div>
    </form>
</div>
@endsection