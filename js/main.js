// ========================================
// PYRAMEDIA - Main JavaScript
// ========================================

// Global Variables
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let selectedDate = null;
let selectedTime = null;
let currentService = null;

// ========================================
// Dark Mode Toggle
// ========================================
const themeToggle = document.getElementById('themeToggle');
const body = document.body;

// Load saved theme
const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'dark') {
    body.classList.add('dark-mode');
    updateThemeIcon(true);
}

themeToggle.addEventListener('click', () => {
    body.classList.toggle('dark-mode');
    const isDark = body.classList.contains('dark-mode');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    updateThemeIcon(isDark);
});

function updateThemeIcon(isDark) {
    const icon = themeToggle.querySelector('i');
    if (isDark) {
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
    } else {
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
    }
}

// ========================================
// Mobile Menu Toggle
// ========================================
const mobileMenuToggle = document.getElementById('mobileMenuToggle');
const mobileMenu = document.getElementById('mobileMenu');

mobileMenuToggle.addEventListener('click', () => {
    mobileMenu.classList.toggle('active');
    const icon = mobileMenuToggle.querySelector('i');
    if (mobileMenu.classList.contains('active')) {
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-times');
    } else {
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
    }
});

// Close mobile menu when clicking on a link
const mobileLinks = mobileMenu.querySelectorAll('a');
mobileLinks.forEach(link => {
    link.addEventListener('click', () => {
        mobileMenu.classList.remove('active');
        const icon = mobileMenuToggle.querySelector('i');
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
    });
});

// ========================================
// Smooth Scrolling for Navigation Links
// ========================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            const offset = 80; // Navbar height
            const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    });
});

// ========================================
// Navbar Scroll Effect
// ========================================
const navbar = document.getElementById('navbar');
let lastScroll = 0;

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;

    if (currentScroll > 100) {
        navbar.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
    } else {
        navbar.style.boxShadow = 'none';
    }

    lastScroll = currentScroll;
});

// ========================================
// Scroll Progress Bar
// ========================================
const scrollProgress = document.getElementById('scrollProgress');

window.addEventListener('scroll', () => {
    const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = (winScroll / height) * 100;
    scrollProgress.style.width = scrolled + '%';
});

// ========================================
// Scroll Animations
// ========================================
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
}, observerOptions);

// Observe all fade-in-up elements
document.querySelectorAll('.fade-in-up').forEach(el => {
    observer.observe(el);
});

// ========================================
// Counter Animation
// ========================================
const counters = document.querySelectorAll('.counter');
let countersAnimated = false;

const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting && !countersAnimated) {
            countersAnimated = true;
            animateCounters();
        }
    });
}, { threshold: 0.5 });

counters.forEach(counter => {
    counterObserver.observe(counter);
});

function animateCounters() {
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps
        let current = 0;

        const updateCounter = () => {
            current += increment;
            if (current < target) {
                counter.textContent = Math.floor(current) + '+';
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target + '+';
            }
        };

        updateCounter();
    });
}

// ========================================
// Booking Modal Functions
// ========================================
function openBookingModal() {
    document.getElementById('bookingModal').classList.add('active');
    showBookingStep1();
    renderCalendar();
    generateTimeSlots();
}

function closeBookingModal() {
    document.getElementById('bookingModal').classList.remove('active');
    resetBookingModal();
}

function showBookingStep1() {
    document.getElementById('bookingStep1').classList.remove('hidden');
    document.getElementById('bookingStep2').classList.add('hidden');
}

function showBookingStep2() {
    if (!selectedDate || !selectedTime) {
        alert('Please select both date and time');
        return;
    }

    document.getElementById('bookingStep1').classList.add('hidden');
    document.getElementById('bookingStep2').classList.remove('hidden');

    // Display selected date and time
    const dateStr = selectedDate.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    document.getElementById('appointmentDisplay').textContent = `${dateStr} at ${selectedTime}`;
}

