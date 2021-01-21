jQuery(function($) {
    $( document ).ready(function() {

        $("#sign_up_submit").click(function(){
            $.ajax({
                url: 'api/register',
                type:     "POST",
                dataType: "json",
                data: $("#sign_up_form").serialize(),
                success: function(result) {
                    $("#notice").html('<div class="alert alert-success" role="alert">' + result.data + '</div>');
                },
                error: function(result) {
                    $("#notice").html('<div class="alert alert-danger" role="alert">' + result.responseJSON.data + '</div>');
                }
            });
        });

        $("#login_form_submit").click(function(){
            $.ajax({
                url: 'api/login',
                type:     "POST",
                dataType: "json",
                data: $("#login_form").serialize(),
                success: function(result) {
                    setCookie("token", result.data, 1);
                    $("#logout_submit").show();
                    $("#login, #register").hide();
                    $("#notice").html('<div class="alert alert-success" role="alert">You are logged in</div>');
                },
                error: function(result) {
                    $("#notice").html('<div class="alert alert-danger" role="alert">' + result.responseJSON.data + '</div>');
                }
            });
        });

        $("#logout_submit").click(function(){
            $("#logout_submit").hide();
            $("#login, #register").show();
            setCookie("token", "", 1);
            $(location).attr('href','/login');
        });

        if($("#home").length) {
            $.ajax({
                url: 'api/users',
                type: "GET",
                dataType: "json",
                data: {
                    'token': getCookie('token')
                },
                success: function(result) {
                    $("#home").html(result.message)
                }
            });
        }

        if($("#admin").length) {
            $.ajax({
                url: 'api/users',
                type: "GET",
                dataType: "json",
                data: {
                    'token': getCookie('token')
                },
                success: function(result) {
                    if (true == result.status){
                        var html = '';
                        $.each(result.data,function( index, value ) {
                            html += `<tr>
                            <td>` + value.name + `</td>
                            <td>` + value.message + `</td>
                        <td>` + value.time + `</td></tr>`;
                        });
                        $("#admin-table").html(html)
                    } else {
                        $("#notice").html('<div class="alert alert-danger" role="alert">' + result.data + '</div>');
                    }
                },
                error: function(result) {
                    $("#notice").html('<div class="alert alert-danger" role="alert">' + result.responseJSON.data + '</div>');
                }
            });
        }

        if ( '' == getCookie('token') ) {
            $("#logout_submit").hide();
            $("#login, #register").show();
        } else {
            $("#logout_submit").show();
            $("#login, #register").hide();
        }

        function setCookie(cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays*24*60*60*1000));
            var expires = "expires="+ d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        function getCookie(cname) {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for(var i = 0; i <ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' '){
                    c = c.substring(1);
                }

                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }

    });
});