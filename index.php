<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD PHP MySQL AJAX</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>CRUD with Image Upload, Select, Radio, Checkbox</h2>

        <!-- Form to Create/Update User -->
        <form id="userForm" enctype="multipart/form-data">
            <input type="hidden" name="id" id="id">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" class="form-control" name="name" id="name" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" class="form-control" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label>Gender:</label><br>
                <input type="radio" name="gender" value="Male" checked> Male
                <input type="radio" name="gender" value="Female"> Female
            </div>
            <div class="form-group">
                <label>Skills:</label><br>
                <input type="checkbox" name="skills[]" value="HTML"> HTML
                <input type="checkbox" name="skills[]" value="CSS"> CSS
                <input type="checkbox" name="skills[]" value="JavaScript"> JavaScript
                <input type="checkbox" name="skills[]" value="PHP"> PHP
            </div>
            <div class="form-group">
                <label>Country:</label>
                <select class="form-control" name="country" id="country">
                    <option value="USA">USA</option>
                    <option value="Canada">Canada</option>
                    <option value="UK">UK</option>
                </select>
            </div>
            <div class="form-group">
                <label>Profile Picture:</label>
                <input type="file" class="form-control" name="profile_pic" id="profile_pic">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <!-- Response Div -->
        <div id="response" class="mt-3"></div>
        
        <!-- User Data Display -->
        <div id="userData" class="mt-3"></div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // Load Users on Page Load
            loadUsers();

            // Submit Form via AJAX
            $('#userForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                var action = $('#id').val() ? 'update' : 'create';
                formData.append('action', action);

                $.ajax({
                    url: 'process.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#response').html(response);
                        $('#userForm')[0].reset();
                        loadUsers();
                    }
                });
            });

            // Load Users
            function loadUsers() {
                $.ajax({
                    url: 'process.php',
                    type: 'POST',
                    data: {action: 'read'},
                    success: function(response) {
                        $('#userData').html(response);
                    }
                });
            }

            // Edit User
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: 'process.php',
                    type: 'POST',
                    data: {action: 'get_user', id: id},
                    success: function(response) {
                        var user = JSON.parse(response);
                        $('#id').val(user.id);
                        $('#name').val(user.name);
                        $('#email').val(user.email);
                        $('input[name="gender"][value="' + user.gender + '"]').prop('checked', true);
                        $('#country').val(user.country);
                        $('input[name="skills[]"]').each(function() {
                            $(this).prop('checked', user.skills.split(',').includes($(this).val()));
                        });
                    }
                });
            });

            // Delete User
            $(document).on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this user?')) {
                    $.ajax({
                        url: 'process.php',
                        type: 'POST',
                        data: {action: 'delete', id: id},
                        success: function(response) {
                            $('#response').html(response);
                            loadUsers();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
