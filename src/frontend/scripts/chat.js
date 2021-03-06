function go() {
    let script = document.createElement('script');
    script.onload = function () {
        function resize()
        {
            let heights = window.innerHeight - 82;
            document.getElementsByClassName('chat-container')[0].style.height = heights + "px";
        }

        resize();

        window.onresize = function() {
            resize();
        };

        getUser();

        setInterval(function () {
            updateLastActivity();
        }, 3000);

        var textArea = document.getElementsByClassName("send-textarea")[0];
        textArea.addEventListener("focus",  enlargeSendTextArea);
        textArea.addEventListener("blur",  reduceSendTextArea);

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
            if(message !== '') {
                var data = {
                    'action': 'send',
                    'complement': 'message',
                    'sender_id': localStorage.getItem('current_user_id'),
                    'receiver_id': localStorage.getItem('another_user_id'),
                    'message': message,
                    'group': localStorage.getItem("group") === "group"
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
                        serveNewMessageWhenOtherDisplayed(data, 0);
                        sendInput.value = "";
                    })
                    .catch(error => console.log(error))
            }
            else
            {
                textArea.setAttribute("placeholder", "The message is empty");
            }
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

        function serveNewMessageWhenOtherDisplayed(data, i) {
            let chatHistory = document.getElementById('chat-history');
            let previous_date, current_date;

            let dateIndicatorsNumber = chatHistory.getElementsByClassName('date-indicator').length;
            previous_date = chatHistory.getElementsByClassName('date-indicator')[dateIndicatorsNumber - 1].innerHTML;
            previous_date = new Date(previous_date.substr(0, 4) + previous_date.substr(5, 3) + previous_date.substr(6, 3));
            current_date = new Date(data[i].sending_date);
            let current_date2 = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate());
            if (current_date2 > previous_date)
            {
                let dateIndicator = '<div class="date-indicator">' + current_date2.toLocaleString("pl-PL", {style:"day", day:"2-digit", style:"month", month:"2-digit", style:"year", year:"numeric"}) + '</div>';
                chatHistory.innerHTML += dateIndicator;
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
                    let chatHistory = document.getElementById('chat-history');

                    if (chatHistory.childNodes[0].innerText === "You haven't sent and received any message yet")
                    {
                        for (let i = 0; i < data.length; i++)
                        {
                            chatHistory.innerHTML = "";
                            serveNewMessage(data, i);

                        }
                    } else
                    {
                        for (let i = 0; i < data.length; i++)
                        {
                            serveNewMessageWhenOtherDisplayed(data, i);
                        }
                    }

                })
                .catch(error => console.log(error))
        }

        function getAllMessages() {

            let data = {
                'action' : 'get',
                'complement' : 'all_messages',
                'current_user_id' : localStorage.getItem('current_user_id'),
                'another_user_id' : localStorage.getItem('another_user_id'),
                'group' : localStorage.getItem("group") === "group"
            };
            fetch('../../api/chats/get/all_messages/', {
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

                    if (data.length === 0)
                    {
                        chatHistory.innerHTML = "<div class='no-message-prompt'>You haven't sent and received any message yet</div>";
                    }

                    for (let i = 0; i < data.length; i++)
                    {
                        serveNewMessage(data, i);
                    }

                    setInterval(function () {
                        getAllNewMessages();
                    }, 3000);

                })
                .catch(error => console.log(error))
        }
        getAllMessages();

        getAllUsers(1);

        document.getElementsByClassName('search-input')[0].addEventListener('change', function () {
            searchUsers(1);
        });

        document.getElementsByClassName('search-button')[0].addEventListener('click', function () {
            searchUsers(1);
        });
    };
    script.src = "../scripts/common.js";

    document.head.appendChild(script);

}