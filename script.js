document.addEventListener('DOMContentLoaded', () => {

    /* ==========================================
       ENVELOPE OPENING LOGIC (Comentado temporalmente)
       ==========================================
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
    */

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
    
    // Ejecutar reveal inmediatamente al cargar ya que no dependemos del sobre
    setTimeout(reveal, 100);
    
    window.addEventListener("scroll", reveal);
    
    /* ==========================================
       COUNTDOWN LOGIC
       ========================================== */
    const targetDate = new Date("August 1, 2026 19:30:00").getTime();
    
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
        RSVP DYNAMIC FORM
        ========================================== */
    const attendanceSelect = document.getElementById('attendance');
    const dynamicFields = document.getElementById('dynamic-rsvp-fields');
    const extraFields = document.getElementById('attending-extra-fields');
    const mealFields = document.getElementById('attending-meal-fields');

    if (attendanceSelect) {
        attendanceSelect.addEventListener('change', function() {
            dynamicFields.classList.remove('hidden');
            setTimeout(() => {
                dynamicFields.classList.add('visible');
            }, 10);
            
            if (this.value === 'yes') {
                extraFields.classList.remove('hidden');
                mealFields.classList.remove('hidden');
                setTimeout(() => {
                    extraFields.classList.add('visible');
                    mealFields.classList.add('visible');
                }, 10);
            } else {
                extraFields.classList.remove('visible');
                mealFields.classList.remove('visible');
                setTimeout(() => {
                    extraFields.classList.add('hidden');
                    mealFields.classList.add('hidden');
                }, 400);
            }
        });
    }

    /* ==========================================
       COMPANION FIELDS
       ========================================== */
    const companionsSelect = document.getElementById('guest-companions');
    const companionsContainer = document.getElementById('companions-container');

    if (companionsSelect) {
        companionsSelect.addEventListener('change', function() {
            const count = parseInt(this.value) || 0;
            companionsContainer.innerHTML = '';

            for (let i = 0; i < count; i++) {
                const row = document.createElement('div');
                row.className = 'companion-row';

                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'companion_name_' + i;
                input.placeholder = 'Nombre del acompañante ' + (i + 1);
                input.className = 'form-control';

                const label = document.createElement('label');
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'companion_child_' + i;
                checkbox.value = '1';
                label.appendChild(checkbox);
                label.appendChild(document.createTextNode(' ¿Es niño/a?'));

                row.appendChild(input);
                row.appendChild(label);
                companionsContainer.appendChild(row);
            }
        });
    }

    /* ==========================================
       RSVP FORM SUBMISSION
       ========================================== */
    const rsvpForm = document.getElementById('rsvp-form');
    const formMessage = document.getElementById('form-message');

    if (rsvpForm) {
        rsvpForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = rsvpForm.querySelector('.btn-rsvp-submit');
            const originalText = submitBtn.innerText;
            submitBtn.innerText = 'ENVIANDO...';
            submitBtn.disabled = true;

            const formData = new FormData(rsvpForm);

            fetch('rsvp.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                formMessage.classList.remove('hidden');
                
                if (data.status === 'success') {
                    formMessage.style.backgroundColor = '#d4edda';
                    formMessage.style.color = '#155724';
                    formMessage.style.border = '1px solid #c3e6cb';
                    formMessage.innerText = data.message;
                    rsvpForm.reset();
                    // Optionally hide the dynamic fields again
                    if (dynamicFields) dynamicFields.classList.add('hidden');
                } else {
                    formMessage.style.backgroundColor = '#f8d7da';
                    formMessage.style.color = '#721c24';
                    formMessage.style.border = '1px solid #f5c6cb';
                    formMessage.innerText = data.message || 'Error al enviar el formulario.';
                }
            })
            .catch(error => {
                formMessage.classList.remove('hidden');
                formMessage.style.backgroundColor = '#f8d7da';
                formMessage.style.color = '#721c24';
                formMessage.style.border = '1px solid #f5c6cb';
                formMessage.innerText = 'Error de conexión. Inténtalo de nuevo más tarde.';
                console.error('Error:', error);
            })
            .finally(() => {
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});
