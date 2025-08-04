{{-- filepath: resources/views/admin/relevant_users/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Xodim va Rahbar biriktirish</h3>
        <form action="{{ route('admin.relevant-users.assign') }}" method="POST" class="mb-4">
            @csrf
            <div class="row">
                <div class="col">
                    <label>Xodim:</label>
                    <select name="user_id" class="form-control" required>
                        <option value="">Tanlang</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <label>Rahbar:</label>
                    <select name="boss_id" class="form-control" required>
                        <option value="">Tanlang</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary mt-4">Biriktirish</button>
                </div>
            </div>
        </form>

        <h5>Mavjud biriktirishlar:</h5>
        <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>T/r</th>
                        <th>XODIM</th>
                        <th>RAHBAR</th>
                        <th>AMALLAR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($relevantUsers as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->user->first_name ?? '' }} {{ $item->user->last_name ?? '' }}</td>
                            <td>{{ $item->boss->first_name ?? '' }} {{ $item->boss->last_name ?? '' }}</td>
                            <td>
                                <a href="{{ route('admin.relevant-users.edit', $item->id) }}"
                                    class="btn btn-sm btn-warning">Tahrirlash</a>
                                <form action="{{ route('admin.relevant-users.destroy', $item->id) }}" method="POST"
                                    style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('O‘chirishga ishonchingiz komilmi?')">O‘chirish</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </table>
    </div>
@endsection
