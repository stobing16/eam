<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Employee</title>
    <!-- Add Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <style>
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            z-index: 9999; /* Ensure it sits on top of everything */
            display: none; /* Hide by default */
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Create New Employee</h1>

    <!-- Back Button -->
    <a href="/employee" class="btn btn-secondary mb-3">Back to Employee List</a>

    <div id="loadingOverlay" style="display: none;">
        <div id="spinner" class="text-center" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Employee Create Form -->
    <form id="createEmployeeForm">

        <div class="mb-3">
            <label for="Nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="Nama" name="Nama" required>
        </div>
        <div class="mb-3">
            <label for="NPWP" class="form-label">Email</label>
            <input type="text" class="form-control" id="Email" name="Email">
        </div>
        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Create Employee</button>
    </form>
    <div id="formResponse" class="mt-3"></div>
</div>

<script>
    $(document).ready(function() {
        $('#createEmployeeForm').on('submit', function(e) {
            e.preventDefault();

            $('#loadingOverlay').fadeIn();

            // Gather form data
            const formData = $(this).serialize();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/create/employee",
                type: "POST",
                data: formData,
                success: function(response) {

                    $('#loadingOverlay').fadeOut();
                    $('#formResponse').html('<div class="alert alert-success">Employee created successfully!</div>');
                    $('#createEmployeeForm')[0].reset(); // Reset the form
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON.errors;
                    let errorHtml = '<div class="alert alert-danger"><ul>';
                    $.each(errors, function(key, value) {
                        errorHtml += `<li>${value}</li>`;
                    });
                    errorHtml += '</ul></div>';
                    $('#formResponse').html(errorHtml);
                }
            });
        });
    });
</script>

</body>
</html>
