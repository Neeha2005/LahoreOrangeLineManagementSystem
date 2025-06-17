document.addEventListener('DOMContentLoaded', function() {
    // Handle book now buttons
    const bookButtons = document.querySelectorAll('.book-btn');
    
    bookButtons.forEach(button => {
        button.addEventListener('click', function() {
            const trainId = this.getAttribute('data-train-id');
            window.location.href = `book.php?train_id=${trainId}`;
        });
    });
    
    // Update greeting based on time of day
    updateGreeting();
    
    function updateGreeting() {
        const now = new Date();
        const hour = now.getHours();
        let greeting = '';
        
        if (hour < 12) {
            greeting = 'Good Morning';
        } else if (hour < 18) {
            greeting = 'Good Afternoon';
        } else {
            greeting = 'Good Evening';
        }
        
        const greetingElement = document.querySelector('.greeting h2');
        if (greetingElement) {
            greetingElement.textContent = greeting + '!';
        }
    }
    
    // Simulate real-time updates (would be replaced with actual AJAX in production)
    setInterval(() => {
        // In a real app, this would fetch updated train data
        console.log('Checking for updates...');
    }, 30000);
});