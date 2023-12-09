$(document).ready(function(){
    var userToken = localStorage.getItem("token");
    console.log(userToken, "foo");

    $.ajax({
        url: "http://localhost/guvi-project/php/profile.php",
        type: "GET",
        headers: {
            "AUTH_TOKEN": userToken
        },
        success: function(response){
            console.log(response);
            response.name ? $("#name").val(response.name).css("color", "orange"): "";
            response.email ? $("#email").val(response.email).css("color", "orange"): "";
            response.cnum ? $("#cnum").val(response.cnum).css("color", "babyPink"): "";
            response.dob ? $("#dob").val(response.dob).css("color", "babyPink"): "";
        },
        error: function(error){
            alert("Error: Cannot load the page")
        }
    });
});
