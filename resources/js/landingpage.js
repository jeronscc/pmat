document.addEventListener('DOMContentLoaded', function() {
    var acc = document.getElementsByClassName("accordion");
    for (var i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var panel = this.nextElementSibling;
            var icon = this.querySelector(".dropdown-icon");

            // Toggle panel visibility
            if (panel.classList.contains("show")) {
                panel.classList.remove("show");
                icon.innerHTML = "&#x25BC;";  // Down arrow
            } else {
                panel.classList.add("show");
                icon.innerHTML = "&#x25B2;";  // Up arrow
            }
        });
    }
});
