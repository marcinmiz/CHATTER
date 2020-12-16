let usersNumber;
var interval;

function getUser() {
    fetch('../../api/users/get/user/' + localStorage.getItem("current_user_id") + "/", {
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(res => res.json())
        .then(data => {
            document.getElementsByTagName('p')[0].innerHTML = data.user_name + " " + data.surname;
        })
        .catch(error => console.log(error))
}

function updateLastActivity() {
    fetch("../../api/users/update/last_activity/" + localStorage.getItem("current_user_id") + "/", {
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(res => res.json())
        .then(data => console.log(data))
        .catch(error => console.log(error))
}

function getStatuses(ids) {
    let data = {
        'action': 'get',
        'complement': 'statuses',
        'current_user_id': localStorage.getItem("current_user_id"),
        'ids': ids
    };
    fetch('../../api/users/get/statuses/', {
        method: 'POST',
        headers: {
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then((res) => res.json())
        .then((data) => {
            console.log(data);
            for (let i = 0; i < usersNumber; i++) {
                let id = 'user-status' + data[i].user_id;
                let userStatus = document.getElementById(id);
                console.log(id);
                userStatus.innerHTML = data[i].last_activity;
            }
        })
        .catch((error) => console.log(error))
}

function createUserTab(fav, usersList, data, i) {
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
    userStatus.id = 'user-status'+data[i].user_id;
    newUser.appendChild(userStatus);

    let favButton = document.createElement('a');
    favButton.setAttribute('class', 'user-favourite');
    favButton.setAttribute('type', 'button');
    let favIcon = document.createElement('i');
    if (data[i].fav){
        favIcon.setAttribute('class', 'icon-star');
    } else {
        favIcon.setAttribute('class', 'icon-star-empty');
    }
    favButton.appendChild(favIcon);
    newUser.appendChild(favButton);

    let addUserButton;

    if (!fav){
        addUserButton = document.createElement('a');
        addUserButton.setAttribute('class', 'user-group-add');
        addUserButton.setAttribute('type', 'button');
        let addUserIcon = document.createElement('i');
        addUserIcon.setAttribute('class', 'icon-user-add');
        addUserButton.appendChild(addUserIcon);
        newUser.appendChild(addUserButton);
    }

    usersList.appendChild(newUser);

    function toogleChat() {
        localStorage.setItem("another_user_id", data[i].user_id);
        localStorage.setItem("group", "private");
        location="chat.php";
    }

    newUser.addEventListener('click', toogleChat);

    favButton.addEventListener('click', function () {
        localStorage.setItem("favourite_user_id", data[i].user_id);
        markUserAsFavourite(favIcon);
    });

    favButton.addEventListener('mouseenter', function () {
        newUser.removeEventListener('click', toogleChat);
    });

    favButton.addEventListener('mouseleave', function () {
        newUser.addEventListener('click', toogleChat);
    });

    if (!fav) {
        addUserButton.addEventListener('click', function () {
        });

        addUserButton.addEventListener('mouseenter', function () {
            newUser.removeEventListener('click', toogleChat);
        });

        addUserButton.addEventListener('mouseleave', function () {
            newUser.addEventListener('click', toogleChat);
        });
    }
}

function getAllUsers(fav) {
    let complement;
    if (fav)
    {
        complement = localStorage.getItem("current_user_id") + "/" + localStorage.getItem("another_user_id")
    } else {
        complement = localStorage.getItem("current_user_id") + "/0"
    }
    fetch('../../api/users/get/all_users/' + complement + "/" + fav +"/", {
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(res => res.json())
        .then(data => {
            console.log(data);
            usersNumber = data.length;
            let usersList;

            if (fav)
            {
                usersList = document.getElementById("favourite-users-list");
                if (usersNumber < 1)
                {
                    usersList.innerText = "No favourite users";
                    return;
                }
            } else {
                usersList = document.getElementById("users-list");
                if (usersNumber < 1)
                {
                    usersList.innerText = "No users";
                    return;
                }
            }

            let ids = [];
            for (let i = 0; i < data.length; i++) {

                createUserTab(fav, usersList, data, i);
                ids.push(data[i].user_id);
            }

            getStatuses(ids);

            interval = setInterval(function () {
                getStatuses(ids);
            }, 2000);

        })
        .catch((error) => console.log(error))
}

function markUserAsFavourite(favIcon) {
    let okResponse;
    let icon = favIcon.className === "icon-star-empty" ? 1 : 0;
    fetch('../../api/users/mark/favourite_user/' + localStorage.getItem("current_user_id") + "/" + localStorage.getItem("favourite_user_id") + "/" + icon + "/", {
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(res => {
            okResponse = res.ok;
            res.json()
        })
        .then(data => {
            if (okResponse)
            {
                if (icon)
                {
                    favIcon.setAttribute('class', 'icon-star');
                } else {
                    favIcon.setAttribute('class', 'icon-star-empty');
                }
            } else {
                console.log("Action has not been done properly");
            }
        })
        .catch(error => console.log(error))
}

function searchUsers(fav) {
    clearInterval(interval);
    let key = document.getElementsByClassName('search-input')[0].value;

    let data = {
        'action' : 'search',
        'complement' : 'users',
        'current_user_id': localStorage.getItem("current_user_id"),
        'key' : key
    };

    if (fav)
    {
        data.another_user_id = localStorage.getItem("another_user_id");
        data.online = document.getElementsByClassName('search-online-users')[0].checked;
    } else {
        data.another_user_id = 0;
        data.online = document.getElementsByClassName('search-online-users')[0].checked;
        data.favourite = document.getElementsByClassName('search-favourite-users')[0].checked;
    }
    console.log(key);
    fetch('../../api/users/search/users/', {
        method: 'POST',
        headers: {
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(res => res.json())
        .then(data => {
            console.log(data);
            usersNumber = data.length;
            console.log(usersNumber);
            let usersList;

            if (fav)
            {
                usersList = document.getElementById("favourite-users-list");
                if (usersNumber < 1)
                {
                    usersList.innerText = "No found favourite users";
                    return;
                }
            } else {
                usersList = document.getElementById("users-list");
                if (usersNumber < 1)
                {
                    usersList.innerText = "No found users";
                    return;
                }
            }
            usersList.innerHTML = "";

            let ids = [];

            for (let i = 0; i < data.length; i++) {

                createUserTab(fav, usersList, data, i);
                ids.push(data[i].user_id);
            }

            getStatuses(ids);

            interval = setInterval(function () {
                getStatuses(ids);
            }, 2000);

        })
        .catch(error => console.log(error))
}
