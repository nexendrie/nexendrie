function emailPickerSetup(triggerButton, outputField) {
    const supported = "contacts" in navigator && "ContactsManager" in window;
    if (!supported) {
        document.getElementById(triggerButton).style.display = "none";
    }
    document.getElementById(triggerButton).addEventListener("click", async function () {
        try {
            const selectedContacts = await navigator.contacts.select(["email"], {multiple: false});
            selectedContacts.forEach(function (contact) {
                if (contact.email) {
                    document.getElementById(outputField).value = contact.email[0];
                }
            });
        } catch (e) {
            alert(e.toString());
        }
    });
}
