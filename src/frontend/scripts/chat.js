function go() {

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
            })
            .catch(error => console.log(error))
    }
    document.getElementsByClassName("send-button")[0].addEventListener('click', sendMessage);
}