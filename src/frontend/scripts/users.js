function go() {

    var usersNumber;

    getUser();

    setInterval(function () {
        updateLastActivity();
    }, 3000);

    getAllUsers();

}