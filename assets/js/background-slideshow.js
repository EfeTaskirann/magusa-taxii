// Background Slideshow Controller for Mağusa Taxi - DEBUG VERSION

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Background slideshow initializing...');
    setTimeout(initializeBackgroundSlideshow, 1000); // Wait 1 second for images to load
});

let currentBgSlide = 0;
let bgSlides = [];
let bgSlideInterval;
let backgroundImages = [];

function initializeBackgroundSlideshow() {
    console.log('🔍 Looking for slider images...');
    
    // Try multiple selectors to find images
    let sliderImages = document.querySelectorAll('.slider-container .slide img');
    
    if (sliderImages.length === 0) {
        console.log('⚠️ No images found in .slider-container, trying alternative selectors...');
        sliderImages = document.querySelectorAll('.slide img');
    }
    
    if (sliderImages.length === 0) {
        console.log('⚠️ No slider images found, trying all images...');
        sliderImages = document.querySelectorAll('img');
    }
    
    console.log(`📊 Found ${sliderImages.length} images`);
    
    if (sliderImages.length === 0) {
        console.log('❌ No images found at all, creating fallback background');
        createFallbackBackground();
        return;
    }
    
    // Extract image sources and debug
    sliderImages.forEach((img, index) => {
        console.log(`🖼️ Image ${index + 1}:`, img.src);
        if (img.src && 
            !img.src.includes('placeholder') && 
            !img.src.includes('data:') &&
            !img.src.includes('default-taxi') &&
            img.src !== window.location.href) {
            backgroundImages.push(img.src);
            console.log(`✅ Added to background: ${img.src}`);
        } else {
            console.log(`❌ Skipped: ${img.src}`);
        }
    });
    
    console.log(`🎯 Total background images: ${backgroundImages.length}`);
    
    // If no valid images, use fallback
    if (backgroundImages.length === 0) {
        console.log('🔄 No valid images found, creating fallback background');
        createFallbackBackground();
        return;
    }
    
    // Create background slideshow container
    createBackgroundSlideshow();
    
    // Preload images
    preloadBackgroundImages();
    
    // Start slideshow
    startBackgroundSlideshow();
    
    console.log('🎉 Background slideshow initialized successfully!');
}

function createBackgroundSlideshow() {
    console.log('🏗️ Creating background slideshow container...');
    
    // Remove any existing background slideshow
    const existing = document.querySelector('.background-slideshow');
    if (existing) {
        existing.remove();
    }
    
    // Create slideshow container
    const slideshowContainer = document.createElement('div');
    slideshowContainer.className = 'background-slideshow';
    
    // Create slides
    backgroundImages.forEach((imageSrc, index) => {
        const slide = document.createElement('div');
        slide.className = `bg-slide ${index === 0 ? 'active' : ''}`;
        slide.style.backgroundImage = `url('${imageSrc}')`;
        
        console.log(`🎭 Creating slide ${index + 1} with image: ${imageSrc}`);
        
        slideshowContainer.appendChild(slide);
    });
    
    // Add to body (as first child)
    document.body.insertBefore(slideshowContainer, document.body.firstChild);
    
    // Store slides reference
    bgSlides = document.querySelectorAll('.bg-slide');
    
    console.log(`✅ Created ${bgSlides.length} background slides`);
}

