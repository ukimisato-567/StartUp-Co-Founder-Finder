// loads the request lists and refreshes them every 4 seconds
function loadLists() {
    fetch('ajax/requests_list.php?type=received')
        .then(function (res) { return res.text(); })
        .then(function (html) { document.getElementById('received').innerHTML = html; });

    fetch('ajax/requests_list.php?type=sent')
        .then(function (res) { return res.text(); })
        .then(function (html) { document.getElementById('sent').innerHTML = html; });
}

// accept / reject buttons
document.addEventListener('click', function (e) {
    if (!e.target.classList.contains('req-btn')) return;

    var body = 'id=' + e.target.dataset.id + '&action=' + e.target.dataset.action;
    fetch('ajax/request_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body
    })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.error) alert(data.error);
            loadLists();
        });
});

loadLists();
setInterval(loadLists, 4000);
