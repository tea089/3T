document.addEventListener('DOMContentLoaded', function() {
    // Budget type selection
    const typeOptions = document.querySelectorAll('.type-option');
    const budgetForm = document.getElementById('budget-form');
    const budgetTypeInput = document.getElementById('budget-type');
    
    if (typeOptions.length && budgetForm) {
        typeOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all options
                typeOptions.forEach(opt => opt.classList.remove('active'));
                
                // Add active class to clicked option
                this.classList.add('active');
                
                // Set the budget type value
                const type = this.dataset.type;
                budgetTypeInput.value = type;
                
                // Show the form
                budgetForm.classList.remove('hidden');
                
                // Apply template if not custom
                if (type !== 'custom') {
                    applyBudgetTemplate(type);
                }
            });
        });
    }
    
    // Toggle extra options
    const toggleExtras = document.querySelectorAll('.toggle-extra');
    toggleExtras.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const extraType = this.dataset.extra;
            const extraContent = this.closest('.form-group').querySelector(`.extra-content.${extraType}`);
            
            if (this.checked) {
                extraContent.classList.remove('hidden');
            } else {
                extraContent.classList.add('hidden');
            }
        });
    });
    
    // Add new budget item
    const addItemBtn = document.getElementById('add-item');
    if (addItemBtn) {
        addItemBtn.addEventListener('click', function() {
            const container = document.getElementById('budget-items-container');
            const items = container.querySelectorAll('.budget-item');
            const newItemId = items.length + 1;
            
            const newItem = document.createElement('div');
            newItem.className = 'budget-item';
            newItem.dataset.itemId = newItemId;
            newItem.innerHTML = `
                <div class="form-group">
                    <label for="item-name-${newItemId}">Item Name</label>
                    <input type="text" id="item-name-${newItemId}" name="items[${newItemId}][name]" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="item-amount-${newItemId}">Amount (RM)</label>
                        <input type="number" id="item-amount-${newItemId}" name="items[${newItemId}][amount]" min="0" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="item-necessity-${newItemId}">Necessity</label>
                        <select id="item-necessity-${newItemId}" name="items[${newItemId}][necessity]" required>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>
                
                <div class="extra-options">
                    <div class="form-group">
                        <label>
                            <input type="checkbox" class="toggle-extra" data-extra="date"> Include Date Range
                        </label>
                        <div class="extra-content date hidden">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="item-start-date-${newItemId}">Start Date</label>
                                    <input type="date" id="item-start-date-${newItemId}" name="items[${newItemId}][start_date]">
                                </div>
                                <div class="form-group">
                                    <label for="item-end-date-${newItemId}">End Date</label>
                                    <input type="date" id="item-end-date-${newItemId}" name="items[${newItemId}][end_date]">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="item-duration-${newItemId}">Duration (days)</label>
                                <input type="number" id="item-duration-${newItemId}" name="items[${newItemId}][duration_days]" min="1">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" class="toggle-extra" data-extra="aim"> Include Aim
                        </label>
                        <div class="extra-content aim hidden">
                            <input type="text" id="item-aim-${newItemId}" name="items[${newItemId}][aim]" placeholder="e.g., < RM20">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" class="toggle-extra" data-extra="time"> Include Time
                        </label>
                        <div class="extra-content time hidden">
                            <input type="text" id="item-time-${newItemId}" name="items[${newItemId}][time_range]" placeholder="e.g., 11am-1pm, 5pm-7pm">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" class="toggle-extra" data-extra="savings"> Include Savings
                        </label>
                        <div class="extra-content savings hidden">
                            <input type="number" id="item-savings-${newItemId}" name="items[${newItemId}][savings_percentage]" min="0" max="100" step="0.01" placeholder="Percentage">
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn remove-item">Remove Item</button>
            `;
            
            container.appendChild(newItem);
            
            // Add event listeners to new toggles
            newItem.querySelectorAll('.toggle-extra').forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const extraType = this.dataset.extra;
                    const extraContent = this.closest('.form-group').querySelector(`.extra-content.${extraType}`);
                    
                    if (this.checked) {
                        extraContent.classList.remove('hidden');
                    } else {
                        extraContent.classList.add('hidden');
                    }
                });
            });
            
            // Add remove item functionality
            newItem.querySelector('.remove-item').addEventListener('click', function() {
                container.removeChild(newItem);
            });
        });
    }
    
    // Remove budget item
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-item')) {
            const item = e.target.closest('.budget-item');
            item.parentNode.removeChild(item);
        }
    });
    
    // Replace the existing form submission handler with this:
    if (budgetForm) {
        budgetForm.addEventListener('submit', function(e) {
            e.preventDefault();
        
            // Calculate total amount from items
            let totalAmount = 0;
            const itemAmounts = document.querySelectorAll('input[name$="[amount]"]');
            itemAmounts.forEach(input => {
                totalAmount += parseFloat(input.value) || 0;
            });
        
            // Set the total amount
            const totalAmountInput = document.getElementById('total-amount');
            totalAmountInput.value = totalAmount.toFixed(2);
        
        // Submit the form
            this.submit();
        });
    }
    
    // Apply budget template based on type
    function applyBudgetTemplate(type) {
        const container = document.getElementById('budget-items-container');
        
        // Clear existing items except the first one
        const items = container.querySelectorAll('.budget-item');
        for (let i = 1; i < items.length; i++) {
            container.removeChild(items[i]);
        }
        
        // Reset the first item
        const firstItem = items[0];
        firstItem.querySelector('input[name$="[name]"]').value = '';
        firstItem.querySelector('input[name$="[amount]"]').value = '';
        firstItem.querySelector('select[name$="[necessity]"]').value = 'medium';
        
        // Reset all extra options
        firstItem.querySelectorAll('.toggle-extra').forEach(toggle => {
            toggle.checked = false;
            const extraType = toggle.dataset.extra;
            const extraContent = toggle.closest('.form-group').querySelector(`.extra-content.${extraType}`);
            extraContent.classList.add('hidden');
            
            // Clear values
            extraContent.querySelectorAll('input').forEach(input => {
                input.value = '';
            });
        });
        
        // Apply template based on type
        if (type === 'large') {
            // Large amount template (projects, trips, etc.)
            firstItem.querySelector('input[name$="[name]"]').value = 'Project Materials';
            firstItem.querySelector('input[name$="[amount]"]').value = '5000';
            
            // Enable date range
            const dateToggle = firstItem.querySelector('.toggle-extra[data-extra="date"]');
            dateToggle.checked = true;
            firstItem.querySelector('.extra-content.date').classList.remove('hidden');
            
            // Set default dates
            const today = new Date();
            const endDate = new Date();
            endDate.setDate(today.getDate() + 7);
            
            firstItem.querySelector('input[name$="[start_date]"]').valueAsDate = today;
            firstItem.querySelector('input[name$="[end_date]"]').valueAsDate = endDate;
            firstItem.querySelector('input[name$="[duration_days]"]').value = '7';
            
            // Add common large budget items
            const largeItems = [
                { name: 'Labor Costs', amount: '2000', necessity: 'high' },
                { name: 'Equipment Rental', amount: '1500', necessity: 'medium' },
                { name: 'Miscellaneous', amount: '500', necessity: 'low' }
            ];
            
            addTemplateItems(largeItems);
            
        } else if (type === 'small') {
            // Small amount template (daily spending, shopping, etc.)
            firstItem.querySelector('input[name$="[name]"]').value = 'Groceries';
            firstItem.querySelector('input[name$="[amount]"]').value = '150';
            firstItem.querySelector('select[name$="[necessity]"]').value = 'high';
            
            // Enable time
            const timeToggle = firstItem.querySelector('.toggle-extra[data-extra="time"]');
            timeToggle.checked = true;
            firstItem.querySelector('.extra-content.time').classList.remove('hidden');
            firstItem.querySelector('input[name$="[time_range]"]').value = '9am-11am';
            
            // Add common small budget items
            const smallItems = [
                { name: 'Dining Out', amount: '80', necessity: 'medium' },
                { name: 'Entertainment', amount: '50', necessity: 'low' },
                { name: 'Transportation', amount: '40', necessity: 'high' }
            ];
            
            addTemplateItems(smallItems);
        }
    }
    
    // Helper function to add template items
    function addTemplateItems(items) {
        items.forEach((item, index) => {
            const itemId = index + 2; // Start from 2 since first item is 1
            const container = document.getElementById('budget-items-container');
            
            const newItem = document.createElement('div');
            newItem.className = 'budget-item';
            newItem.dataset.itemId = itemId;
            newItem.innerHTML = `
                <div class="form-group">
                    <label for="item-name-${itemId}">Item Name</label>
                    <input type="text" id="item-name-${itemId}" name="items[${itemId}][name]" value="${item.name}" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="item-amount-${itemId}">Amount (RM)</label>
                        <input type="number" id="item-amount-${itemId}" name="items[${itemId}][amount]" value="${item.amount}" min="0" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="item-necessity-${itemId}">Necessity</label>
                        <select id="item-necessity-${itemId}" name="items[${itemId}][necessity]" required>
                            <option value="high" ${item.necessity === 'high' ? 'selected' : ''}>High</option>
                            <option value="medium" ${item.necessity === 'medium' ? 'selected' : ''}>Medium</option>
                            <option value="low" ${item.necessity === 'low' ? 'selected' : ''}>Low</option>
                        </select>
                    </div>
                </div>
                
                <button type="button" class="btn remove-item">Remove Item</button>
            `;
            
            container.appendChild(newItem);
            
            // Add remove item functionality
            newItem.querySelector('.remove-item').addEventListener('click', function() {
                container.removeChild(newItem);
            });
        });
    }
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('delete')) {
        if (!confirm('Are you sure you want to delete this budget? This cannot be undone.')) {
            e.preventDefault();
        }
    }
});

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

// ===== BACKGROUND SELECTION ===== //
document.querySelectorAll('.bg-option').forEach((option, index) => {
    // Set data-bg attribute with correct path
    option.setAttribute('data-bg', backgrounds[index]);
    
    // Set thumbnail image src (if you have thumbnails)
    const img = option.querySelector('img');
    if (img) {
        img.src = backgrounds[index].replace('.png', '_thumb.jpg');
    }

    option.addEventListener('click', function() {
        // Visual feedback
        document.querySelectorAll('.bg-option').forEach(opt => {
            opt.style.border = 'none';
        });
        this.style.border = '2px solid #c9a66b';

        // Set background with correct path
        const bgImage = this.getAttribute('data-bg');
        document.body.style.backgroundImage = `url('${bgImage}')`;
        localStorage.setItem('selectedBackground', bgImage);
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