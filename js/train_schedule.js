function loadStations() {
    fetch('get_stations.php')
        .then(res => res.json())
        .then(data => {
            const fromList = document.getElementById('from-list');
            const toList = document.getElementById('to-list');
            fromList.innerHTML = '';
            toList.innerHTML = '';

            data.forEach(station => {
                const opt = document.createElement('option');
                opt.value = station.name;
                fromList.appendChild(opt.cloneNode(true));
                toList.appendChild(opt);
            });
        });
}

function loadSchedules(from = '', to = '', date = '') {
    const trainList = document.getElementById('train-list');
    trainList.innerHTML = '<p>Loading train schedules...</p>';

    const params = new URLSearchParams();
    if (from) params.append('from', from);
    if (to) params.append('to', to);
    if (date) params.append('date', date);

    fetch('get_schedules.php?' + params.toString())
        .then(res => res.json())
        .then(schedules => {
            trainList.innerHTML = '';

            if (schedules.length === 0) {
                trainList.innerHTML = '<p>No trains found.</p>';
                return;
            }

            schedules.forEach(s => {
                const card = document.createElement('div');
                card.className = 'train-card';
                card.innerHTML = `
                    <h3>Train #${s.train_id}</h3>
                    <p><strong>From:</strong> ${s.from_station}</p>
                    <p><strong>To:</strong> ${s.to_station}</p>
                    <p><strong>Departure:</strong> ${s.departure_time}</p>
                    <p><strong>Arrival:</strong> ${s.arrival_time}</p>
                    <p><strong>Frequency:</strong> ${s.frequency}</p>
                    <p><strong>Status:</strong> ${s.status}</p>
                    <button class="book-btn" onclick="bookTrain('${s.from_station}', '${s.to_station}', 0, 0, 10, 10)">Book Ticket</button>
                `;
                trainList.appendChild(card);
            });
        });
}

function bookTrain(from, to, x1, y1, x2, y2) {
    const url = `ticket.html?from=${from}&to=${to}&x1=${x1}&y1=${y1}&x2=${x2}&y2=${y2}`;
    window.location.href = url;
}

document.getElementById('search-btn').addEventListener('click', () => {
    const from = document.getElementById('from').value.trim();
    const to = document.getElementById('to').value.trim();
    const date = document.getElementById('date').value;
    loadSchedules(from, to, date);
});

loadStations();
loadSchedules(); // Load all schedules by default
