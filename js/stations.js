function openMapModal(lat, lng, name) {
    console.log("Opening map for:", name);

    const modal = document.getElementById("mapModal");
    const title = document.getElementById("mapTitle");
    const iframe = document.getElementById("mapFrame");

    if (modal && title && iframe) {
        const mapURL = `https://www.google.com/maps?q=${lat},${lng}&hl=es;z=14&output=embed`;
        iframe.src = mapURL;
        title.textContent = name;
        modal.classList.remove("hidden");
    } else {
        console.warn("Map modal elements not found.");
    }
}

function closeMapModal() {
    const modal = document.getElementById("mapModal");
    const iframe = document.getElementById("mapFrame");

    if (modal && iframe) {
        iframe.src = ""; // Clear iframe
        modal.classList.add("hidden"); // Hide modal
    }
}

window.openMapModal = openMapModal;
window.closeMapModal = closeMapModal;
