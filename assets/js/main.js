(() => {
    const root = document.documentElement;
    const themeButton = document.querySelector('[data-theme-toggle]');
    const themeLabel = document.querySelector('[data-theme-label]');
    const themeIcon = document.querySelector('[data-theme-icon]');
    const navButton = document.querySelector('[data-nav-toggle]');
    const navMenu = document.querySelector('[data-nav-menu]');

    const setTheme = (theme) => {
        const isDark = theme === 'dark';
        root.dataset.theme = isDark ? 'dark' : 'light';
        themeButton?.setAttribute('aria-pressed', String(isDark));
        if (themeLabel) themeLabel.textContent = isDark ? 'Light mode' : 'Dark mode';
        if (themeIcon) themeIcon.textContent = isDark ? '☼' : '◐';
    };

    let savedTheme = null;
    try { savedTheme = localStorage.getItem('study-room-theme'); } catch (_error) { /* Storage may be disabled. */ }
    setTheme(savedTheme === 'dark' ? 'dark' : 'light');

    themeButton?.addEventListener('click', () => {
        const nextTheme = root.dataset.theme === 'dark' ? 'light' : 'dark';
        setTheme(nextTheme);
        try { localStorage.setItem('study-room-theme', nextTheme); } catch (_error) { /* Keep the current page theme. */ }
    });

    navButton?.addEventListener('click', () => {
        const open = navButton.getAttribute('aria-expanded') === 'true';
        navButton.setAttribute('aria-expanded', String(!open));
        navMenu?.classList.toggle('is-open', !open);
    });
    navMenu?.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => {
        navButton?.setAttribute('aria-expanded', 'false');
        navMenu.classList.remove('is-open');
    }));

    document.querySelectorAll('[data-flash-dismiss]').forEach((button) => {
        button.addEventListener('click', () => button.closest('.flash-toast')?.remove());
    });

    document.querySelectorAll('.flash-toast.alert--success').forEach((toast) => {
        window.setTimeout(() => toast.remove(), 6000);
    });

    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        const input = document.getElementById(button.getAttribute('aria-controls'));
        if (!input) return;

        button.addEventListener('click', () => {
            const showPassword = input.type === 'password';
            input.type = showPassword ? 'text' : 'password';
            button.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
            button.setAttribute('aria-pressed', String(showPassword));
            button.classList.toggle('is-visible', showPassword);
        });
    });

    document.querySelectorAll('.number-stepper').forEach((control) => {
        const input = control.querySelector('input[type="number"]');
        const buttons = control.querySelectorAll('[data-number-step]');
        if (!input) return;

        const syncButtons = () => {
            const min = input.min === '' ? -Infinity : Number(input.min);
            const max = input.max === '' ? Infinity : Number(input.max);
            const value = input.value === '' ? NaN : Number(input.value);
            buttons.forEach((button) => {
                const direction = Number(button.dataset.numberStep);
                button.disabled = direction < 0
                    ? Number.isNaN(value) || value <= min
                    : value >= max;
            });
        };

        const changeValue = (button) => {
            if (button.disabled) return;
            const direction = Number(button.dataset.numberStep);
            const step = Number(input.step) || 1;
            const min = input.min === '' ? 0 : Number(input.min);
            const current = input.value === ''
                ? (direction > 0 ? min - step : min + step)
                : Number(input.value);
            input.value = String(Math.max(min, current + direction * step));
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
            input.focus();
            syncButtons();
        };

        let holdDelay = null;
        let holdRepeat = null;
        const stopHold = () => {
            window.clearTimeout(holdDelay);
            window.clearInterval(holdRepeat);
            holdDelay = null;
            holdRepeat = null;
        };

        buttons.forEach((button) => {
            button.addEventListener('pointerdown', (event) => {
                if (event.button !== 0 || button.disabled) return;
                event.preventDefault();
                button.setPointerCapture?.(event.pointerId);
                changeValue(button);
                holdDelay = window.setTimeout(() => {
                    holdRepeat = window.setInterval(() => {
                        if (button.disabled) {
                            stopHold();
                            return;
                        }
                        changeValue(button);
                    }, 90);
                }, 350);
            });
            button.addEventListener('pointerup', stopHold);
            button.addEventListener('pointercancel', stopHold);
            button.addEventListener('lostpointercapture', stopHold);
            button.addEventListener('click', (event) => {
                if (event.detail !== 0) return;
                changeValue(button);
            });
        });

        document.addEventListener('pointerup', stopHold);
        window.addEventListener('blur', stopHold);

        input.addEventListener('input', syncButtons);
        syncButtons();
    });

    const slotRoomSelect = document.querySelector('#slot-room');
    const slotCapacityInput = document.querySelector('#slot-capacity');
    const slotCapacityHint = document.querySelector('#slot-capacity-hint');
    if (slotRoomSelect && slotCapacityInput && slotCapacityHint) {
        const syncSlotCapacity = () => {
            const maxCapacity = slotRoomSelect.selectedOptions[0]?.dataset.capacity;
            if (!maxCapacity) {
                slotCapacityInput.removeAttribute('max');
                slotCapacityHint.textContent = 'Select a room to see its maximum capacity.';
                slotCapacityInput.dispatchEvent(new Event('input', { bubbles: true }));
                return;
            }

            slotCapacityInput.max = maxCapacity;
            slotCapacityHint.textContent = `This room can hold up to ${maxCapacity} people.`;
            if (Number(slotCapacityInput.value) > Number(maxCapacity)) {
                slotCapacityInput.value = maxCapacity;
            }
            slotCapacityInput.dispatchEvent(new Event('input', { bubbles: true }));
        };

        slotRoomSelect.addEventListener('change', syncSlotCapacity);
        syncSlotCapacity();
    }

    const roomDialog = document.querySelector('#room-edit-dialog');
    if (roomDialog instanceof HTMLDialogElement) {
        const roomIdInput = roomDialog.querySelector('#edit-room-id');
        const roomPageInput = roomDialog.querySelector('#edit-room-page');
        const roomNameInput = roomDialog.querySelector('#edit-room-name');
        const roomCapacityInput = roomDialog.querySelector('#edit-room-capacity');
        const roomDescriptionInput = roomDialog.querySelector('#edit-room-description');

        document.querySelectorAll('[data-room-edit]').forEach((button) => {
            button.addEventListener('click', () => {
                roomIdInput.value = button.dataset.roomId || '';
                roomPageInput.value = button.dataset.roomPage || '1';
                roomNameInput.value = button.dataset.roomName || '';
                roomCapacityInput.value = button.dataset.roomCapacity || '';
                roomCapacityInput.dispatchEvent(new Event('input', { bubbles: true }));
                roomDescriptionInput.value = button.dataset.roomDescription || '';
                roomDialog.showModal();
                roomNameInput.focus();
            });
        });

        roomDialog.querySelectorAll('[data-room-edit-close]').forEach((button) => {
            button.addEventListener('click', () => roomDialog.close());
        });

        roomDialog.addEventListener('click', (event) => {
            if (event.target === roomDialog) roomDialog.close();
        });
    }

    const slotDialog = document.querySelector('#slot-edit-dialog');
    if (slotDialog instanceof HTMLDialogElement) {
        const slotIdInput = slotDialog.querySelector('#edit-slot-id');
        const slotPageInput = slotDialog.querySelector('#edit-slot-page');
        const slotDateInput = slotDialog.querySelector('#edit-slot-date');
        const slotStartInput = slotDialog.querySelector('#edit-slot-start');
        const slotEndInput = slotDialog.querySelector('#edit-slot-end');
        const slotCapacityInput = slotDialog.querySelector('#edit-slot-capacity');

        document.querySelectorAll('[data-slot-edit]').forEach((button) => {
            button.addEventListener('click', () => {
                slotIdInput.value = button.dataset.slotId || '';
                slotPageInput.value = button.dataset.slotPage || '1';
                slotDateInput.value = button.dataset.slotDate || '';
                slotStartInput.value = button.dataset.slotStart || '';
                slotEndInput.value = button.dataset.slotEnd || '';
                slotCapacityInput.value = button.dataset.slotCapacity || '';
                slotCapacityInput.dispatchEvent(new Event('input', { bubbles: true }));
                slotDialog.showModal();
                slotDateInput.focus();
            });
        });

        slotDialog.querySelectorAll('[data-slot-edit-close]').forEach((button) => {
            button.addEventListener('click', () => slotDialog.close());
        });

        slotDialog.addEventListener('click', (event) => {
            if (event.target === slotDialog) slotDialog.close();
        });
    }

    document.querySelectorAll('[data-demo-login]').forEach((button) => {
        button.addEventListener('click', () => {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            if (!emailInput || !passwordInput) return;

            emailInput.value = button.dataset.email || '';
            passwordInput.value = button.dataset.password || '';
            emailInput.dispatchEvent(new Event('input', { bubbles: true }));
            passwordInput.dispatchEvent(new Event('input', { bubbles: true }));
        });
    });

    const revealItems = document.querySelectorAll('[data-reveal]');
    if ('IntersectionObserver' in window && revealItems.length) {
        root.classList.add('reveal-ready');
        const observer = new IntersectionObserver((entries, currentObserver) => entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                currentObserver.unobserve(entry.target);
            }
        }), { threshold: 0.12 });
        revealItems.forEach((item) => observer.observe(item));
    } else {
        revealItems.forEach((item) => item.classList.add('is-visible'));
    }
})();
