function sse() {
    if (!("Notification" in window) || Notification.permission !== "granted") {
        return;
    }
    const eventSource = new EventSource("/sse");
    eventSource.addEventListener("notification", function (event) {
        const data = JSON.parse(event.data);
        const notification = new Notification(data.title, {
            body: data.body,
            lang: data.lang,
            icon: data.icon,
            tag: data.tag,
        });
        if (data.targetUrl) {
            notification.onclick = function () {
                window.location.assign(data.targetUrl);
            };
        }
    });
}

window.onload = function () {
    sse();
};
