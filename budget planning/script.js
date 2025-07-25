// ===== BACKGROUND IMAGE CYCLING ===== //
const backgrounds = [
    './img/index/background_spring.png',
    './img/index/background_summer.png',
    './img/index/background_fall.png',
    './img/index/background_winter.png'
];
let currentBgIndex = 0;
let isTransitioning = false;

function changeBackground() {
    if (isTransitioning) return;
    isTransitioning = true;
    // Remove manual selection, resume cycling mode
    localStorage.removeItem('selectedBackground');
    currentBgIndex = (currentBgIndex + 1) % backgrounds.length;
    document.body.style.backgroundImage = `url('${backgrounds[currentBgIndex]}')`;
    setTimeout(() => isTransitioning = false, 2000);
}

// Click handlers for title elements
document.querySelector('.title-sign')?.addEventListener('click', function() {
    if (!isTransitioning) {
        this.classList.add('clicked');
        setTimeout(() => this.classList.remove('clicked'), 200);
        changeBackground();
    }
});

document.querySelector('.sticky-title')?.addEventListener('click', function() {
    if (!isTransitioning) {
        this.classList.add('clicked');
        setTimeout(() => this.classList.remove('clicked'), 200);
        changeBackground();
    }
});

// ===== STICKY NAVIGATION ===== //
const buttonRow = document.querySelector('.button-row');
const stickyNav = document.getElementById('stickyNav');

window.addEventListener('scroll', () => {
    const buttonRowBottom = buttonRow?.getBoundingClientRect().bottom || 0;
    stickyNav?.classList.toggle('visible', buttonRowBottom < 0);
});

// ===== BUTTON FUNCTIONALITY ===== //
document.querySelectorAll('.pixel-button').forEach(button => {
    button.addEventListener('click', (e) => {
        button.classList.add('clicked');
        setTimeout(() => button.classList.remove('clicked'), 200);

        switch(button.textContent.trim()) {
            case "SIGN UP": window.location.href = "signUp.html"; break;
            case "FAQ": window.location.href = "faq.html"; break;
            case "LOG IN": window.location.href = "login.html"; break;
            case "Home": window.location.href = "homepage.php"; break;
            case "BUDGET": window.location.href = "./budget-app/homepage.php"; break;
        }
    });
});

// ===== BUTTON FUNCTIONALITY ===== //
document.querySelectorAll('.create-button').forEach(button => {
    button.addEventListener('click', (e) => {
        button.classList.add('clicked');
        setTimeout(() => button.classList.remove('clicked'), 200);

        switch(button.textContent.trim()) {
            case "Create New Budget": window.location.href = "./budget-app/create-budget.php"; break;
            case "Create your first budget": window.location.href = "./budget-app/create-budget.php"; break;
        }
    });
});

// ===== BUTTON FUNCTIONALITY ===== //
document.querySelectorAll('.budget-button').forEach(button => {
    button.addEventListener('click', (e) => {
        // Add clicked class for visual feedback
        button.classList.add('clicked');
        setTimeout(() => button.classList.remove('clicked'), 200);

        // Get the budget ID from data attribute
        const budgetId = button.dataset.budgetId;
        
        // Handle different button actions
        switch(button.textContent.trim()) {
            case "View":
                window.location.href = `./budget-app/view-budget.php?id=${budgetId}`;
                break;
            case "Edit":
                window.location.href = `./budget-app/edit-budget.php?id=${budgetId}`;
                break;
            case "Record":
                window.location.href = `./budget-app/record-spending.php?budget_id=${budgetId}`;
                break;
            case 'DELETE':
                if(confirm('Are you sure you want to delete this budget?')) {
                    window.location.href = `./budget-app/delete-budget.php?id=${budgetId}`;
                }
                break;
            // Add cases for other buttons as needed
        }
    });
});

