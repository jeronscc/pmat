// Get the menu button and side navigation elements
const menuIcon = document.getElementById("menu-icon");
const sideNav = document.getElementById("side-nav");

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
