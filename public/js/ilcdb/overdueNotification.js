document.addEventListener("DOMContentLoaded", function () {
    // Prevent modal from showing again if already seen in this session
    if (sessionStorage.getItem("overdueModalShown") === "true") {
        return;
    }

    fetch('/api/overdue-procurements')
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                let modalBody = document.getElementById('overdueProcurementList');
                modalBody.innerHTML = `
                    <div style="max-height: 400px; overflow-y: auto;"> <!-- Scrollable table -->
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>PR No</th>
                                    <th>Activity</th>
                                    <th>Date Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                            ${data.map(procurement => {
                                let displayDate = "N/A";

                                if (procurement.dt_submitted) {
                                    let dateObj = new Date(procurement.dt_submitted);
                                    if (!isNaN(dateObj.getTime())) {
                                        displayDate = dateObj.toISOString().split('T')[0];
                                    }
                                }

                                return `
                                    <tr>
                                        <td>${procurement.procurement_id}</td>
                                        <td>${procurement.activity}</td>
                                        <td>${displayDate}</td>  
                                    </tr>
                                `;
                            }).join('')}
                            </tbody>
                        </table>
                    </div>
                `;

                let overdueModal = new bootstrap.Modal(document.getElementById('overdueModal'));
                overdueModal.show();

                // Set sessionStorage to prevent repeated modal pop-ups
                sessionStorage.setItem("overdueModalShown", "true");
            }
        })
        .catch(error => console.error('Error fetching overdue procurements:', error));
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
