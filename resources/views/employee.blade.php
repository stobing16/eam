<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
    <!-- Add Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Employee List</h1>

    <!-- Create Employee Button -->
    <div class="mb-3">
        <a href="{{ route('employees.create') }}" class="btn btn-success">Create New Employee</a>
    </div>

    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($employees as $employee)
            <tr>
                <td>{{ $loop->iteration + ($employees->currentPage() - 1) * $employees->perPage() }}</td>
                <td>{{ $employee->Nama }}</td>
                <td>{{ $employee->Email }}</td>
                <td>
                    <a href="#" class="btn btn-sm btn-primary">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center">No employees found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <!-- Pagination Links -->
    <div class="d-flex justify-content-center">
        {{ $employees->links('pagination::bootstrap-5') }}
    </div>
</div>
</body>
</html>
