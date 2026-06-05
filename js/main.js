// Основной JavaScript файл для сайта СтичШоп

document.addEventListener('DOMContentLoaded', function() {
    // Подтверждение удаления
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Вы уверены, что хотите удалить этот товар?')) {
                e.preventDefault();
            }
        });
    });
    
    // Автоотправка формы при изменении количества в корзине
    const quantityInputs = document.querySelectorAll('.cart-item-quantity input');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const min = parseInt(this.min) || 1;
            if (this.value < min) {
                this.value = min;
            }
        });
    });
    
    // Фильтры - подсветка активных
    const filterInputs = document.querySelectorAll('.filters-section input, .filters-section select');
    filterInputs.forEach(input => {
        if (input.value) {
            input.style.borderColor = '#667eea';
        }
        
        input.addEventListener('change', function() {
            if (this.value) {
                this.style.borderColor = '#667eea';
            } else {
                this.style.borderColor = '#ddd';
            }
        });
    });
    
    // Плавная прокрутка для якорных ссылок
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
    
    // Валидация формы регистрации
    const registerForm = document.querySelector('.auth-form form');
    if (registerForm) {
        const password = registerForm.querySelector('[name="password"]');
        const passwordConfirm = registerForm.querySelector('[name="password_confirm"]');
        
        if (password && passwordConfirm) {
            passwordConfirm.addEventListener('input', function() {
                if (this.value !== password.value) {
                    this.setCustomValidity('Пароли не совпадают');
                } else {
                    this.setCustomValidity('');
                }
            });
        }
    }
    
    // Отображение/скрытие пароля
    const togglePasswordButtons = document.querySelectorAll('[data-toggle-password]');
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const inputId = this.getAttribute('data-toggle-password');
            const input = document.getElementById(inputId);
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    this.textContent = '🙈';
                } else {
                    input.type = 'password';
                    this.textContent = '👁️';
                }
            }
        });
    });
    
    // Счётчик товаров в корзине (обновление без перезагрузки)
    const updateCartButtons = document.querySelectorAll('[data-update-cart]');
    updateCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-update-cart');
            const quantityInput = document.querySelector(`[data-product-id="${productId}"]`);
            
            if (quantityInput) {
                const quantity = parseInt(quantityInput.value) || 1;
                
                fetch(`${SITE_URL}/includes/update-cart.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}&quantity=${quantity}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Обновляем счётчик
                        const badge = document.querySelector('.cart-badge');
                        if (badge) {
                            badge.textContent = data.total_items;
                        }
                        
                        // Обновляем сумму
                        const totalElement = document.querySelector('.cart-total p');
                        if (totalElement) {
                            totalElement.innerHTML = `Итого: <strong>${data.total_formatted} ₽</strong>`;
                        }
                    }
                })
                .catch(error => console.error('Ошибка:', error));
            }
        });
    });
    
    // Поиск - автозаполнение (опционально)
    const searchInput = document.querySelector('.search-box input');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) return;
            
            searchTimeout = setTimeout(() => {
                // Здесь можно добавить AJAX поиск
                console.log('Поиск:', query);
            }, 500);
        });
    }
    
    // Анимация появления элементов при прокрутке
    const animatedElements = document.querySelectorAll('.product-card, .section');
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
    
    // Уведомления - автоудаление через 5 секунд
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
    
    // Мобильное меню
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const nav = document.querySelector('.nav');
    
    if (mobileMenuToggle && nav) {
        mobileMenuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            nav.classList.toggle('active');
        });
    }
    
    // Закрытие меню при клике вне его
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 768 && nav && mobileMenuToggle) {
            const isClickInsideMenu = nav.contains(event.target) || mobileMenuToggle.contains(event.target);
            if (!isClickInsideMenu && nav.classList.contains('active')) {
                nav.classList.remove('active');
            }
        }
    });
    
    // Глобальные переменные для использования в других скриптах
    window.StitchShop = {
        siteUrl: SITE_URL,
        userId: USER_ID,
        isAdmin: IS_ADMIN
    };
});

// Утилита для форматирования чисел
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
}

// Утилита для показа уведомления
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.textContent = message;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.5s ease';
        setTimeout(() => notification.remove(), 500);
    }, 3000);
}
