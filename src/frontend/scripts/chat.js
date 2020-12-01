function go() {

    setInterval(function () {
        updateLastActivity();
    }, 3000);

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

    document.getElementsByClassName("send-textarea")[0].addEventListener("focus",  enlargeSendTextArea);
    document.getElementsByClassName("send-textarea")[0].addEventListener("blur",  reduceSendTextArea);

    function enlargeSendTextArea() {
        document.getElementsByClassName("send-textarea")[0].setAttribute("rows", "4");
        document.getElementsByClassName("send-textarea")[0].setAttribute("placeholder", "");
    }

    function reduceSendTextArea() {
        document.getElementsByClassName("send-textarea")[0].setAttribute("rows", "1");
        document.getElementsByClassName("send-textarea")[0].setAttribute("placeholder", "Enter message");
    }

    function sendMessage() {
        var sendInput = document.getElementsByClassName("send-textarea")[0];
        var message = sendInput.value;

        var data = {
            'action' : 'send',
            'complement' : 'message',
            'sender_id' : localStorage.getItem('current_user_id'),
            'receiver_id' : localStorage.getItem('another_user_id'),
            'message' : message,
            'group' : localStorage.getItem("group") === "group"
        };
        fetch('../../api/chats/send/message/', {
            method: 'POST',
            headers: {
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(res => res.json())
            .then(data => {
                console.log(data);
                sendInput.value = "";
            })
            .catch(error => console.log(error))
    }
    document.getElementsByClassName("send-button")[0].addEventListener('click', sendMessage);

    function createNewMessage(data, i, current_date) {
        var chatHistory = document.getElementById('chat-history');

        let newMessage = '<div class="new-message">\n' +
            '<div class="message">' +
            '<div class="message-full-name"><img class="message-avatar" src="../../extras/img/avatar.png"><div>' + data[i].user_name + ' ' + data[i].surname + '</div></div>' +
            data[i].message_text +
            '<div class="hour-indicator">' + (current_date.getHours() < 10 ? '0' : '') + current_date.getHours() + ':' + (current_date.getMinutes() < 10 ? '0' : '') + current_date.getMinutes() + ':' + (current_date.getSeconds() < 10 ? '0' : '') + current_date.getSeconds() + '</div>\n' +
            '</div>\n' +
            '</div>';

        chatHistory.innerHTML += newMessage;

        if (data[i].receiver_id === localStorage.getItem('current_user_id'))
        {
            chatHistory.lastElementChild.classList.add("message-receiver");
        } else {

            chatHistory.lastElementChild.classList.add("message-sender");
        }
    }

    function serveNewMessage(data, i) {

        var chatHistory = document.getElementById('chat-history');
        var previous_date, current_date, current_date2;

        if (i === 0)
        {
            current_date = new Date(data[i].sending_date);
            current_date2 = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate());
            let dateIndicator = '<div class="date-indicator">' + current_date2.toLocaleDateString() + '</div>';
            chatHistory.innerHTML += dateIndicator;

        } else
        {
            previous_date = new Date(data[i-1].sending_date);
            previous_date = new Date(previous_date.getFullYear(), previous_date.getMonth(), previous_date.getDate());
            current_date = new Date(data[i].sending_date);
            current_date2 = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate());
            if (current_date2 > previous_date)
            {
                let dateIndicator = '<div class="date-indicator">' + current_date2.toLocaleString("pl-PL", {style:"day", day:"2-digit", style:"month", month:"2-digit", style:"year", year:"numeric"}) + '</div>';
                chatHistory.innerHTML += dateIndicator;
            }
        }
        createNewMessage(data, i, current_date);
    }

    function getAllNewMessages() {

        let data = {
            'action': 'get',
            'complement': 'all_new_messages',
            'current_user_id': localStorage.getItem('current_user_id'),
            'another_user_id': localStorage.getItem('another_user_id'),
            'group': localStorage.getItem("group") === "group"
        };
        fetch('../../api/chats/get/all_new_messages/', {
            method: 'POST',
            headers: {
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(res => res.json())
            .then(data => {
                console.log(data);
                var chatHistory = document.getElementById('chat-history');
                var previous_date, current_date;

                if (chatHistory.childNodes[0].innerText === "You haven't sent and received any message yet")
                {
                    for (let i = 0; i < data.length; i++)
                    {
                        chatHistory.innerHTML = "";
                        serveNewMessage(data, i);

                    }
                } else
                {

                }

            })
            .catch(error => console.log(error))
    }

}