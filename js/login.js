$(document).ready(function () {
    $("#loginbtn").click(function (event) {
        event.preventDefault();
        var userEmail = $("#inpEmail").val();
        var userPassword = $("#inpPassword").val();

        $.ajax({
            url: "http://localhost/guvi-project/php/login.php",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({ email: userEmail, password: userPassword }),
            success: function (response) {
                var userToken = response.token;
                localStorage.setItem("token", userToken);
                window.location.href = "http://localhost/guvi-project/index.html";
            },
            error: function (error) {
                console.log(error);
                alert("Login failed: " + error.message);
            }
        });
    });
});
