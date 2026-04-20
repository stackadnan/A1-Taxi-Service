<div class="overflow-auto">
  <table class="table-auto w-full border">
    <thead>
      <tr class="bg-gray-100">
        <th class="px-3 py-2">Role \ Permission</th>
        @foreach($permissions as $permission)
          <th class="px-3 py-2 text-xs">{{ $permission->name }}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach($roles as $role)
        <tr>
          <td class="border px-2 py-2 font-medium">{{ $role->name }}</td>
          @foreach($permissions as $permission)
            <td class="border px-2 py-2 text-center">
              <input type="checkbox" name="assign[{{ $role->id }}][]" value="{{ $permission->id }}" {{ $role->permissions->contains($permission) ? 'checked' : '' }} />
            </td>
          @endforeach
        </tr>
      @endforeach
    </tbody>
  </table>
</div>