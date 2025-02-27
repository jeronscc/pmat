// for id and activity name in adding proc
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const prNumber = urlParams.get('pr_number');
    const activityName = urlParams.get('activity');

    console.log('prNumber:', prNumber, 'activityName:', activityName); // Debug log

    if (prNumber) {
        document.getElementById('prNumber').textContent = prNumber;
    }
    if (activityName) {
        document.getElementById('activityName').textContent = activityName;
    }
});

