$(document).ready(function() {
    // Editar usuario
    $(document).on('click', '.editButton', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var email = $(this).data('email');

        $('#userId').val(id);
        $('#name').val(name);
        $('#email').val(email);
    });

    // Eliminar usuario
    $(document).on('click', '.deleteButton', function() {
        var id = $(this).data('id');

        if (confirm("¿Estás seguro de eliminar este usuario?")) {
            $.ajax({
                url: 'delete.php',
                method: 'POST',
                data: {userId: id},
                success: function() {
                    location.reload();
                }
            });
        }
    });

    // Enviar formulario
    $('#userForm').submit(function(event) {
        event.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: 'create_update.php',
            method: 'POST',
            data: formData,
            success: function() {
                location.reload();
            }
        });
    });
});
