function emailPickerSetup(triggerButton, outputField) {
    const hasContacts = "contacts" in navigator && "ContactsManager" in window;
    const hasClipboard = "clipboard" in navigator && typeof navigator.clipboard.readText === "function";

    const triggerButtonElement = document.getElementById(triggerButton);
    const outputFieldElement = document.getElementById(outputField);

    if (hasContacts) {
        triggerButtonElement.addEventListener("click", async function () {
            try {
                const selectedContacts = await navigator.contacts.select(["email"], {multiple: false});
                if (selectedContacts.length > 0) {
                    const contact = selectedContacts[0];
                    if (contact.email && contact.email.length > 0 && contact.email[0]) {
                        outputFieldElement.value = contact.email[0];
                    } else {
                        alert("Vybraný kontakt nemá uloženou žádnou e-mailovou adresu.");
                    }
                }
            } catch (e) {
                if (e.name !== "AbortError") {
                    alert(e.toString());
                }
            }
        });
    } else if (hasClipboard) {
        triggerButtonElement.textContent = "Vložit ze schránky";
        triggerButtonElement.addEventListener("click", async function () {
            outputFieldElement.value = (await navigator.clipboard.readText()).trim();
            if (!outputFieldElement.checkValidity()) {
                outputFieldElement.reportValidity();
                outputFieldElement.value = "";
            } else {
                outputFieldElement.dispatchEvent(new Event("input", { bubbles: true }));
            }
        });
    } else {
        triggerButtonElement.style.display = "none";
    }
}