// ===== SCROLL TO TOP/BOTTOM ===== //
document.querySelectorAll('.back-to-top').forEach(button => {
    button.addEventListener('click', () => {
        button.classList.add('clicked');
        setTimeout(() => button.classList.remove('clicked'), 200);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});

document.querySelectorAll('.down-to-bottom').forEach(button => {
    button.addEventListener('click', () => {
        button.classList.add('clicked');
        setTimeout(() => button.classList.remove('clicked'), 200);
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
    });
});

// ===== SETTINGS SIDEBAR ===== //
const settingsButton = document.querySelector('.settings-button');
const overlay = document.getElementById('overlay');
const rightSidebar = document.getElementById('rightSidebar');
const closeSidebar = document.getElementById('closeSidebar');

settingsButton?.addEventListener('click', (e) => {
    e.preventDefault();
    overlay.classList.add('active');
    rightSidebar.classList.add('active');
    document.body.style.overflow = 'hidden';
});

closeSidebar?.addEventListener('click', () => {
    overlay.classList.remove('active');
    rightSidebar.classList.remove('active');
    document.body.style.overflow = '';
});

overlay?.addEventListener('click', () => {
    overlay.classList.remove('active');
    rightSidebar.classList.remove('active');
    document.body.style.overflow = '';
});

// ===== BACKGROUND SELECTION IN SETTINGS ===== //
document.querySelectorAll('.bg-option').forEach(option => {
    option.addEventListener('click', function() {
        // Remove active border from all options
        document.querySelectorAll('.bg-option').forEach(opt => {
            opt.style.border = 'none';
        });

        // Add border to selected option
        this.style.border = '2px solid #c9a66b';

        // Change background image
        const bgImage = this.getAttribute('data-bg');
        document.body.style.backgroundImage = `url('${bgImage}')`;

        // Save to localStorage to remember preference
        localStorage.setItem('selectedBackground', bgImage);
    });
});

// Load saved background on page load
window.addEventListener('DOMContentLoaded', () => {
    const savedBg = localStorage.getItem('selectedBackground');
    if (savedBg) {
        document.body.style.backgroundImage = `url('${savedBg}')`;

        // Highlight the saved option in settings
        document.querySelectorAll('.bg-option').forEach(option => {
            if (option.getAttribute('data-bg') === savedBg) {
                option.style.border = '2px solid #c9a66b';
            }
        });
    } else {
        document.body.style.backgroundImage = `url('${backgrounds[0]}')`;
    }
});

// Add this to your script.js or in a <script> tag
document.addEventListener('DOMContentLoaded', function() {
    const backButton = document.getElementById('back-button');
    
    backButton.addEventListener('click', function() {
        // Check if there's history to go back to
        if (document.referrer && document.referrer.indexOf(window.location.hostname) !== -1) {
            // If coming from within the same site, go back
            window.history.back();
        } else {
            // If coming from external site or direct link, go to home
            window.location.href = 'homepage.php';
        }
    });
    
    // Optional: Style the button based on history
    if (history.length <= 1) {
        backButton.style.opacity = '0.5';
        backButton.title = "No previous page available";
    }
});

// star rating
const stars = document.querySelectorAll('.star');
const ratingInput = document.getElementById('rating-input');
let selectedRating = 0;

stars.forEach(star => {
    star.addEventListener('click', () => {
        selectedRating = star.getAttribute('data-value');
        ratingInput.value = selectedRating;
        updateStars(selectedRating);
    });

    star.addEventListener('mouseover', () => {
        updateStars(star.getAttribute('data-value'));
    });

    star.addEventListener('mouseout', () => {
        updateStars(selectedRating);
    });
});

function updateStars(rating) {
    stars.forEach(star => {
        if (star.getAttribute('data-value') <= rating) {
            star.classList.add('selected');
        } else {
            star.classList.remove('selected');
        }
    });
}

// ===== CUSTOM SCROLLBAR ===== //
const scrollbarThumb = document.querySelector('.scrollbar-thumb');
const scrollbarTrack = document.querySelector('.scrollbar-track');
let isDragging = false;

function updateThumb() {
    if (!scrollbarThumb || !scrollbarTrack) return;

    const scrollHeight = document.documentElement.scrollHeight;
    const clientHeight = document.documentElement.clientHeight;
    const scrollableHeight = scrollHeight - clientHeight;

    // Calculate thumb height (minimum 20px)
    const thumbHeight = Math.max(20, (clientHeight / scrollHeight) * clientHeight);
    scrollbarThumb.style.height = `${thumbHeight}px`;

    // Update thumb position
    const scrollTop = document.documentElement.scrollTop;
    const thumbPosition = (scrollTop / scrollableHeight) * (clientHeight - thumbHeight);
    scrollbarThumb.style.top = `${thumbPosition}px`;
}

// Initialize scrollbar
function initScrollbar() {
    updateThumb();

    // Thumb dragging
    scrollbarThumb?.addEventListener('mousedown', (e) => {
        isDragging = true;
        e.preventDefault();
    });

    document.addEventListener('mousemove', (e) => {
        if (!isDragging || !scrollbarTrack) return;

        const trackRect = scrollbarTrack.getBoundingClientRect();
        const thumbHeight = scrollbarThumb.offsetHeight;
        const clickY = e.clientY - trackRect.top;
        const clickPercent = Math.min(1, Math.max(0, clickY / trackRect.height));

        const scrollPos = clickPercent * (document.documentElement.scrollHeight - window.innerHeight);
        window.scrollTo(0, scrollPos);
    });

    document.addEventListener('mouseup', () => {
        isDragging = false;
    });

    // Track click
    scrollbarTrack?.addEventListener('click', (e) => {
        if (e.target === scrollbarThumb) return;
        const trackRect = scrollbarTrack.getBoundingClientRect();
        const clickY = e.clientY - trackRect.top;
        const clickPercent = clickY / trackRect.height;

        const scrollPos = clickPercent * (document.documentElement.scrollHeight - window.innerHeight);
        window.scrollTo(0, scrollPos);
    });

    // Handle window resize and scroll
    window.addEventListener('resize', updateThumb);
    window.addEventListener('scroll', updateThumb);

    // Mouse wheel scrolling
    document.addEventListener('wheel', (e) => {
        // Prevent default scrolling behavior
        e.preventDefault();

        // Calculate the new scroll position
        const scrollAmount = e.deltaY; // This gets the scroll amount
        const currentScroll = document.documentElement.scrollTop;
        const newScroll = currentScroll + scrollAmount;

        // Scroll to the new position
        window.scrollTo({
            top: newScroll,
            behavior: 'auto' // Use 'smooth' if you want smooth scrolling
        });
    }, { passive: false }); // Important: passive must be false to allow preventDefault
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', initScrollbar);   