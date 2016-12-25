
    function onSignIn(googleUser) {
        
        var profile = googleUser.getBasicProfile();

        $("#grettings").html(profile.getName());
        $("#gLogin").hide();
        $("#send").show();
        $("#gLogout").show();
    }

    function signOut() {

        var auth2 = gapi.auth2.getAuthInstance();

        auth2.signOut().then(function () {

            $("#grettings").html('');
            $("#gLogout").hide();
            $("#gLogin").show();
            $("#send").hide();
        });
    }

    $("#wscalls").DataTable({
      sPaginationType: "full_numbers",
      oLanguage:
      {
        "sProcessing":   "Processing...",
        "sLengthMenu":   "Showing _MENU_ calls",
        "sZeroRecords":  "There is no calls",
        "sInfo":         "Showing from _START_ to _END_ of _TOTAL_ calls",
        "sInfoEmpty":    "Showing 0 calls",
        "sInfoFiltered": "(filtering _MAX_ total calls)",
        "sInfoPostFix":  "",
        "sSearch":       "Search:",
        "sUrl":          "",
        "oPaginate": {
            "sFirst":    "First",
            "sPrevious": "Previous",
            "sNext":     "Next",
            "sLast":     "Last"
        }
    },
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "snail"
    }
    );

    $("#send").click(function(){

        var h = $("#h").val();
        var u = $("#u").val();
        var d = $("#d").val();
        var f = $("#f").val();
        var token = $("#token").val();

        if (!h || !u || !d || !f) {

            alert("Missing Parameters");
            return false;
        }

        var dataSnail = { "h": h, "u": u, "d": d, "f": f, "token": token };
        $.ajax({
            url : "snail/",
            type: "POST",
            data: JSON.stringify(dataSnail),
            contentType: "application/json; charset=utf-8",
            dataType   : "json",
            success    : function(msg){

                console.log(msg);
            }
        });
        $('#formData').trigger("reset");
        dt = $("#wscalls").dataTable();
        dt.fnDraw();
    });