function resetBookingModal() {
    selectedDate = null;
    selectedTime = null;
    document.getElementById('bookingForm').reset();
    document.querySelectorAll('.calendar-day.selected').forEach(el => el.classList.remove('selected'));
    document.querySelectorAll('.time-slot.selected').forEach(el => el.classList.remove('selected'));
    document.getElementById('continueToStep2').disabled = true;
}

// ========================================
// Calendar System
// ========================================
function renderCalendar() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];

    document.getElementById('calendarMonth').textContent = `${monthNames[currentMonth]} ${currentYear}`;

    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const calendarDays = document.getElementById('calendarDays');
    calendarDays.innerHTML = '';

    // Empty cells before first day
    for (let i = 0; i < firstDay; i++) {
        const emptyCell = document.createElement('div');
        calendarDays.appendChild(emptyCell);
    }

    // Calendar days
    for (let day = 1; day <= daysInMonth; day++) {
        const dayCell = document.createElement('button');
        dayCell.type = 'button';
        dayCell.className = 'calendar-day p-3 rounded-lg text-center font-semibold border-2 border-gray-200 hover:border-orange-500';
        dayCell.textContent = day;

        const cellDate = new Date(currentYear, currentMonth, day);
        cellDate.setHours(0, 0, 0, 0);

        // Disable past dates
        if (cellDate < today) {
            dayCell.classList.add('disabled');
            dayCell.disabled = true;
        } else {
            dayCell.addEventListener('click', () => selectDate(cellDate, dayCell));
        }

        calendarDays.appendChild(dayCell);
    }
}

function selectDate(date, element) {
    selectedDate = date;

    // Remove previous selection
    document.querySelectorAll('.calendar-day.selected').forEach(el => {
        el.classList.remove('selected');
    });

    // Add selection to clicked element
    element.classList.add('selected');

    // Enable continue button if time is also selected
    updateContinueButton();
}

function previousMonth() {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    renderCalendar();
}

function nextMonth() {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    renderCalendar();
}

// ========================================
// Time Slots
// ========================================
function generateTimeSlots() {
    const timeSlots = document.getElementById('timeSlots');
    timeSlots.innerHTML = '';

    const slots = [
        '9:00 AM', '10:00 AM', '11:00 AM',
        '12:00 PM', '1:00 PM', '2:00 PM',
        '3:00 PM', '4:00 PM', '5:00 PM'
    ];

    slots.forEach(time => {
        const slot = document.createElement('button');
        slot.type = 'button';
        slot.className = 'time-slot p-3 rounded-lg border-2 border-gray-200 font-semibold hover:border-orange-500';
        slot.textContent = time;
        slot.addEventListener('click', () => selectTime(time, slot));
        timeSlots.appendChild(slot);
    });
}

function selectTime(time, element) {
    selectedTime = time;

    // Remove previous selection
    document.querySelectorAll('.time-slot.selected').forEach(el => {
        el.classList.remove('selected');
    });

    // Add selection to clicked element
    element.classList.add('selected');

    // Enable continue button if date is also selected
    updateContinueButton();
}

function updateContinueButton() {
    const continueBtn = document.getElementById('continueToStep2');
    continueBtn.disabled = !(selectedDate && selectedTime);
}

