const webShareLink = document.getElementById("share-link-web");
if (webShareLink) {
    if (!navigator.canShare) {
        webShareLink.style.display = "none";
    }

    webShareLink.addEventListener("click", function (event) {
        event.preventDefault();
        const data = {
            url: document.getElementById("share-link-web").dataset.url
        };
        if (!navigator.canShare) {
            alert("Tvůj prohlížeš nepodporuje Web Share API");
            return;
        }
        if (!navigator.canShare(data)) {
            alert("Nejde sdílet odkaz");
            return;
        }
        navigator.share(data)
            .catch(function (e) {
                alert("Chyba: " + e);
            });
    });
}