function createFallbackBackground() {
    console.log('🎨 Creating fallback background...');
    
    // Remove any existing background slideshow
    const existing = document.querySelector('.background-slideshow');
    if (existing) {
        existing.remove();
    }
    
    // Create a beautiful gradient fallback with animated taxi icons
    const fallbackContainer = document.createElement('div');
    fallbackContainer.className = 'background-slideshow';
    fallbackContainer.innerHTML = `
        <div class="bg-slide active" style="
            background: linear-gradient(45deg, #2c3e50 0%, #3498db 50%, #f39c12 100%);
            position: relative;
        ">
            <div style="
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                opacity: 0.1;
                font-size: 8rem;
                display: flex;
                align-items: center;
                justify-content: center;
                animation: taxiFloat 15s ease-in-out infinite;
            ">🚕</div>
        </div>
        <div class="bg-slide" style="
            background: linear-gradient(-45deg, #e74c3c 0%, #8e44ad 50%, #2980b9 100%);
            position: relative;
        ">
            <div style="
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                opacity: 0.1;
                font-size: 8rem;
                display: flex;
                align-items: center;
                justify-content: center;
                animation: taxiFloat 15s ease-in-out infinite reverse;
            ">🚖</div>
        </div>
        <div class="bg-slide" style="
            background: linear-gradient(135deg, #27ae60 0%, #16a085 50%, #f39c12 100%);
            position: relative;
        ">
            <div style="
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                opacity: 0.1;
                font-size: 8rem;
                display: flex;
                align-items: center;
                justify-content: center;
                animation: taxiFloat 15s ease-in-out infinite;
            ">🚗</div>
        </div>
    `;
    
    // Add keyframe animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes taxiFloat {
            0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); }
            25% { transform: translateY(-30px) rotate(2deg) scale(1.1); }
            50% { transform: translateY(0px) rotate(0deg) scale(0.9); }
            75% { transform: translateY(30px) rotate(-2deg) scale(1.1); }
        }
    `;
    document.head.appendChild(style);
    
    document.body.insertBefore(fallbackContainer, document.body.firstChild);
    
    // Use fallback slides
    bgSlides = fallbackContainer.querySelectorAll('.bg-slide');
    backgroundImages = ['fallback1', 'fallback2', 'fallback3'];
    
    startBackgroundSlideshow();
    
    console.log('🎨 Fallback background created and started');
}

function preloadBackgroundImages() {
    console.log('⏳ Preloading background images...');
    backgroundImages.forEach((imageSrc, index) => {
        const img = new Image();
        img.onload = () => console.log(`✅ Preloaded image ${index + 1}`);
        img.onerror = () => console.log(`❌ Failed to preload image ${index + 1}: ${imageSrc}`);
        img.src = imageSrc;
    });
}

function startBackgroundSlideshow() {
    if (bgSlides.length <= 1) {
        console.log('⚠️ Only 1 slide, not starting slideshow');
        return;
    }
    
    console.log('▶️ Starting background slideshow...');
    
    bgSlideInterval = setInterval(() => {
        nextBackgroundSlide();
    }, 6000); // 6 seconds per slide
}

function nextBackgroundSlide() {
    if (bgSlides.length === 0) return;
    
    // Remove active class from current slide
    bgSlides[currentBgSlide].classList.remove('active');
    
    // Move to next slide
    const prevSlide = currentBgSlide;
    currentBgSlide = (currentBgSlide + 1) % bgSlides.length;
    
    // Add active class to new slide
    bgSlides[currentBgSlide].classList.add('active');
    
    console.log(`🔄 Slide transition: ${prevSlide} → ${currentBgSlide}`);
}

function stopBackgroundSlideshow() {
    if (bgSlideInterval) {
        clearInterval(bgSlideInterval);
        console.log('⏹️ Background slideshow stopped');
    }
}

// Pause background slideshow when user interacts with front slider
document.addEventListener('click', function(e) {
    if (e.target.closest('.slider-container') || e.target.classList.contains('nav-dot')) {
        console.log('👆 User interacted with slider, pausing background');
        stopBackgroundSlideshow();
        // Restart after 10 seconds
        setTimeout(() => {
            console.log('🔄 Restarting background slideshow after interaction');
            startBackgroundSlideshow();
        }, 10000);
    }
});

// Performance optimization: pause slideshow when page is not visible
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        console.log('👁️ Page hidden, stopping slideshow');
        stopBackgroundSlideshow();
    } else {
        console.log('👁️ Page visible, starting slideshow');
        startBackgroundSlideshow();
    }
});

// Debug functions - call from console
window.debugBackgroundSlideshow = function() {
    console.log('🔧 DEBUG INFO:');
    console.log('Background images:', backgroundImages);
    console.log('Background slides:', bgSlides.length);
    console.log('Current slide:', currentBgSlide);
    console.log('Slider running:', !!bgSlideInterval);
};

// Manual trigger for testing
window.testBackgroundSlideshow = function() {
    console.log('🧪 Testing background slideshow manually...');
    initializeBackgroundSlideshow();
};

// Export functions for external use
window.backgroundSlideshow = {
    next: nextBackgroundSlide,
    stop: stopBackgroundSlideshow,
    start: startBackgroundSlideshow,
    debug: window.debugBackgroundSlideshow,
    test: window.testBackgroundSlideshow
};