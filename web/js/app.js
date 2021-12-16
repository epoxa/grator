const ajax = new XMLHttpRequest();

ajax.onloadend = function () {
    const json = JSON.parse(ajax.responseText);
    const div = document.getElementById('offer-text');
    div.innerHTML = json['text'];
    const commands = document.getElementById('commands-holder');
    commands.innerHTML = '';
    json['links'].forEach(function (link) {
        let btn = document.createElement('button');
        btn.innerHTML = link['caption'];
        btn.url = link['url'];
        btn.method = link['method'];
        btn.onclick = function () {
            ajax.open(btn.method, btn.url, true);
            ajax.setRequestHeader('Accept', 'application/json');
            ajax.send();
        }
        commands.insertBefore(btn, null);
    });
}

ajax.open('GET', '/', true);
ajax.setRequestHeader('Accept', 'application/json');
ajax.send();

function restart() {
    if (!confirm('Do you really want to restart it from scratch?')) return;
    ajax.open('POST', '/restart', true);
    ajax.setRequestHeader('Accept', 'application/json');
    ajax.send();
}

function logout() {
    window.location.href = '/logout';
}

