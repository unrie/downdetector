<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle"></i> Проверить свой сайт
                    </h5>
                </div>
                <div class="card-body">
                    <form id="customCheckForm" class="row g-3">
                        <div class="col-md-8">
                            <input type="text" id="customUrl" name="url" class="form-control" 
                                   placeholder="example.com или https://example.com" 
                                   required>
                            <div class="form-text">Можно вводить с http/https или без</div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Проверить
                            </button>
                        </div>
                    </form>
                    <div id="customResult" class="mt-3"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-globe"></i> Популярные сайты
                    </h5>
                    <button id="refreshAll" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-sync"></i> Обновить все
                    </button>
                </div>
                <div class="card-body">
                    <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                        <i class="fas fa-info-circle"></i> 
                        Данные кэшируются на сервере на <?= round(CACHE_TIME / 60) ?> минут
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    
                    <div class="row" id="sitesContainer">
                        <?php foreach ($defaultSites as $name => $url): ?>
                        <?php $cachedData = $cachedResults[$name] ?? null; ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card site-card" data-url="<?= htmlspecialchars($url) ?>" data-name="<?= htmlspecialchars($name) ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="card-title mb-0"><?= ucfirst($name) ?></h6>
                                        <div class="status-indicator">
                                            <?php if ($cachedData): ?>
                                                <span class="badge bg-<?= $cachedData['available'] ? 'success' : 'danger' ?>">
                                                    <i class="fas <?= $cachedData['available'] ? 'fa-check-circle' : 'fa-times-circle' ?>"></i> 
                                                    <?= $cachedData['available'] ? 'Доступен' : 'Не доступен' ?>
                                                    <i class="fas fa-database text-light ms-1" title="Данные из кэша"></i>
                                                </span>
                                            <?php else: ?>
                                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                    <span class="visually-hidden">Проверка...</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <p class="card-text small text-muted mb-1">
                                        <i class="fas fa-link"></i> <?= htmlspecialchars($url) ?>
                                    </p>
                                    <div class="site-details" style="<?= $cachedData ? 'display: block;' : 'display: none;' ?>">
                                        <?php if ($cachedData): ?>
                                        <p class="card-text small mb-1 response-time">
                                            <i class="fas fa-clock"></i> Время ответа: <span><?= $cachedData['response_time'] ?></span>ms
                                        </p>
                                        <p class="card-text small mb-0 http-code">
                                            <i class="fas fa-code"></i> HTTP код: <span><?= $cachedData['http_code'] ?></span>
                                        </p>
                                        <p class="card-text small text-muted mt-2 checked-at">
                                            <i class="fas fa-sync"></i> Проверено: <span><?= $cachedData['checked_at'] ?></span>
                                        </p>
                                        <?php else: ?>
                                        <p class="card-text small mb-1 response-time">
                                            <i class="fas fa-clock"></i> Время ответа: <span>—</span>
                                        </p>
                                        <p class="card-text small mb-0 http-code">
                                            <i class="fas fa-code"></i> HTTP код: <span>—</span>
                                        </p>
                                        <p class="card-text small text-muted mt-2 checked-at">
                                            <i class="fas fa-sync"></i> Проверено: <span>—</span>
                                        </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Функция для проверки одного сайта
    function checkSite(cardElement, url, name, force = false) {
        const statusIndicator = cardElement.querySelector('.status-indicator');
        const siteDetails = cardElement.querySelector('.site-details');
        
        // Показываем спиннер
        statusIndicator.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Проверка...</span></div>';
        
        // AJAX запрос для проверки сайта
        fetch('?action=check', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'url=' + encodeURIComponent(url) + '&force=' + (force ? '1' : '0')
        })
        .then(response => response.json())
        .then(data => {
            // Обновляем карточку
            updateCardWithData(cardElement, data);
        })
        .catch(error => {
            statusIndicator.innerHTML = '<span class="badge bg-warning"><i class="fas fa-exclamation-triangle"></i> Ошибка</span>';
            console.error('Error:', error);
        });
    }
    
    // Функция для обновления карточки данными
    function updateCardWithData(cardElement, data) {
        const statusIndicator = cardElement.querySelector('.status-indicator');
        const siteDetails = cardElement.querySelector('.site-details');
        
        // Обновляем статус
        if (data.available) {
            statusIndicator.innerHTML = '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Доступен</span>';
            cardElement.classList.add('border-success');
            cardElement.classList.remove('border-danger');
        } else {
            statusIndicator.innerHTML = '<span class="badge bg-danger"><i class="fas fa-times-circle"></i> Не доступен</span>';
            cardElement.classList.add('border-danger');
            cardElement.classList.remove('border-success');
        }
        
        // Добавляем иконку кэша если данные из кэша
        if (data.from_cache) {
            statusIndicator.querySelector('.badge').innerHTML += ' <i class="fas fa-database text-light ms-1" title="Из кэша"></i>';
        }
        
        // Обновляем детали
        if (siteDetails) {
            siteDetails.querySelector('.response-time span').textContent = data.response_time + 'ms';
            siteDetails.querySelector('.http-code span').textContent = data.http_code;
            siteDetails.querySelector('.checked-at span').textContent = data.checked_at;
            siteDetails.style.display = 'block';
        }
    }
    
    // Проверяем только те сайты, у которых нет кэшированных данных
    const siteCards = document.querySelectorAll('.site-card');
    siteCards.forEach((card, index) => {
        const url = card.getAttribute('data-url');
        const name = card.getAttribute('data-name');
        const statusIndicator = card.querySelector('.status-indicator');
        
        // Если нет кэшированных данных, проверяем с задержкой
        if (statusIndicator.querySelector('.spinner-border')) {
            setTimeout(() => {
                checkSite(card, url, name, false);
            }, index * 800); // Задержка 800ms между проверками
        }
    });
    
    // Обработчик для формы проверки кастомного URL
    document.getElementById('customCheckForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const customUrl = document.getElementById('customUrl').value.trim();
        const resultContainer = document.getElementById('customResult');
        
        if (!customUrl) {
            resultContainer.innerHTML = '<div class="alert alert-warning">Введите URL для проверки</div>';
            return;
        }
        
        // Показываем загрузку
        resultContainer.innerHTML = `
            <div class="card">
                <div class="card-body text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Проверка...</span>
                    </div>
                    <p class="mt-2 mb-0">Проверяем сайт...</p>
                </div>
            </div>
        `;
        
        // Форматируем URL
        let formattedUrl = customUrl;
        if (!formattedUrl.includes('://')) {
            formattedUrl = 'https://' + formattedUrl;
        }
        
        // AJAX запрос для проверки кастомного URL
        fetch('?action=check', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'url=' + encodeURIComponent(formattedUrl) + '&force=true'
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                resultContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> Ошибка: ${data.error}
                    </div>
                `;
                return;
            }
            
            const cacheIcon = data.from_cache ? ' <i class="fas fa-database text-light ms-1" title="Из кэша"></i>' : '';
            
            resultContainer.innerHTML = `
                <div class="card ${data.available ? 'border-success' : 'border-danger'}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title mb-0">Пользовательский сайт</h6>
                            <span class="badge bg-${data.available ? 'success' : 'danger'}">
                                <i class="fas ${data.available ? 'fa-check-circle' : 'fa-times-circle'}"></i> 
                                ${data.available ? 'Доступен' : 'Не доступен'}${cacheIcon}
                            </span>
                        </div>
                        <p class="card-text small text-muted mb-1">
                            <i class="fas fa-link"></i> ${data.url}
                        </p>
                        <p class="card-text small mb-1">
                            <i class="fas fa-clock"></i> Время ответа: ${data.response_time}ms
                        </p>
                        <p class="card-text small mb-0">
                            <i class="fas fa-code"></i> HTTP код: ${data.http_code}
                        </p>
                        ${data.error ? `<p class="card-text small text-danger mt-2">
                            <i class="fas fa-exclamation-circle"></i> Ошибка: ${data.error}
                        </p>` : ''}
                        <p class="card-text small text-muted mt-2">
                            <i class="fas fa-sync"></i> Проверено: ${data.checked_at}
                        </p>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            resultContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Ошибка при проверке: ${error}
                </div>
            `;
        });
    });
    
    // Кнопка обновления всех сайтов (принудительная проверка)
    document.getElementById('refreshAll').addEventListener('click', function() {
        const siteCards = document.querySelectorAll('.site-card');
        siteCards.forEach((card, index) => {
            setTimeout(() => {
                const url = card.getAttribute('data-url');
                const name = card.getAttribute('data-name');
                
                // Сбрасываем стили
                card.classList.remove('border-success', 'border-danger');
                checkSite(card, url, name, true); // force = true
            }, index * 800);
        });
    });
});
</script>