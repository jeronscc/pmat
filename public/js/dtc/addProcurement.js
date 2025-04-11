document.addEventListener("DOMContentLoaded", function () {
    document
        .getElementById("addProcurement")
        .addEventListener("click", function () {
            const category = document.getElementById("category").value;
            const prNumber = document.getElementById("pr-number").value;
            const saroNumber = document.getElementById("saro-number").value;
            const prYear = document.getElementById("pr-year").value;
            const activity = document.getElementById("activity").value;
            const description = document.getElementById("description").value;
            const prAmount = parseFloat(
                document.getElementById("pr-amount").value
            ); 
            const approvedBudget = parseFloat(
                document.getElementById("approved-budget").value
            );

            if (
                !category ||
                !prNumber ||
                !saroNumber ||
                !prYear ||
                !activity ||
                !description ||
                !prAmount ||
                !approvedBudget
            ) {
                alert("All fields must be filled out.");
                return;
            }

            fetch("/api/dtc/add-procurement-dtc", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: JSON.stringify({
                    category: category,
                    pr_number: prNumber,
                    saro_number: saroNumber,
                    pr_year: prYear,
                    activity: activity,
                    description: description,
                    pr_amount: prAmount,
                    approved_budget: approvedBudget,
                }),
            })
                .then((response) => {
                    if (!response.ok) {
                        return response.json().then((errData) => {
                            console.error("Validation/Error:", errData);
                            alert("Error: " + JSON.stringify(errData));
                            throw new Error("Request failed");
                        });
                    }
                    return response.json();
                })
                .then((data) => {
                    if (data.message === "Procurement added successfully") {
                        //alert("New Procurement added successfully");
                        const procurementModal = bootstrap.Modal.getInstance(
                            document.getElementById("procurementModal")
                        );
                        procurementModal.hide();

                        // Redirect based on category
                        if (category === "SVP") {
                            window.location.href =
                                "/dtcSVP?pr_number=" +
                                encodeURIComponent(prNumber) +
                                "&activity=" +
                                encodeURIComponent(activity);
                        } else if (category === "Honoraria") {
                            window.location.href =
                                "/dtcHonoraria?pr_number=" +
                                encodeURIComponent(prNumber) +
                                "&activity=" +
                                encodeURIComponent(activity);
                        } else if (category === "Daily travel expense") {
                            window.location.href =
                                "/dtcDTE?pr_number=" +
                                encodeURIComponent(prNumber) +
                                "&activity=" +
                                encodeURIComponent(activity);
                        }
                    } else {
                        alert("Failed to add procurement");
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("An error occurred while adding procurement");
                });
        });

    // OPTIONS FOR EXISTING SARO IN PROC MODAL
    const yearSelect = document.getElementById("pr-year");
    const saroSelect = document.getElementById("saro-number");

    // Function to fetch SARO data based on selected year
    function fetchSaroByYear(year) {
        fetch(`/api/dtc/fetch-saro-dtc?year=${year}`)
            .then((response) => response.json())
            .then((data) => {
                saroSelect.innerHTML =
                    '<option value="" disabled selected>Select SARO Number</option>'; // Clear existing options

                if (data.length > 0) {
                    data.forEach((saro) => {
                        const option = document.createElement("option");
                        option.value = saro.saro_no;
                        option.textContent = saro.saro_no;
                        saroSelect.appendChild(option);
                    });
                } else {
                    const emptyOption = document.createElement("option");
                    emptyOption.value = "";
                    emptyOption.textContent =
                        "No SARO records found for the selected year";
                    saroSelect.appendChild(emptyOption);
                }
            })
            .catch((error) =>
                console.error("Error fetching SARO data:", error)
            );
    }

    // Listen for the year selection change
    yearSelect.addEventListener("change", function () {
        const selectedYear = this.value;
        if (selectedYear) {
            fetchSaroByYear(selectedYear); // Fetch SARO numbers for the selected year
        }
    });
});

// edit procurement redirection
document.addEventListener("DOMContentLoaded", function () {
    // Attach event listener to the modal's Edit button
    document
        .querySelector("#procurementDetailsModal .btn-primary")
        .addEventListener("click", function () {
            // Get values from the modal's spans
            var prNumber = document
                .getElementById("modalProcurementNo")
                .textContent.trim();
            var activity = document
                .getElementById("modalActivity")
                .textContent.trim();
            var category = document
                .getElementById("modalProcurementCategory")
                .textContent.trim();

            // Determine the redirect URL based on the category
            var redirectUrl = "/dtcSVP"; // default for SVP
            if (category === "Honoraria") {
                redirectUrl = "/dtcHonoraria";
            } else if (category === "Daily travel expense") {
                redirectUrl = "/dtcDTE";
            }

            // Append query parameters to the URL
            redirectUrl +=
                "?pr_number=" +
                encodeURIComponent(prNumber) +
                "&activity=" +
                encodeURIComponent(activity);

            // Redirect to the desired URL
            window.location.href = redirectUrl;
        });
});

// PR MODAL SELECTION CHANGE
// Listen for category selection change
document.getElementById("category").addEventListener("change", function () {
    const category = this.value;

    // Reset form fields before changing
    resetForm();

    // Get current year
    const currentYear = new Date().getFullYear();
    // Generate PR number with random 5 digits
    const generatePRNumber = () =>
        `PROC-${currentYear}-${Math.floor(10000 + Math.random() * 90000)}`;

    if (category === "SVP") {
        // Retain original form for SVP
        document.getElementById("activityLabel").innerText = "ACTIVITY";
        document
            .getElementById("pr-number")
            .setAttribute("placeholder", "Enter PR Number");
        document.getElementById("pr-number").removeAttribute("readonly");
        document
            .getElementById("activity")
            .setAttribute("placeholder", "Enter Activity");
        document
            .getElementById("description")
            .setAttribute("placeholder", "Enter Description");
    } else if (category === "Honoraria") {
        // Modify form for Honoraria
        document.getElementById("activityLabel").innerText = "NAME OF SPEAKER";
        document.getElementById("pr-number").value = generatePRNumber(); // Auto-generate PR number
        document.getElementById("pr-number").setAttribute("readonly", "true"); // Make PR Number non-editable
        document
            .getElementById("activity")
            .setAttribute("placeholder", "Enter name of the resource speaker");
        document
            .getElementById("description")
            .setAttribute("placeholder", "Enter title of the training");
    } else if (category === "Daily travel expense") {
        // Modify form for Other expense
        document.getElementById("activityLabel").innerText =
            "NAME OF TRAVELLER";
        document.getElementById("pr-number").value = generatePRNumber(); // Auto-generate PR number
        document.getElementById("pr-number").setAttribute("readonly", "true"); // Make PR Number non-editable
        document
            .getElementById("activity")
            .setAttribute("placeholder", "Enter name of traveller");
        document
            .getElementById("description")
            .setAttribute("placeholder", "Enter the purpose of travel");
    }
});

// Reset the form fields
// Reset the form fields
function resetForm() {
    document.getElementById("activity").value = "";
    document.getElementById("description").value = "";
    document.getElementById("pr-number").value = ""; // Clear PR Number
    document.getElementById("pr-year").value = "";
    document.getElementById("saro-number").value = "";
    document.getElementById("pr-amount").value = ""; // Reset PR Amount
    document.getElementById("approved-budget").value = ""; // Reset Approved Budget
}
