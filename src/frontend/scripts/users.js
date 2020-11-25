function go(current_user_id) {

    var usersNumber;

    function getUser(id = current_user_id) {
        fetch('../../api/users/get/user/' + id + "/", {
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

    getUser();

    setInterval(function () {
        updateLastActivity();
    }, 3000);

    function updateLastActivity(id = current_user_id) {
        fetch("../../api/users/update/last_activity/"+id+"/", {
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(res => res.json())
            .then(data => console.log(data))
            .catch(error => console.log(error))
    }

    function getStatuses(id = current_user_id) {
        fetch('../../api/users/get/statuses/'+id+"/", {
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


    function getAllUsers(id = current_user_id) {
        fetch('../../api/users/get/all_users/'+id+"/", {
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(res => res.json())
            .then(data => {
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

                    usersList.appendChild(newUser);

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