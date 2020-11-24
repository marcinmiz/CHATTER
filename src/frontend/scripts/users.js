function go(current_user_id) {
    setInterval(function () {
        updateLastActivity();}, 3000);

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
}