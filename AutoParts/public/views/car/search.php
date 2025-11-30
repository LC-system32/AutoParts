<?php
/**
 * Production-версія сторінки підбору по авто
 *
 * @var array<int,array<string,mixed>> $carMakes
 */

$carMakes = $carMakes ?? [];

// Попередньо вибрані значення з GET (щоб зберігати стан форми)
$selectedMakeId         = (string)($_GET['make_id']         ?? '');
$selectedModelId        = (string)($_GET['model_id']        ?? '');
$selectedGenerationId   = (string)($_GET['generation_id']   ?? '');
$selectedModificationId = (string)($_GET['modification_id'] ?? '');
?>

<section class="py-4 py-md-5">
    <div class="container">
        <!-- Основний контент -->
        <div class="row g-4">

            <!-- Ліва колонка: користувацьке пояснення -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-uppercase small text-muted fw-semibold mb-1">
                            Як користуватися
                        </div>
                        <h2 class="h5 fw-bold mb-2">
                            Підбір запчастин за твоїм авто
                        </h2>
                        <ol class="small text-muted ps-3 mb-3">
                            <li>Обери марку та модель авто.</li>
                            <li>Вкажи покоління та модифікацію (двигун, роки випуску).</li>
                            <li>Натисни <strong>«Знайти запчастини»</strong> — на сторінці товарів буде застосовано фільтр по авто.</li>
                        </ol>

                        <div class="alert alert-success small mb-2">
                            <i class="bi bi-check-circle me-1"></i>
                            Після вибору модифікації ми передаємо параметри авто на сторінку каталогу, щоб показати тільки сумісні позиції.
                        </div>

                        <p class="small text-muted mb-0">
                            За потреби ти завжди можеш змінити вибір авто та оновити результати пошуку.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Права колонка: форма підбору -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="small text-muted text-uppercase fw-semibold">
                                    Пошук по авто
                                </div>
                                <h2 class="h5 fw-bold mb-0">
                                    Обери своє авто крок за кроком
                                </h2>
                            </div>
                        </div>

                        <form class="row g-2 g-md-3 align-items-end" method="get"
                              action="/products" id="carPickerForm">

                            <!-- Марка -->
                            <div class="col-12 col-md-6">
                                <label for="carMake" class="form-label small text-muted mb-1">
                                    Марка авто
                                </label>
                                <select
                                    name="make_id"
                                    id="carMake"
                                    class="form-select"
                                    required
                                    data-selected="<?= htmlspecialchars($selectedMakeId); ?>"
                                >
                                    <option value="">Оберіть марку...</option>
                                    <?php foreach ($carMakes as $make): ?>
                                        <?php
                                        $id   = (string)($make['id'] ?? '');
                                        $name = (string)($make['name'] ?? $make['slug'] ?? '—');
                                        ?>
                                        <option
                                            value="<?= htmlspecialchars($id); ?>"
                                            <?= $id === $selectedMakeId ? 'selected' : ''; ?>
                                        >
                                            <?= htmlspecialchars($name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Модель -->
                            <div class="col-12 col-md-6">
                                <label for="carModel" class="form-label small text-muted mb-1">
                                    Модель
                                </label>
                                <select
                                    name="model_id"
                                    id="carModel"
                                    class="form-select"
                                    disabled
                                    required
                                    data-selected="<?= htmlspecialchars($selectedModelId); ?>"
                                >
                                    <option value="">Спочатку оберіть марку...</option>
                                </select>
                            </div>

                            <!-- Покоління -->
                            <div class="col-12 col-md-6">
                                <label for="carGeneration" class="form-label small text-muted mb-1">
                                    Покоління
                                </label>
                                <select
                                    name="generation_id"
                                    id="carGeneration"
                                    class="form-select"
                                    disabled
                                    data-selected="<?= htmlspecialchars($selectedGenerationId); ?>"
                                >
                                    <option value="">Спочатку оберіть модель...</option>
                                </select>
                            </div>

                            <!-- Модифікація -->
                            <div class="col-12 col-md-6">
                                <label for="carModification" class="form-label small text-muted mb-1">
                                    Модифікація (двигун / роки)
                                </label>
                                <select
                                    name="modification_id"
                                    id="carModification"
                                    class="form-select"
                                    disabled
                                    data-selected="<?= htmlspecialchars($selectedModificationId); ?>"
                                >
                                    <option value="">Спочатку оберіть покоління...</option>
                                </select>
                            </div>

                            <!-- Кнопка пошуку -->
                            <div class="col-12 d-grid mt-2">
                                <button type="submit"
                                        class="btn btn-warning text-dark fw-semibold py-2"
                                        id="carPickerSubmit"
                                        disabled>
                                    <i class="bi bi-search me-1"></i>
                                    Знайти запчастини
                                </button>
                            </div>

                            <!-- Тип пошуку, щоб на /products можна було відрізнити логіку -->
                            <input type="hidden" name="search_type" value="car">
                        </form>

                        <!-- Stepper -->
                        <div class="d-flex justify-content-center justify-content-md-end mt-3 small text-muted">
                            <div class="me-3">
                                <span class="badge bg-dark text-white me-1">1</span> Марка
                            </div>
                            <div class="me-3">
                                <span class="badge bg-secondary text-white me-1">2</span> Модель
                            </div>
                            <div class="me-3">
                                <span class="badge bg-secondary text-white me-1">3</span> Покоління
                            </div>
                            <div>
                                <span class="badge bg-warning text-dark me-1">4</span> Модифікація / Результати
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Коротке пояснення під формою, без технічних деталей -->
                <div class="mt-3 small text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Вибрані параметри авто будуть використані як фільтр на сторінці каталогу товарів.
                    Ти побачиш запчастини, що відповідають вибраній модифікації.
                </div>
            </div>

        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const makeSelect         = document.getElementById('carMake');
    const modelSelect        = document.getElementById('carModel');
    const generationSelect   = document.getElementById('carGeneration');
    const modificationSelect = document.getElementById('carModification');
    const submitButton       = document.getElementById('carPickerSubmit');

    if (!makeSelect || !modelSelect || !generationSelect || !modificationSelect || !submitButton) {
        return;
    }

    const initial = {
        make: makeSelect.dataset.selected || '',
        model: modelSelect.dataset.selected || '',
        generation: generationSelect.dataset.selected || '',
        modification: modificationSelect.dataset.selected || ''
    };

    function resetSelect(selectEl, placeholder, disabled = true) {
        selectEl.innerHTML = '<option value="">' + placeholder + '</option>';
        selectEl.disabled = disabled;
    }

    async function fetchJson(url) {
        const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!resp.ok) throw new Error('Помилка відповіді сервера');
        return await resp.json();
    }

    async function loadModels(makeId, preselectModelId = '') {
        resetSelect(modelSelect, 'Спочатку оберіть марку...', true);
        resetSelect(generationSelect, 'Спочатку оберіть модель...', true);
        resetSelect(modificationSelect, 'Спочатку оберіть покоління...', true);
        submitButton.disabled = true;

        if (!makeId) return;

        try {
            modelSelect.innerHTML = '<option value="">Завантаження моделей...</option>';
            modelSelect.disabled = true;

            const models = await fetchJson('/car/models?make_id=' + encodeURIComponent(makeId));

            resetSelect(modelSelect, 'Оберіть модель...', false);

            if (Array.isArray(models) && models.length) {
                models.forEach(function (m) {
                    const opt = document.createElement('option');
                    opt.value = m.id ?? '';
                    opt.textContent = m.name ?? (m.slug ?? 'Без назви');

                    if (preselectModelId && String(opt.value) === String(preselectModelId)) {
                        opt.selected = true;
                    }
                    modelSelect.appendChild(opt);
                });
            } else {
                resetSelect(modelSelect, 'Для цієї марки немає моделей', true);
            }
        } catch (e) {
            console.error(e);
            resetSelect(modelSelect, 'Помилка завантаження моделей', true);
        }
    }

    async function loadGenerations(modelId, preselectGenerationId = '') {
        resetSelect(generationSelect, 'Спочатку оберіть модель...', true);
        resetSelect(modificationSelect, 'Спочатку оберіть покоління...', true);
        submitButton.disabled = true;

        if (!modelId) return;

        try {
            generationSelect.innerHTML = '<option value="">Завантаження поколінь...</option>';
            generationSelect.disabled = true;

            const gens = await fetchJson('/car/generations?model_id=' + encodeURIComponent(modelId));

            resetSelect(generationSelect, 'Оберіть покоління...', false);

            if (Array.isArray(gens) && gens.length) {
                gens.forEach(function (g) {
                    const opt = document.createElement('option');
                    opt.value = g.id ?? '';
                    let label = g.name ?? '';
                    const yFrom = g.year_from ?? g.yearFrom;
                    const yTo   = g.year_to ?? g.yearTo;
                    if (yFrom || yTo) {
                        label += ' (' + (yFrom ?? '') + '–' + (yTo ?? '') + ')';
                    }
                    opt.textContent = label || 'Покоління #' + (g.id ?? '');

                    if (preselectGenerationId && String(opt.value) === String(preselectGenerationId)) {
                        opt.selected = true;
                    }

                    generationSelect.appendChild(opt);
                });
            } else {
                resetSelect(generationSelect, 'Для цієї моделі немає поколінь', true);
            }
        } catch (e) {
            console.error(e);
            resetSelect(generationSelect, 'Помилка завантаження поколінь', true);
        }
    }

    async function loadModifications(genId, preselectModificationId = '') {
        resetSelect(modificationSelect, 'Спочатку оберіть покоління...', true);
        submitButton.disabled = true;

        if (!genId) return;

        try {
            modificationSelect.innerHTML = '<option value="">Завантаження модифікацій...</option>';
            modificationSelect.disabled = true;

            const mods = await fetchJson('/car/modifications?generation_id=' + encodeURIComponent(genId));

            resetSelect(modificationSelect, 'Оберіть модифікацію...', false);

            if (Array.isArray(mods) && mods.length) {
                mods.forEach(function (m) {
                    const opt = document.createElement('option');
                    opt.value = m.id ?? '';

                    const engine  = m.engine_code ?? m.engine ?? '';
                    const fuel    = m.fuel_type ?? '';
                    const trans   = m.transmission ?? '';
                    const yFrom   = m.year_from ?? m.yearFrom ?? '';
                    const yTo     = m.year_to ?? m.yearTo ?? '';

                    let label = engine ? engine : 'Модифікація #' + (m.id ?? '');
                    const extra = [];

                    if (fuel)  extra.push(fuel);
                    if (trans) extra.push(trans);
                    if (yFrom || yTo) extra.push((yFrom || '') + '–' + (yTo || ''));

                    if (extra.length) {
                        label += ' • ' + extra.join(' • ');
                    }

                    opt.textContent = label;

                    if (preselectModificationId && String(opt.value) === String(preselectModificationId)) {
                        opt.selected = true;
                    }

                    modificationSelect.appendChild(opt);
                });
            } else {
                resetSelect(modificationSelect, 'Для цього покоління немає модифікацій', true);
            }
        } catch (e) {
            console.error(e);
            resetSelect(modificationSelect, 'Помилка завантаження модифікацій', true);
        }
    }

    // Обробники змін
    makeSelect.addEventListener('change', function () {
        loadModels(this.value, '');
    });

    modelSelect.addEventListener('change', function () {
        loadGenerations(this.value, '');
    });

    generationSelect.addEventListener('change', function () {
        loadModifications(this.value, '');
    });

    modificationSelect.addEventListener('change', function () {
        submitButton.disabled = !modificationSelect.value;
    });

    // Ініціалізація, якщо є попередньо вибрані значення (production UX)
    (async function initPreselected() {
        try {
            if (initial.make) {
                makeSelect.value = initial.make;
                await loadModels(initial.make, initial.model);
            }
            if (initial.model) {
                await loadGenerations(initial.model, initial.generation);
            }
            if (initial.generation) {
                await loadModifications(initial.generation, initial.modification);
            }
            if (initial.modification) {
                submitButton.disabled = false;
            }
        } catch (e) {
            console.error('Init preselected error', e);
        }
    })();
});
</script>
