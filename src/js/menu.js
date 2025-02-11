// Get the menu button and side navigation elements
const menuIcon = document.getElementById("menu-icon");
const sideNav = document.getElementById("side-nav");

// Function to update sidenav position based on header height
function updateSidenavPosition() {
    const header = document.querySelector("header");

    if (header && sideNav) {
        const headerHeight = header.offsetHeight; // Get current header height
        sideNav.style.top = `${headerHeight}px`; // Apply it to sidenav
    }
}

// Update sidenav position on load and resize
window.addEventListener("load", updateSidenavPosition);
window.addEventListener("resize", updateSidenavPosition);

// Toggle side navigation on menu icon click
menuIcon.addEventListener("click", function () {
    sideNav.classList.toggle("active");  // Toggle "active" class to show/hide the side nav
});

// Close the side navigation if clicked outside
document.addEventListener("click", function (event) {
    // Check if the clicked area is outside the side nav and the menu button
    if (!sideNav.contains(event.target) && !menuIcon.contains(event.target)) {
        sideNav.classList.remove("active");  // Remove "active" class to hide the side nav
    }
});
