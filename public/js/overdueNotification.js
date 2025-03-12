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
                                let submittedDates = [
                                    procurement.dt_submitted1,
                                    procurement.dt_submitted2,
                                    procurement.dt_submitted3,
                                    procurement.dt_submitted4,
                                    procurement.dt_submitted5,
                                    procurement.dt_submitted6
                                ].filter(date => date) // Remove null values
                                  .map(date => new Date(date).toISOString().split('T')[0]); // Format to YYYY-MM-DD

                                let displayDate = submittedDates.length > 0 ? submittedDates[0] : 'N/A';

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
