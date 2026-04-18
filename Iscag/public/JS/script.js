// Mobile Navigation Toggle
function toggleMobileNav() {
    const mobileNav = document.getElementById('mobileNav');
    mobileNav.classList.toggle('open');
}

// Close mobile nav when clicking outside
document.addEventListener('click', function(event) {
    const mobileNav = document.getElementById('mobileNav');
    const navToggle = document.querySelector('.dev-nav-toggle');
    
    if (!mobileNav.contains(event.target) && !navToggle.contains(event.target)) {
        mobileNav.classList.remove('open');
    }
});

// Close mobile nav when window is resized to desktop
window.addEventListener('resize', function() {
    if (window.innerWidth > 991) {
        const mobileNav = document.getElementById('mobileNav');
        mobileNav.classList.remove('open');
    }
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Simple Zakat Calculator (for donation page)
function calculateZakat() {
    const cash = parseFloat(document.querySelector('input[placeholder="0.00"]')?.value) || 0;
    const gold = parseFloat(document.querySelectorAll('input[placeholder="0.00"]')[1]?.value) || 0;
    const business = parseFloat(document.querySelectorAll('input[placeholder="0.00"]')[2]?.value) || 0;
    const debts = parseFloat(document.querySelectorAll('input[placeholder="0.00"]')[3]?.value) || 0;
    
    const totalAssets = cash + gold + business - debts;
    const nisab = 85 * 3000; // Approximate nisab in PHP (85 grams of gold)
    
    if (totalAssets >= nisab) {
        const zakat = totalAssets * 0.025; // 2.5%
        alert(`Your Zakat obligation is: ₱${zakat.toFixed(2)}`);
    } else {
        alert('Your assets are below the nisab threshold. No Zakat is due.');
    }
}

// Add event listener for Zakat calculator button
document.addEventListener('DOMContentLoaded', function() {
    const zakatButton = document.querySelector('.btn-send');
    if (zakatButton && window.location.pathname.includes('donation.html')) {
        zakatButton.addEventListener('click', function(e) {
            e.preventDefault();
            calculateZakat();
        });
    }
});