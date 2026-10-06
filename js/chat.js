// group chat: asks the server for new messages every 2 seconds
var lastId = 0;
var box = document.getElementById('messages');

function loadMessages() {
    fetch('ajax/chat_get.php?team_id=' + TEAM_ID + '&last_id=' + lastId)
        .then(function (res) { return res.json(); })
        .then(function (messages) {
            if (messages.length == 0) return;

            var empty = document.getElementById('empty');
            if (empty) empty.remove();

            messages.forEach(function (m) {
                var div = document.createElement('div');
                div.className = 'm' + (m.mine ? ' mine' : '');

                var name = document.createElement('b');
                name.textContent = m.name;
                var text = document.createElement('span');
                text.textContent = m.message;
                var time = document.createElement('small');
                time.textContent = ' ' + m.time;

                div.appendChild(name);
                div.appendChild(text);
                div.appendChild(time);
                box.appendChild(div);
                lastId = m.id;
            });
            box.scrollTop = box.scrollHeight;
        });
}

document.getElementById('chatForm').addEventListener('submit', function (e) {
    e.preventDefault();
    var input = document.getElementById('text');
    if (input.value.trim() == '') return;

    fetch('ajax/chat_send.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'team_id=' + TEAM_ID + '&message=' + encodeURIComponent(input.value)
    })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.error) alert(data.error);
            input.value = '';
            loadMessages();
        });
});

loadMessages();
setInterval(loadMessages, 2000);
