<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div>
    <h3 class="text-lg font-semibold mb-4">Register New Account</h3>

    <label class="block text-sm font-medium text-gray-700">Email</label>
    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="mt-1 block w-full border rounded p-2" required>
    @error('email')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror

    <label class="block text-sm font-medium text-gray-700 mt-4">Username</label>
    <input type="text" name="username" value="{{ old('username', $user->username ?? '') }}" class="mt-1 block w-full border rounded p-2" required>
    @error('username')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror

    <label class="block text-sm font-medium text-gray-700 mt-4">Name</label>
    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="mt-1 block w-full border rounded p-2">
    @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror

    <label class="block text-sm font-medium text-gray-700 mt-4">Password</label>
    <input type="password" name="password" class="mt-1 block w-full border rounded p-2">
    @error('password')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror

    <label class="block text-sm font-medium text-gray-700 mt-4">Confirm Password</label>
    <input type="password" name="password_confirmation" class="mt-1 block w-full border rounded p-2">


  </div>

  <div>

    <label class="block text-sm font-medium text-gray-700">Role</label>
    <select name="role" class="mt-1 block w-full min-h-[44px] border rounded p-2 text-base">
      <option value="">Select</option>
      @foreach($roles as $role)
        <option value="{{ $role->id }}" {{ (in_array($role->id, old('roles', isset($user) ? $user->roles->pluck('id')->toArray() : []) ) ? 'selected' : '') }}>{{ $role->name }}</option>
      @endforeach
    </select>

    <label class="block text-sm font-medium text-gray-700 mt-4">Status</label>
    <select name="is_active" class="mt-1 block w-full border rounded p-2">
      <option value="1" {{ old('is_active', $user->is_active ?? 1) ? 'selected' : '' }}>Active</option>
      <option value="0" {{ old('is_active', $user->is_active ?? 1) === 0 ? 'selected' : '' }}>Inactive</option>
    </select>


  </div>

  <div class="mt-6">
    <div class="flex flex-col md:flex-row md:items-center md:gap-3">
      <button type="submit" class="w-full md:w-20 {{ isset($user) ? 'bg-green-600' : 'bg-indigo-600' }} text-white py-2 rounded shadow">
        {{ isset($user) ? 'Save' : 'Register' }}
      </button>
      <a href="{{ route('admin.users.index') }}" class="mt-3 md:mt-0 w-full md:w-auto px-4 py-2 border rounded text-gray-700 text-center">Cancel</a>
    </div>
  </div>
</div>