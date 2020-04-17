$(document).ready(() => {

    if ($(".shf-authentication-login-in-error").length > 0) {
        $(".detail-card").css("display", "none");
        $(".openLoginButton").css("display", "none");

    }
    $(".openLoginButton").click(() => {
        $(".shf-authentication-login").css("display", "block");
        $(".detail-card").css("display", "none");
        $(".openLoginButton").css("display", "none");
    });
});