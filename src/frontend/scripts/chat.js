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

}