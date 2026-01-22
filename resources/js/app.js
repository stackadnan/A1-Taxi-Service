import './bootstrap';
import * as Turbo from '@hotwired/turbo';

// Configure Turbo for smooth SPA-like navigation
Turbo.start();

// Add smooth page transition effect
document.addEventListener('turbo:before-visit', () => {
    document.body.style.opacity = '1';
});

document.addEventListener('turbo:visit', () => {
    document.body.style.transition = 'opacity 0.2s ease-in-out';
    document.body.style.opacity = '0.95';
});

document.addEventListener('turbo:load', () => {
    document.body.style.opacity = '1';
    
    // Reinitialize any JavaScript that needs to run on each page
    initializePageScripts();
});

// Function to reinitialize page-specific scripts after Turbo navigation
function initializePageScripts() {
    // Update active sidebar states
    updateSidebarActiveStates();
    
    // Reinitialize mobile sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        // Remove old listeners by cloning
        const newToggle = sidebarToggle.cloneNode(true);
        sidebarToggle.parentNode.replaceChild(newToggle, sidebarToggle);
        
        newToggle.addEventListener('click', function() {
            const sidebar = document.querySelector('aside[aria-label="Sidebar"]');
            if (sidebar) {
                sidebar.classList.toggle('hidden');
            }
        });
    }

    // Reinitialize user menu
    const userMenuButton = document.getElementById('userMenuButton');
    const userMenu = document.getElementById('userMenu');
    if (userMenuButton && userMenu) {
        const newButton = userMenuButton.cloneNode(true);
        userMenuButton.parentNode.replaceChild(newButton, userMenuButton);
        
        newButton.addEventListener('click', function() {
            userMenu.classList.toggle('hidden');
        });
    }

    // Reinitialize logout link
    const userLogout = document.getElementById('userLogout');
    if (userLogout) {
        const newLogout = userLogout.cloneNode(true);
        userLogout.parentNode.replaceChild(newLogout, userLogout);
        
        newLogout.addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('logout-form');
            if (form) {
                form.submit();
            }
        });
    }
}

// Function to update sidebar active states based on current URL
function updateSidebarActiveStates() {
    const currentPath = window.location.pathname;
    const sidebar = document.getElementById('sidebar');
    
    if (!sidebar) return;
    
    // Get all navigation links in sidebar
    const navLinks = sidebar.querySelectorAll('nav a, .absolute a');
    
    // Remove all active states first
    navLinks.forEach(link => {
        link.classList.remove('bg-gray-100', 'font-semibold');
        const svg = link.querySelector('svg');
        if (svg) {
            svg.classList.remove('text-indigo-600');
            svg.classList.add('text-gray-500');
        }
    });
    
    // Find the active link by comparing href with current path
    let activeLink = null;
    let maxMatchLength = 0;
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (!href) return;
        
        // Extract path from href (remove domain if present)
        let linkPath = href;
        try {
            const url = new URL(href, window.location.origin);
            linkPath = url.pathname;
        } catch (e) {
            // href is already a path
        }
        
        // Check if current path starts with link path (for nested routes)
        if (currentPath === linkPath || currentPath.startsWith(linkPath + '/') || currentPath.startsWith(linkPath + '?')) {
            // Use longest match to handle nested routes correctly
            if (linkPath.length > maxMatchLength) {
                maxMatchLength = linkPath.length;
                activeLink = link;
            }
        }
    });
    
    // Apply active state to the matched link
    if (activeLink) {
        activeLink.classList.add('bg-gray-100', 'font-semibold');
        const svg = activeLink.querySelector('svg');
        if (svg) {
            svg.classList.remove('text-gray-500');
            svg.classList.add('text-indigo-600');
        }
    }
}

// Handle progress bar for loading
document.addEventListener('turbo:before-fetch-request', () => {
    // Show loading indicator if needed
    const loadingBar = document.createElement('div');
    loadingBar.id = 'turbo-progress-bar';
    loadingBar.style.cssText = 'position:fixed;top:0;left:0;width:0;height:3px;background:linear-gradient(90deg,#4f46e5,#7c3aed);z-index:99999;transition:width 0.3s ease;';
    document.body.appendChild(loadingBar);
    setTimeout(() => {
        loadingBar.style.width = '70%';
    }, 100);
});

document.addEventListener('turbo:before-render', () => {
    const loadingBar = document.getElementById('turbo-progress-bar');
    if (loadingBar) {
        loadingBar.style.width = '100%';
    }
});

document.addEventListener('turbo:render', () => {
    const loadingBar = document.getElementById('turbo-progress-bar');
    if (loadingBar) {
        setTimeout(() => {
            loadingBar.remove();
        }, 300);
    }
});
