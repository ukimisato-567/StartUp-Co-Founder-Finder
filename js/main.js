// keeps the join-request number in the menu up to date (checks every 5 seconds)
function setBadge(id, count) {
    var badge = document.getElementById(id);
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline-block' : 'none';
    }
}

function checkCounts() {
    fetch(ROOT + 'ajax/notify_counts.php')
        .then(function (res) { return res.json(); })
        .then(function (data) { setBadge('reqCount', data.requests); });
}
setInterval(checkCounts, 5000);
