$(function(){

    $('#login-form input').first().focus();

    var alert = $('<div class="alert alert-error">I dati inseriti non sono corretti</div>').hide();
    $('#login-form').append(alert);

    $('#login-form input').focus(function() {
        $('#login-form .alert').hide('50');
    });

    $('#login-form button').click(function (e) {
        e.preventDefault();
        $.ajax({
            url: '/admin/login/',
            type: 'POST',
            dataType: 'json',
            data: $('#login-form').serialize(),
            success: function(data) {
                if (data.logged == true) {
                    location.reload();
                } else {
                    $('#login-form input').first().focus();
                    $('#login-form .alert').show('100');
                }
            }
        });
    });

});

