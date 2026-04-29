<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Pages</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Page Manager</h1>
            <p class="text-muted mb-0">Manage dynamic page content and URL mappings.</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ url('/') }}" target="_blank">Open Website</a>
            <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger">Logout</button>
            </form>
            <a class="btn btn-primary" href="{{ route('admin.pages.create') }}">Add Page</a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Rows Pattern</th>
                    <th>Primary URL</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($pages as $page)
                    @php($primaryUrl = $page->urls->first())
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $page->name }}</div>
                            <small class="text-muted">ID: {{ $page->id }}</small>
                        </td>
                        <td><code>{{ $page->number_of_rows ?: '1' }}</code></td>
                        <td>
                            @if($primaryUrl)
                                <a href="{{ url('/'.$primaryUrl->group_slug.'/'.$primaryUrl->slug) }}" target="_blank">
                                    /{{ $primaryUrl->group_slug }}/{{ $primaryUrl->slug }}
                                </a>
                                @if(!$primaryUrl->is_active)
                                    <span class="badge text-bg-warning ms-1">Inactive</span>
                                @endif
                            @else
                                <span class="text-muted">Not mapped</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" onsubmit="return confirm('Delete this page?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No pages found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $pages->links() }}
    </div>
</div>
</body>
</html>
