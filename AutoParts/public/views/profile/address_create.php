<section class="py-4 py-md-5">

    <!-- HERO-БЛОК -->
    <div class="d-flex flex-column flex-md-row align-items-md-center mb-4">
        <div class="me-md-3 mb-3 mb-md-0">
            <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center"
                 style="width:72px;height:72px;">
                <i class="bi bi-geo-alt fs-1 text-warning"></i>
            </div>
        </div>
        <div>
            <h1 class="fw-bold fs-3 mb-1">
                <?= htmlspecialchars(__('page.addresses.new.title', 'Нова адреса доставки'), ENT_QUOTES, 'UTF-8'); ?>
            </h1>
            <p class="text-muted fs-6 mb-0">
                <?= __('page.addresses.new.subtitle'); ?>
            </p>
        </div>
    </div>

    <?php if (!empty($flash)): ?>
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="<?= htmlspecialchars(__('ui.close', 'Закрити'), ENT_QUOTES, 'UTF-8'); ?>"></button>
        </div>
    <?php endif; ?>

    <!-- КАРТКА З ФОРМОЮ -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-md-4">
            <form action="/profile/addresses/store" method="post" class="row g-3">
                <input type="hidden" name="_csrf"
                       value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">

                <div class="col-12 col-md-6">
                    <label for="full_name" class="form-label">
                        <?= __('page.addresses.new.form.full_name.label'); ?>
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        id="full_name"
                        name="full_name"
                        placeholder="<?= htmlspecialchars(__('page.addresses.new.form.full_name.placeholder'), ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >
                </div>

                <div class="col-12 col-md-6">
                    <label for="phone" class="form-label">
                        <?= __('page.addresses.new.form.phone.label'); ?>
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        id="phone"
                        name="phone"
                        placeholder="<?= htmlspecialchars(__('page.addresses.new.form.phone.placeholder'), ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >
                </div>

                <div class="col-12 col-md-4">
                    <label for="country" class="form-label">
                        <?= __('page.addresses.new.form.country.label'); ?>
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        id="country"
                        name="country"
                        value="<?= htmlspecialchars(__('page.addresses.new.form.country.default', 'Україна'), ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >
                </div>

                <div class="col-12 col-md-4">
                    <label for="region" class="form-label">
                        <?= __('page.addresses.new.form.region.label'); ?>
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        id="region"
                        name="region"
                        placeholder="<?= htmlspecialchars(__('page.addresses.new.form.region.placeholder'), ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >
                </div>

                <div class="col-12 col-md-4">
                    <label for="city" class="form-label">
                        <?= __('page.addresses.new.form.city.label'); ?>
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        id="city"
                        name="city"
                        placeholder="<?= htmlspecialchars(__('page.addresses.new.form.city.placeholder'), ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >
                </div>

                <div class="col-12 col-md-4">
                    <label for="postal_code" class="form-label">
                        <?= __('page.addresses.new.form.postal_code.label'); ?>
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        id="postal_code"
                        name="postal_code"
                        placeholder="<?= htmlspecialchars(__('page.addresses.new.form.postal_code.placeholder'), ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >
                </div>

                <div class="col-12 col-md-8">
                    <label for="street_address" class="form-label">
                        <?= __('page.addresses.new.form.street.label'); ?>
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        id="street_address"
                        name="street_address"
                        placeholder="<?= htmlspecialchars(__('page.addresses.new.form.street.placeholder'), ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >
                </div>

                <div class="col-12">
                    <label for="comment" class="form-label">
                        <?= __('page.addresses.new.form.comment.label'); ?>
                    </label>
                    <textarea
                        class="form-control"
                        id="comment"
                        name="comment"
                        rows="2"
                        placeholder="<?= htmlspecialchars(__('page.addresses.new.form.comment.placeholder'), ENT_QUOTES, 'UTF-8'); ?>"
                    ></textarea>
                </div>

                <div class="col-12 d-flex justify-content-end pt-2">
                    <a href="/profile/addresses" class="btn btn-outline-secondary me-2">
                        <?= __('page.addresses.new.form.cancel'); ?>
                    </a>
                    <button type="submit" class="btn btn-warning text-dark fw-semibold">
                        <?= __('page.addresses.new.form.save'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

</section>
