// TUNU DELIVERY - Main JavaScript
// Created by Kadili Dev
// Email: kadiliy17@gmail.com | Contact: 0618240534

// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
        });
    }
    
    // Initialize cart
    updateCartDisplay();
    
    // Add to cart buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id');
            const itemName = this.getAttribute('data-name');
            const itemPrice = parseFloat(this.getAttribute('data-price'));
            const quantityInput = document.getElementById('quantity-' + itemId);
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
            
            addToCart(itemId, itemName, itemPrice, quantity);
        });
    });
});

// Cart Management
function getCart() {
    const cart = localStorage.getItem('cart');
    return cart ? JSON.parse(cart) : [];
}

function saveCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartDisplay();
}

function addToCart(itemId, itemName, itemPrice, quantity) {
    let cart = getCart();
    
    // Check if item already exists
    const existingItem = cart.find(item => item.id === itemId);
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            id: itemId,
            name: itemName,
            price: itemPrice,
            quantity: quantity
        });
    }
    
    saveCart(cart);
    showNotification('Imeongezwa kwenye cart!', 'success');
}

function removeFromCart(itemId) {
    let cart = getCart();
    cart = cart.filter(item => item.id !== itemId);
    saveCart(cart);
    
    // Reload cart page if on cart page
    if (window.location.pathname.includes('cart.php')) {
        location.reload();
    }
}

function updateQuantity(itemId, newQuantity) {
    if (newQuantity < 1) return;
    
    let cart = getCart();
    const item = cart.find(item => item.id === itemId);
    
    if (item) {
        item.quantity = newQuantity;
        saveCart(cart);
        
        // Update cart page display
        if (window.location.pathname.includes('cart.php')) {
            updateCartPageDisplay();
        }
    }
}

function updateCartDisplay() {
    const cart = getCart();
    const cartCount = document.querySelector('.cart-count');
    
    if (cartCount) {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = totalItems;
        
        if (totalItems > 0) {
            cartCount.style.display = 'flex';
        } else {
            cartCount.style.display = 'none';
        }
    }
}

function updateCartPageDisplay() {
    const cart = getCart();
    const cartTableBody = document.getElementById('cart-items');
    const cartTotalElement = document.getElementById('cart-total');
    
    if (!cartTableBody) return;
    
    // Clear existing items
    cartTableBody.innerHTML = '';
    
    if (cart.length === 0) {
        cartTableBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Cart yako ni tupu</td></tr>';
        if (cartTotalElement) {
            cartTotalElement.textContent = 'TSH 0';
        }
        return;
    }
    
    let total = 0;
    
    cart.forEach(item => {
        const subtotal = item.price * item.quantity;
        total += subtotal;
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.name}</td>
            <td>TSH ${item.price.toLocaleString()}</td>
            <td>
                <input type="number" 
                       class="quantity-input" 
                       value="${item.quantity}" 
                       min="1" 
                       onchange="updateQuantity('${item.id}', this.value)">
            </td>
            <td>TSH ${subtotal.toLocaleString()}</td>
            <td>
                <button class="btn btn-danger" onclick="removeFromCart('${item.id}')">
                    Ondoa
                </button>
            </td>
        `;
        
        cartTableBody.appendChild(row);
    });
    
    if (cartTotalElement) {
        cartTotalElement.textContent = 'TSH ' + total.toLocaleString();
    }
}

function clearCart() {
    localStorage.removeItem('cart');
    updateCartDisplay();
}

// Quantity Controls
function decreaseQuantity(itemId) {
    const input = document.getElementById('quantity-' + itemId);
    if (input && input.value > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

function increaseQuantity(itemId) {
    const input = document.getElementById('quantity-' + itemId);
    if (input) {
        input.value = parseInt(input.value) + 1;
    }
}

// Payment Method Selection
function selectPaymentMethod(method) {
    // Remove selected class from all options
    document.querySelectorAll('.payment-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    // Add selected class to clicked option
    const selectedOption = document.querySelector(`[data-method="${method}"]`);
    if (selectedOption) {
        selectedOption.classList.add('selected');
    }
    
    // Set hidden input value
    const paymentInput = document.getElementById('payment-method');
    if (paymentInput) {
        paymentInput.value = method;
    }
    
    // Show/hide phone number input for digital payments
    const phoneGroup = document.getElementById('payment-phone-group');
    if (phoneGroup) {
        if (method !== 'cash_on_delivery') {
            phoneGroup.style.display = 'block';
            document.getElementById('payment-phone').required = true;
        } else {
            phoneGroup.style.display = 'none';
            document.getElementById('payment-phone').required = false;
        }
    }
}

// Form Validation
function validateCheckoutForm() {
    const form = document.getElementById('checkout-form');
    if (!form) return false;
    
    const cart = getCart();
    if (cart.length === 0) {
        showNotification('Cart yako ni tupu!', 'danger');
        return false;
    }
    
    const paymentMethod = document.getElementById('payment-method').value;
    if (!paymentMethod) {
        showNotification('Tafadhali chagua njia ya malipo', 'danger');
        return false;
    }
    
    return true;
}

// Notification System
function showNotification(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '250px';
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// Search Functionality
function searchMenu(query) {
    const menuItems = document.querySelectorAll('.menu-item');
    const searchQuery = query.toLowerCase();
    
    menuItems.forEach(item => {
        const itemName = item.querySelector('h3').textContent.toLowerCase();
        const itemDescription = item.querySelector('p').textContent.toLowerCase();
        
        if (itemName.includes(searchQuery) || itemDescription.includes(searchQuery)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Filter by Category
function filterByCategory(categoryId) {
    const menuItems = document.querySelectorAll('.menu-item');
    
    if (categoryId === 'all') {
        menuItems.forEach(item => {
            item.style.display = 'block';
        });
        return;
    }
    
    menuItems.forEach(item => {
        const itemCategory = item.getAttribute('data-category');
        
        if (itemCategory === categoryId) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Initialize on page load
window.addEventListener('load', function() {
    if (window.location.pathname.includes('cart.php')) {
        updateCartPageDisplay();
    }
});
