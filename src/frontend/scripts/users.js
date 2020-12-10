function go() {

    var usersNumber;

    getUser();

    setInterval(function () {
        updateLastActivity();
    }, 3000);

    function getStatuses() {
        fetch('../../api/users/get/statuses/' + localStorage.getItem("current_user_id") + "/", {
            headers: {
                'Accept': 'application/json'
            }
        })
            .then((res) => res.json())
            .then((data) => {
                console.log(data);
                const usersStatuses = document.getElementsByClassName("user-status");
                for (let i = 0; i < usersNumber; i++) {
                    usersStatuses[i].innerHTML = data[i].last_activity;
                }
            })
            .catch((error) => console.log(error))
    }


    function getAllUsers() {
        fetch('../../api/users/get/all_users/' + localStorage.getItem("current_user_id") + "/0/0/", {
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(res => res.json())
            .then(data => {
                console.log(data);
                usersNumber = data.length;
                const usersList = document.getElementById("users-list");

                for (let i = 0; i < data.length; i++) {
                    let newUser = document.createElement('div');
                    newUser.setAttribute("class", "user");

                    let avatar = document.createElement('div');
                    avatar.setAttribute("class", "user-avatar");

                    let avatarImg = document.createElement('img');
                    avatarImg.setAttribute("class", "avatar");
                    avatarImg.setAttribute('src', '../../extras/img/avatar.png');
                    avatarImg.setAttribute('alt', 'avatar');
                    avatar.appendChild(avatarImg);
                    newUser.appendChild(avatar);

                    let nameContainer = document.createElement('div');
                    nameContainer.setAttribute('class', 'user-name');

                    let name = document.createElement('p');
                    name.appendChild(document.createTextNode(data[i].user_name + ' ' + data[i].surname));
                    nameContainer.appendChild(name);
                    newUser.appendChild(nameContainer);

                    let userType = document.createElement('div');
                    userType.setAttribute('class', 'user-type');

                    let userIcon = document.createElement('i');
                    userIcon.setAttribute('class', 'icon-user');
                    userType.appendChild(userIcon);
                    newUser.appendChild(userType);

                    let userStatus = document.createElement('div');
                    userStatus.setAttribute('class', 'user-status');
                    newUser.appendChild(userStatus);

                    let favButton = document.createElement('a');
                    favButton.setAttribute('class', 'user-favourite');
                    favButton.setAttribute('type', 'button');
                    let favIcon = document.createElement('i');
                    favIcon.setAttribute('class', 'icon-star-empty');
                    favButton.appendChild(favIcon);
                    newUser.appendChild(favButton);

                    let addUserButton = document.createElement('a');
                    addUserButton.setAttribute('class', 'user-group-add');
                    addUserButton.setAttribute('type', 'button');
                    let addUserIcon = document.createElement('i');
                    addUserIcon.setAttribute('class', 'icon-user-add');
                    addUserButton.appendChild(addUserIcon);
                    newUser.appendChild(addUserButton);

                    usersList.appendChild(newUser);

                    function toogleChat() {
                        localStorage.setItem("another_user_id", data[i].user_id);
                        localStorage.setItem("group", "private");
                        location="chat.php";
                    }

                    newUser.addEventListener('click', toogleChat);

                    favButton.addEventListener('click', function () {
                    });

                    favButton.addEventListener('mouseenter', function () {
                        newUser.removeEventListener('click', toogleChat);
                    });

                    favButton.addEventListener('mouseleave', function () {
                        newUser.addEventListener('click', toogleChat);
                    });

                    addUserButton.addEventListener('click', function () {
                    });

                    addUserButton.addEventListener('mouseenter', function () {
                        newUser.removeEventListener('click', toogleChat);
                    });

                    addUserButton.addEventListener('mouseleave', function () {
                        newUser.addEventListener('click', toogleChat);
                    });
                }

                getStatuses();

                setInterval(function () {
                    getStatuses();
                }, 2000);

                })
            .catch((error) => console.log(error))
    }

    getAllUsers();

}