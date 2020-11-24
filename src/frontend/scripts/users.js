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
                for (let i = 0; i < usersNumber; i++) {
                    document.getElementById('status' + data[i].user_id).innerHTML = data[i].last_activity;
                }
            })
            .catch((error) => console.log(error))
    }
}