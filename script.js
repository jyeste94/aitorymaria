document.addEventListener('DOMContentLoaded', () => {

    /* ==========================================
       ENVELOPE OPENING LOGIC
       ========================================== */
    const envelopePreloader = document.getElementById('envelope-preloader');
    const envelope = document.querySelector('.envelope');
    const mainContent = document.getElementById('main-content');

    envelope.addEventListener('click', () => {
        // Start opening animation
        envelope.classList.add('is-opening');
        
        // Wait for the flap animation, then fade out preloader
        setTimeout(() => {
            envelopePreloader.classList.add('open');
            mainContent.classList.remove('hidden');
            document.body.classList.remove('locked');
            
            // Trigger first scroll reveal manually for hero section once visible
            setTimeout(reveal, 100);
        }, 800);
        
        // Remove preloader from DOM to allow clicking things underneath
        setTimeout(() => {
            envelopePreloader.style.display = 'none';
        }, 1800);
    });

    /* ==========================================
       SCROLL REVEAL ANIMATIONS
       ========================================== */
    function reveal() {
        var reveals = document.querySelectorAll(".reveal");
        for (var i = 0; i < reveals.length; i++) {
            var windowHeight = window.innerHeight;
            var elementTop = reveals[i].getBoundingClientRect().top;
            var elementVisible = 100;
            
            if (elementTop < windowHeight - elementVisible) {
                reveals[i].classList.add("active");
            }
        }
    }
    
    window.addEventListener("scroll", reveal);
    
    /* ==========================================
       COUNTDOWN LOGIC
       ========================================== */
    const targetDate = new Date("June 15, 2026 17:00:00").getTime();
    
    function updateCountdown() {
        const now = new Date().getTime();
        const distance = targetDate - now;
        
        if (distance < 0) {
            document.getElementById("days").innerText = "00";
            document.getElementById("hours").innerText = "00";
            document.getElementById("minutes").innerText = "00";
            document.getElementById("seconds").innerText = "00";
            return;
        }
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        document.getElementById("days").innerText = days.toString().padStart(2, '0');
        document.getElementById("hours").innerText = hours.toString().padStart(2, '0');
        document.getElementById("minutes").innerText = minutes.toString().padStart(2, '0');
        document.getElementById("seconds").innerText = seconds.toString().padStart(2, '0');
    }
    
    setInterval(updateCountdown, 1000);
    updateCountdown(); // Init immediately
    
    /* ==========================================
       ADD TO CALENDAR (.ICS FILE)
       ========================================== */
    const btnCalendar = document.getElementById("btn-calendar");
    if(btnCalendar) {
        btnCalendar.addEventListener("click", function(e) {
            e.preventDefault();
            
            // Format: YYYYMMDDTHHMMSSZ (UTC Time) 
            // 5:00 PM in Mallorca in June (CEST = UTC+2) -> 15:00:00Z
            const icsContent = 
`BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Aitor y Maria//Boda//ES
BEGIN:VEVENT
UID:boda-aitor-maria-2026
DTSTAMP:20260615T150000Z
DTSTART:20260615T150000Z
DTEND:20260616T040000Z
SUMMARY:Boda de Aitor y Maria
DESCRIPTION:¡Nos casamos! Acompáñanos en nuestro día especial.
LOCATION:Finca Biniagual\\, Camí de Biniagual\\, s/n\\, 07350 Binissalem\\, Illes Balears
END:VEVENT
END:VCALENDAR`;

            const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.setAttribute('download', 'boda-aitor-maria.ics');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }

});
