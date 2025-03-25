document.addEventListener("DOMContentLoaded", function () {
    let loggedInUserId = document.getElementById("loggedInUserId").value; // Get logged-in user ID

    document.querySelectorAll(".edit-btn").forEach(button => {
        button.addEventListener("click", function () {
            let userId = this.getAttribute("data-userid");
            let username = this.getAttribute("data-username");
            let email = this.getAttribute("data-email");
            let role = this.getAttribute("data-role");

            console.log("Editing User ID:", userId);
            console.log("Logged-in User ID:", loggedInUserId);
            console.log("Username:", username);  // ✅ Added to check if username is being passed

            // ✅ Make sure `currentUsername` is updated
            document.getElementById("currentUsername").textContent = username; 

            // Set values in the modal fields
            document.getElementById("editUserId").value = userId;
            document.getElementById("editUsername").value = username;
            document.getElementById("editEmail").value = email;

            // Check if the logged-in user is editing their own account
            if (parseInt(userId) === parseInt(loggedInUserId)) {
                console.log("Disabling role field for logged-in user."); // Debugging
                document.getElementById("editRole").classList.add("d-none"); // Hide dropdown
                document.getElementById("editRoleLockedContainer").classList.remove("d-none"); // Show locked role
                document.getElementById("editRoleLocked").textContent = role; // Display role with lock icon
                document.getElementById("editRoleHidden").value = role; // Store value for form submission
            } else {
                console.log("Enabling role field for other users."); // Debugging
                document.getElementById("editRole").classList.remove("d-none"); // Show dropdown
                document.getElementById("editRoleLockedContainer").classList.add("d-none"); // Hide locked role
                document.getElementById("editRole").value = role;
            }
        });
    });

    document.getElementById("saveChangesBtn").addEventListener("click", function() {
        let userId = document.getElementById("editUserId").value;
        let form = document.getElementById("editUserForm");
        
        if (userId) {
            form.submit();
        } else {
            alert("User ID is missing.");
        }
    });

    // Clear error messages when modal is closed
    let editUserModal = document.getElementById("editUserModal");
    editUserModal.addEventListener("hidden.bs.modal", function () {
        // Remove validation error messages
        document.querySelectorAll(".invalid-feedback").forEach(el => el.innerHTML = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

        // Reset form fields
        document.getElementById("editUserForm").reset();

        // Reset role visibility
        document.getElementById("editRole").classList.remove("d-none"); // Show dropdown for next use
        document.getElementById("editRoleLockedContainer").classList.add("d-none"); // Hide locked role
    });
});


