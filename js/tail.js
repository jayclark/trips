function getLog(timer) {
    var url = "tail.php";
    request.open("POST", url, true);
    request.onreadystatechange = updatePage;
    request.send(null);
    startTail(timer);

    var button_html = "<center>\n";
	button_html += "\t<button onclick=\"getLog('start');\" disabled>Start Log</button>\n";
        button_html += "\t<button onclick=\"stopTail();\">Stop Log</button>\n";
        button_html += "\t[STATUS: Running]\n<center>\n";

    buttonDiv = document.getElementById("buttons");
    buttonDiv.innerHTML=button_html;
}
 
function startTail(timer) {
    if (timer == "stop") {
        stopTail();
    } else {
        t= setTimeout("getLog()",1000);
    }
}
 
function stopTail() {
    clearTimeout(t);
    var button_html = "<center>\n";
	button_html += "\t<button onclick=\"getLog('start');\">Start Log</button>\n";
        button_html += "\t<button onclick=\"stopTail();\" disabled>Stop Log</button>\n";
        button_html += "\t[STATUS: Stopped]\n<center>\n";

    buttonDiv = document.getElementById("buttons");
    buttonDiv.innerHTML=button_html;
}
 
function updatePage() {
    if (request.readyState == 4) {
        if (request.status == 200) {
            var currentLogValue = request.responseText.split("\n");
            eval(currentLogValue);
            logDiv = document.getElementById("log");
            var logLine = ' ';
            for (i=0; i < currentLogValue.length - 1; i++) {
                logLine += currentLogValue[i] + "<br/>\n";
            }
            logDiv.innerHTML=logLine;
            scrollToBottom("log");
        } else
            alert("Error! Request status is " + request.status);
    }
}

function scrollToBottom(elm_id)
{
    var elm = document.getElementById(elm_id);
    try
        {
        elm.scrollTop = elm.scrollHeight;
        }
    catch(e)
        {
        var f = document.createElement("input");
        if (f.setAttribute) f.setAttribute("type","text")
        if (elm.appendChild) elm.appendChild(f);
        f.style.width = "0px";
        f.style.height = "0px";
        if (f.focus) f.focus();
        if (elm.removeChild) elm.removeChild(f);
        }
}
