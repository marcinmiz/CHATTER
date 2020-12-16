function go() {
    let script = document.createElement('script');
    script.onload = function () {

        getUser();

        setInterval(function () {
            updateLastActivity();
        }, 3000);

        getAllUsers(0);

        document.getElementsByClassName('search-input')[0].addEventListener('change', function () {
            searchUsers(0);
        });

        document.getElementsByClassName('search-button')[0].addEventListener('click', function () {
            searchUsers(0);
        });
    };
    script.src = "../scripts/common.js";

    document.head.appendChild(script);

}