// ========================================
// Booking Form Submission
// ========================================
document.getElementById('bookingForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    formData.append('date', selectedDate.toISOString().split('T')[0]);
    formData.append('time', selectedTime);

    try {
        const response = await fetch('php/booking.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert('Booking confirmed! Check your email for details.');
            closeBookingModal();
        } else {
            alert('Booking failed: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
});

// ========================================
// Service Modal Functions
// ========================================
const serviceData = {
    'digital-marketing': {
        title: 'Digital Marketing',
        description: 'Get a comprehensive digital marketing strategy tailored to your business goals.',
        fieldLabel: 'Budget Range',
        fieldName: 'budget_range',
        fieldType: 'select',
        options: ['$1,000 - $5,000', '$5,000 - $10,000', '$10,000 - $25,000', '$25,000+']
    },
    'automation': {
        title: 'Marketing Automation',
        description: 'Streamline your marketing processes with intelligent automation solutions.',
        fieldLabel: 'Current Tools',
        fieldName: 'current_tools',
        fieldType: 'text',
        placeholder: 'e.g., HubSpot, Mailchimp, etc.'
    },
    'brand-strategy': {
        title: 'Brand Strategy',
        description: 'Build a powerful brand identity that resonates with your target audience.',
        fieldLabel: 'Brand Stage',
        fieldName: 'brand_stage',
        fieldType: 'select',
        options: ['New Brand', 'Rebrand', 'Brand Refresh', 'Brand Expansion']
    },
    'ai-solutions': {
        title: 'AI Solutions',
        description: 'Leverage artificial intelligence to drive business growth and efficiency.',
        fieldLabel: 'Solution Type',
        fieldName: 'solution_type',
        fieldType: 'select',
        options: ['Chatbot', 'Analytics', 'Content Generation', 'Customer Insights', 'Other']
    },
    'video-production': {
        title: 'Video Production',
        description: 'Create engaging video content that tells your brand story.',
        fieldLabel: 'Video Type',
        fieldName: 'video_type',
        fieldType: 'select',
        options: ['Commercial', 'Social Media', 'Explainer', 'Event Coverage', 'Other']
    },
    'analytics': {
        title: 'Analytics & Insights',
        description: 'Get actionable insights from your data to make better business decisions.',
        fieldLabel: 'Current Tools',
        fieldName: 'analytics_tools',
        fieldType: 'text',
        placeholder: 'e.g., Google Analytics, Tableau, etc.'
    }
};

function openServiceModal(serviceId) {
    currentService = serviceId;
    const service = serviceData[serviceId];

    document.getElementById('serviceModalTitle').textContent = service.title;
    document.getElementById('serviceModalDescription').textContent = service.description;

    // Create specific field
    const specificField = document.getElementById('serviceSpecificField');
    let fieldHTML = `<label class="block text-gray-700 font-semibold mb-2">${service.fieldLabel}</label>`;

    if (service.fieldType === 'select') {
        fieldHTML += `<select name="${service.fieldName}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">
            <option value="">Select an option</option>`;
        service.options.forEach(opt => {
            fieldHTML += `<option value="${opt}">${opt}</option>`;
        });
        fieldHTML += `</select>`;
    } else {
        fieldHTML += `<input type="text" name="${service.fieldName}" placeholder="${service.placeholder || ''}"
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none">`;
    }

    specificField.innerHTML = fieldHTML;

    document.getElementById('serviceModal').classList.add('active');
}

function closeServiceModal() {
    document.getElementById('serviceModal').classList.remove('active');
    document.getElementById('serviceInquiryForm').reset();
    currentService = null;
}

// ========================================
// Service Inquiry Form Submission
// ========================================
document.getElementById('serviceInquiryForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    formData.append('service', currentService);

    try {
        const response = await fetch('php/contact.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert('Thank you! We will contact you soon.');
            closeServiceModal();
        } else {
            alert('Submission failed: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
});

// ========================================
// Contact Form Submission
// ========================================
document.getElementById('contactForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    formData.append('form_type', 'contact');

    try {
        const response = await fetch('php/contact.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert('Thank you! We will get back to you soon.');
            e.target.reset();
        } else {
            alert('Submission failed: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
});

// ========================================
// Newsletter Form Submission
// ========================================
document.getElementById('newsletterForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);

    try {
        const response = await fetch('php/newsletter.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert('Successfully subscribed to our newsletter!');
            e.target.reset();
        } else {
            alert('Subscription failed: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
});

// ========================================
// Close Modals on Outside Click
// ========================================
window.addEventListener('click', (e) => {
    const bookingModal = document.getElementById('bookingModal');
    const serviceModal = document.getElementById('serviceModal');

    if (e.target === bookingModal) {
        closeBookingModal();
    }
    if (e.target === serviceModal) {
        closeServiceModal();
    }
});

// ========================================
// ESC Key to Close Modals
// ========================================
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeBookingModal();
        closeServiceModal();
    }
});

// ========================================
// Initialize on Page Load
// ========================================
document.addEventListener('DOMContentLoaded', () => {
    console.log('PYRAMEDIA website loaded successfully!');
});
