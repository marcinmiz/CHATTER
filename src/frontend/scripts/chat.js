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

}