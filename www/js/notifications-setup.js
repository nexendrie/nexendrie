function notificationsSetup() {
    if (!("Notification" in window)) {
        alert("Tvůj prohlížeč nepodporuje upozornění.");
    } else if (Notification.permission !== "granted") {
        Notification.requestPermission()
            .then(function (permission) {
                if (permission === "granted") {
                    new Notification("Nexendrie", {
                        lang: "cs-CZ",
                        body: "Upozornění zapnuty",
                    });
                    updateSetup();
                }
            });
    }
}

function updateSetup() {
    if (!("Notification" in window)) {
        const element = document.createElement("p");
        element.textContent = "Tvůj prohlížeč nepodporuje upozornění.";
        document.getElementById("notifications_browser").replaceWith(element);
    } else if (Notification.permission === "granted") {
        const element = document.createElement("p");
        element.textContent = "Upozornění jsou zapnuta v tomto prohlížeči.";
        document.getElementById("notifications_browser").replaceWith(element);
    }
}

updateSetup();
