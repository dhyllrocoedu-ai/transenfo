@php($user = $user ?? null)
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $user?->name) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $user?->email) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Password{{ $user ? ' (leave blank to keep)' : '' }}</label>
        <input type="password" name="password" class="form-control" {{ $user ? '' : 'required' }}>
    </div>
    <div class="col-md-6">
        <label class="form-label">Role</label>
        <select name="role" class="form-select" required>
            @foreach ($roles as $role)
                <option value="{{ $role->value }}" @selected(old('role', $user?->role?->value) === $role->value)>{{ $role->label() }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user?->phone) }}">
    </div>
    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" class="form-check-input" name="is_active" value="1" id="is_active" @checked(old('is_active', $user?->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active account</label>
        </div>
    </div>
</div>
