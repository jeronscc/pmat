document.addEventListener("DOMContentLoaded", function () {
    // Prevent modal from showing again if already seen in this session
    if (sessionStorage.getItem("overdueModalShown") === "true") {
        return;
    }

    fetch("/api/dtc/overdue-procurements")
        .then((response) => response.json())
        .then((data) => {
            if (data.length > 0) {
                let container = document.getElementById(
                    "overdueProcurementList"
                );
                container.innerHTML = data
                    .map((procurement) => {
                        let displayDate = "N/A";
                        if (procurement.dt_submitted) {
                            let dateObj = new Date(procurement.dt_submitted);
                            if (!isNaN(dateObj.getTime())) {
                                displayDate = dateObj
                                    .toISOString()
                                    .split("T")[0];
                            }
                        }

                        return `
                    <div class="alert alert-warning d-flex justify-content-between align-items-center shadow-sm" role="alert">
                        <div>
                            <h6 class="mb-1"><strong>PR No:</strong> ${procurement.procurement_id}</h6>
                            <p class="mb-0"><strong>Activity:</strong> ${procurement.activity}</p>
                            <small><strong>Date Submitted:</strong> ${displayDate}</small>
                        </div>
                        <i class="bi bi-exclamation-circle-fill fs-3 text-danger"></i>
                    </div>
                `;
                    })
                    .join("");

                let overdueModal = new bootstrap.Modal(
                    document.getElementById("overdueModal")
                );
                overdueModal.show();

                // Prevent showing again in the same session
                sessionStorage.setItem("overdueModalShown", "true");
            }
        })
        .catch((error) =>
            console.error("Error fetching overdue procurements:", error)
        );
});

// Select logout form dynamically and reset session storage when user logs out
document.addEventListener("DOMContentLoaded", function () {
    let logoutForm = document.querySelector("form[action*='/logout']");
    if (logoutForm) {
        logoutForm.addEventListener("submit", function () {
            sessionStorage.removeItem("overdueModalShown");
        });
    }
});
