// === Ticket Preview Script ===

document.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  const from = params.get("from");
  const to = params.get("to");
  const scheduleId = params.get("schedule_id");

  // Validate required params
  if (!from || !to || !scheduleId) {
    showError("Missing required ticket information.");
    return;
  }

  // Populate static info
  document.getElementById("from").textContent = from;
  document.getElementById("to").textContent = to;

  // Get fare, distance, departure time from server
  fetchTicketData(from, to, scheduleId);
});

// Fetch ticket info from backend
function fetchTicketData(from, to, scheduleId) {
  fetch(`ticket.php?from=${from}&to=${to}&schedule_id=${scheduleId}`)
    .then(response => response.json())
    .then(data => {
      if (!data || data.error) {
        showError(data?.error || "Failed to load ticket data.");
        return;
      }

      const travelDate = new Date().toISOString().split('T')[0]; // today's date
      document.getElementById("departure").textContent = data.departure_time;
      document.getElementById("distance").textContent = data.distance;
      document.getElementById("fare").textContent = data.fare.toFixed(2);
      document.getElementById("travel-date").textContent = travelDate;

      setupConfirmButton(scheduleId, travelDate, data.fare);
    })
    .catch(error => {
      console.error("Fetch error:", error);
      showError("Error fetching ticket data.");
    });
}

// Handle confirm ticket button click
function setupConfirmButton(scheduleId, travelDate, fare) {
  const confirmBtn = document.getElementById("confirm-btn");

  confirmBtn.addEventListener("click", () => {
    const payload = {
      user_id: 1, // Replace with real session/user ID
      schedule_id: scheduleId,
      travel_date: travelDate
    };

    fetch('ticket.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
      .then(response => response.json())
      .then(result => {
        if (result && result.message) {
          document.getElementById("status").textContent = result.message;
        } else {
          showError("Failed to confirm ticket.");
        }
      })
      .catch(error => {
        console.error("Booking error:", error);
        showError("Error confirming ticket.");
      });
  });
}

// Display error message to user
function showError(message) {
  const status = document.getElementById("status");
  status.textContent = message;
  status.style.color = "red";
}
