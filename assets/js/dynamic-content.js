// Dynamic Content Loader for HTML Version
// This script loads content from localStorage and updates the page

document.addEventListener('DOMContentLoaded', function() {
    loadDynamicContent();
});

function loadDynamicContent() {
    try {
        const settings = JSON.parse(localStorage.getItem('magusaTaxiSettings') || '{}');
        
        // Update hero section
        if (settings.heroTitle) {
            const titleElement = document.querySelector('.header h1');
            if (titleElement) {
                titleElement.textContent = settings.heroTitle;
            }
            
            // Update page title
            document.title = settings.heroTitle + ' | Güvenilir Kıbrıs Taxi Hizmeti';
        }
        
        if (settings.heroSubtitle) {
            const subtitleElement = document.querySelector('.header .subtitle');
            if (subtitleElement) {
                subtitleElement.textContent = settings.heroSubtitle;
            }
        }
        
        // Update blog content
        if (settings.blogContent) {
            const blogElement = document.querySelector('.blog-content');
            if (blogElement) {
                // Keep the existing structure but update the first paragraphs
                const existingContent = blogElement.innerHTML;
                const newContent = settings.blogContent.replace(/\n\n/g, '</p><p>');
                
                // Replace only the first two paragraphs
                const updatedContent = existingContent.replace(
                    /<p>Mağusa Taxi olarak[\s\S]*?<\/p>\s*<p>Müşteri memnuniyeti[\s\S]*?<\/p>/,
                    '<p>' + newContent + '</p>'
                );
                
                blogElement.innerHTML = updatedContent;
            }
        }
        
        // Update contact buttons
        if (settings.whatsappNumber) {
            const whatsappButtons = document.querySelectorAll('a[href*="wa.me"]');
            whatsappButtons.forEach(button => {
                const cleanNumber = settings.whatsappNumber.replace(/[^0-9]/g, '');
                button.href = `https://wa.me/${cleanNumber}?text=Merhaba, taxi hizmeti almak istiyorum.`;
            });
            
            // Update footer
            const footerWhatsapp = document.querySelector('.footer-contact p:has(.fa-whatsapp)');
            if (footerWhatsapp) {
                footerWhatsapp.innerHTML = `<i class="fab fa-whatsapp"></i> ${settings.whatsappNumber}`;
            }
        }
        
        if (settings.phoneNumber) {
            const phoneButtons = document.querySelectorAll('a[href^="tel:"]');
            phoneButtons.forEach(button => {
                button.href = `tel:${settings.phoneNumber}`;
            });
            
            // Update footer
            const footerPhone = document.querySelector('.footer-contact p:has(.fa-phone)');
            if (footerPhone) {
                footerPhone.innerHTML = `<i class="fas fa-phone"></i> ${settings.phoneNumber}`;
            }
            
            // Update structured data
            updateStructuredData(settings.phoneNumber);
        }
        
        // Update meta tags
        if (settings.metaDescription) {
            updateMetaTag('description', settings.metaDescription);
            updateMetaTag('og:description', settings.metaDescription);
            updateMetaTag('twitter:description', settings.metaDescription);
        }
        
        if (settings.metaKeywords) {
            updateMetaTag('keywords', settings.metaKeywords);
        }
        
    } catch (error) {
        console.log('No saved settings found or error loading settings:', error);
    }
}

function updateMetaTag(name, content) {
    // Update meta description
    let metaTag = document.querySelector(`meta[name="${name}"]`) || 
                  document.querySelector(`meta[property="${name}"]`);
    
    if (metaTag) {
        metaTag.setAttribute('content', content);
    } else if (name === 'description') {
        // Create meta description if it doesn't exist
        metaTag = document.createElement('meta');
        metaTag.setAttribute('name', 'description');
        metaTag.setAttribute('content', content);
        document.head.appendChild(metaTag);
    }
}

function updateStructuredData(phoneNumber) {
    const scriptTag = document.querySelector('script[type="application/ld+json"]');
    if (scriptTag) {
        try {
            const data = JSON.parse(scriptTag.textContent);
            data.telephone = phoneNumber;
            scriptTag.textContent = JSON.stringify(data);
        } catch (error) {
            console.log('Error updating structured data:', error);
        }
    }
}

// Function to reset content to defaults (useful for testing)
function resetToDefaults() {
    localStorage.removeItem('magusaTaxiSettings');
    location.reload();
}

// Export function for console use
window.resetToDefaults = resetToDefaults;
