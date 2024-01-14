const link = document.getElementById("share-link-fediverse");
if (link) {
    link.addEventListener("click", function (event) {
        document.getElementById("fediverse-share").showModal();
        event.preventDefault();
    });
}

function fediverseShareClose() {
    document.getElementById("fediverse-share").close();
}

function fediverseShareSubmit(event) {
    event.preventDefault();
    const platform = document.getElementById("fediverse-share-platform").value;
    const instance = document.getElementById("fediverse-share-instance").value;
    const url = document.getElementById("share-link-fediverse").dataset.url;
    document.getElementById("fediverse-share").close();
    switch (platform) {
        case "mastodon":
            window.open(`${instance}/share?text=${url}`);
            break;
        case "friendica":
        case "diaspora":
            window.open(`${instance}/bookmarklet?url=${url}&jump-doclose`);
            break;
        case "pleroma":
            window.open(`${instance}/share?message=${url}`);
            break;
        case "gnusocial":
            window.open(`${instance}/action=newnotice&status_textarea=${url}`);
            break;
    }
}
