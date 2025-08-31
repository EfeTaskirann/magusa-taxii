// Mağusa Taxi Website JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeSlider();
    initializeContactButtons();
    initializeLazyLoading();
    initializeAccessibility();
    initializeLanguageSelector();
});

// Image Slider Functionality
let currentSlide = 0;
let slides = [];
let slideInterval;

function initializeSlider() {
    slides = document.querySelectorAll('.slide');
    const navDots = document.querySelectorAll('.nav-dot');
    
    if (slides.length === 0) return;
    
    // Start automatic sliding
    startAutoSlide();
    
    // Pause on hover
    const sliderContainer = document.querySelector('.slider-container');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', stopAutoSlide);
        sliderContainer.addEventListener('mouseleave', startAutoSlide);
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            previousSlide();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
        }
    });
    
    // Touch/swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    if (sliderContainer) {
        sliderContainer.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        });
        
        sliderContainer.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });
    }
    
    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                nextSlide();
            } else {
                previousSlide();
            }
        }
    }
}

function goToSlide(slideIndex) {
    // Remove active class from current slide and nav dot
    slides[currentSlide].classList.remove('active');
    const currentNavDot = document.querySelector('.nav-dot.active');
    if (currentNavDot) {
        currentNavDot.classList.remove('active');
    }
    
    // Set new current slide
    currentSlide = slideIndex;
    
    // Add active class to new slide and nav dot
    slides[currentSlide].classList.add('active');
    const navDots = document.querySelectorAll('.nav-dot');
    if (navDots[currentSlide]) {
        navDots[currentSlide].classList.add('active');
    }
    
    // Restart auto slide timer
    stopAutoSlide();
    startAutoSlide();
}

function nextSlide() {
    const nextIndex = (currentSlide + 1) % slides.length;
    goToSlide(nextIndex);
}

function previousSlide() {
    const prevIndex = (currentSlide - 1 + slides.length) % slides.length;
    goToSlide(prevIndex);
}

function startAutoSlide() {
    if (slides.length <= 1) return;
    
    slideInterval = setInterval(function() {
        nextSlide();
    }, 5000); // 5 seconds
}

function stopAutoSlide() {
    if (slideInterval) {
        clearInterval(slideInterval);
    }
}

// Contact Buttons Enhancement
function initializeContactButtons() {
    const contactButtons = document.querySelectorAll('.contact-btn');
    
    contactButtons.forEach(button => {
        // Add click tracking for analytics
        button.addEventListener('click', function(e) {
            const buttonType = this.classList.contains('whatsapp-btn') ? 'WhatsApp' : 'Phone';
            console.log(`Contact button clicked: ${buttonType}`);
            
            // Add a small animation feedback
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
        
        // Add hover sound effect (optional)
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
}

// Lazy Loading for Images
function initializeLazyLoading() {
    const images = document.querySelectorAll('img[loading="lazy"]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    }
}

// Accessibility Improvements
function initializeAccessibility() {
    // Add ARIA labels to slider navigation
    const navDots = document.querySelectorAll('.nav-dot');
    navDots.forEach((dot, index) => {
        dot.setAttribute('aria-label', `Go to slide ${index + 1}`);
        dot.setAttribute('role', 'button');
        dot.setAttribute('tabindex', '0');
        
        // Allow keyboard navigation
        dot.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                goToSlide(index);
            }
        });
    });
    
    // Add ARIA labels to contact buttons
    const whatsappBtn = document.querySelector('.whatsapp-btn');
    const phoneBtn = document.querySelector('.phone-btn');
    
    if (whatsappBtn) {
        whatsappBtn.setAttribute('aria-label', 'WhatsApp ile iletişime geç');
    }
    
    if (phoneBtn) {
        phoneBtn.setAttribute('aria-label', 'Telefon ile ara');
    }
    
    // Skip to content link for screen readers
    const skipLink = document.createElement('a');
    skipLink.href = '#main-content';
    skipLink.textContent = 'Ana içeriğe geç';
    skipLink.className = 'skip-link';
    skipLink.style.cssText = `
        position: absolute;
        top: -40px;
        left: 6px;
        background: #000;
        color: #fff;
        padding: 8px;
        text-decoration: none;
        border-radius: 4px;
        z-index: 1001;
    `;
    
    skipLink.addEventListener('focus', function() {
        this.style.top = '6px';
    });
    
    skipLink.addEventListener('blur', function() {
        this.style.top = '-40px';
    });
    
    document.body.insertBefore(skipLink, document.body.firstChild);
    
    // Add main content ID
    const mainContent = document.querySelector('.main-content');
    if (mainContent) {
        mainContent.id = 'main-content';
    }
}

// Performance monitoring
function logPerformance() {
    if ('performance' in window) {
        window.addEventListener('load', function() {
            setTimeout(function() {
                const perfData = performance.timing;
                const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
                console.log(`Page load time: ${pageLoadTime}ms`);
            }, 0);
        });
    }
}

// Error handling for images
document.addEventListener('error', function(e) {
    if (e.target.tagName === 'IMG') {
        console.log('Image failed to load:', e.target.src);
        e.target.src = 'assets/images/placeholder.jpg'; // Fallback image
        e.target.alt = 'Resim yüklenemedi';
    }
}, true);

// Initialize performance monitoring
logPerformance();

// SEO and Analytics helper functions
function trackPageView() {
    // Google Analytics or other tracking code can be added here
    console.log('Page view tracked for Mağusa Taxi');
}

function trackContactClick(type) {
    // Track contact button clicks for analytics
    console.log(`Contact clicked: ${type}`);
}

// Language selector initialization
function initializeLanguageSelector() {
    const languageButtons = document.querySelectorAll('.language-btn');
    
    languageButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Add click animation feedback
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
}

// Call page view tracking
trackPageView();
