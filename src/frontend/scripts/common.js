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
