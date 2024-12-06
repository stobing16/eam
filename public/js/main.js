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
