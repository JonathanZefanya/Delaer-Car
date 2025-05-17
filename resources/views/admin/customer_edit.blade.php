@extends('admin.app_admin')

@section('admin_content')
<h1 class="h3 mb-3 text-gray-800">Edit Customer</h1>

<form action="{{ route('admin_customer_update', $customer->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="form-group">
                <label>Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
            </div>

            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}" required>
            </div>

            <div class="form-group">
                <label>Password (Kosongkan jika tidak ingin ubah)</label>
                <input type="password" name="password" class="form-control">
            </div>

            <div class="form-group">
                <label>Photo</label><br>
                @if ($customer->photo)
                    <img src="{{ asset('uploads/user_photos/' . $customer->photo) }}" width="100">
                @endif
                <input type="file" name="photo" class="form-control mt-2">
            </div>

            <div class="form-group">
                <label>Status *</label>
                <select name="status" class="form-control" required>
                    <option value="Active" {{ $customer->status == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Pending" {{ $customer->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin_customer_view') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
</form>
@endsection
