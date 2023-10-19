//ログイン関数
document.addEventListener("DOMContentLoaded", function() {
    const loginForm = document.getElementById("Login_Authorization"); 
    const submitButton = document.getElementById("Login");

    submitButton.addEventListener("click", function(event) {
        event.preventDefault();
        loginForm.action = "/manage-raquty-cms-system/login/login.php"; 
        loginForm.submit();
    });
});

// home_url()　JS版
function homeUrl(dir = '') {
    const format = (window.location.protocol === 'https:') ? 'https://' : 'http://';
    return format + window.location.host + '/' + dir.replace(/^\//, '');
}

//ログアウト処理
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-logout-action]').forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            const form = document.createElement('form');
            form.method = 'post';
            form.action = '/manage-raquty-cms-system/login/logout.php';

            const logoutFunctionInput = document.createElement('input');
            logoutFunctionInput.type = 'hidden';
            logoutFunctionInput.name = 'logoutFunction';
            logoutFunctionInput.value = this.getAttribute('data-logout-action');

            form.appendChild(logoutFunctionInput);

            document.body.appendChild(form);
            form.submit();
        });
    });
});
