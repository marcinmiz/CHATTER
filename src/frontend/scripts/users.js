function go() {
    let script = document.createElement('script');
    script.onload = function () {

        getUser();

        setInterval(function () {
            updateLastActivity();
        }, 3000);

        getAllUsers();

    };
    script.src = "../scripts/common.js";

    document.head.appendChild(script);

}