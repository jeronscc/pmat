document.getElementById('saveSaro').addEventListener('click', function() {
    const saroNumber = document.getElementById('saro_number').value;
    const budget = document.getElementById('budget').value;
    const year = document.getElementById('saro_year').value;
    const desc = document.getElementById('saroDesc').value;
    
    console.log('saroNumber:', saroNumber, 'budget:', budget, 'year:', saro_year);

    if (!saroNumber || !budget || !saro_year || !desc) {
        alert('All fields must be filled out.');
        return;
    }

    fetch('/api/add-saro-ilcdb', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',  // Helps Laravel parse JSON properly
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            saro_number: saroNumber,
            budget: budget,
            saro_year: year,
            saroDesc: desc
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errData => {
                console.error('Validation/Error:', errData);
                alert('Error: ' + JSON.stringify(errData));
                throw new Error('Request failed');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.message === 'SARO added successfully') {
            alert('SARO added successfully');
            fetchSaroData('');
            const addSaroModal = bootstrap.Modal.getInstance(document.getElementById("addSaroModal"));
            addSaroModal.hide();
            document.getElementById('saroForm').reset();
        } else {
            alert('Failed to add SARO');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding SARO');
    });
});