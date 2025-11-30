
import { dbQuery, pool } from '../db/index.js';



/**
 * approveReview function.
 * @param {*} reviewId
 * @returns {Promise<*>}
 */
export async function approveReview(reviewId) {
    const { rowCount } = await dbQuery(
        `UPDATE product_reviews SET is_approved = true WHERE id = $1`,
        [reviewId],
    );
    return rowCount > 0;
}

/**
 * createAdminBrand function.
 * @param {*} arg1
 * @returns {Promise<*>}
 */
export async function createAdminBrand({ name, slug, is_active }) {
    const rawName = (name ?? '').toString().trim();
    if (!rawName) {
        throw new Error('Brand name is required');
    }

    const rawSlug = (slug ?? '').toString().trim();
    let baseSlug = rawSlug || rawName;
    let finalSlug = slugifyBrand(baseSlug);

    if (!finalSlug) {
        finalSlug = 'brand-' + Date.now();
    }

    const { rows } = await dbQuery(
        `
    INSERT INTO brands (name, slug, is_active)
    VALUES ($1, $2, COALESCE($3, TRUE))
    RETURNING id, name, slug, is_active
  `,
        [
            rawName,
            finalSlug,
            typeof is_active === 'boolean' ? is_active : null,
        ]
    );

    return rows[0] || null;
}

/**
 * createAdminCategory function.
 * @param {*} data
 * @returns {Promise<*>}
 */
export async function createAdminCategory(data = {}) {
    const name = (data.name ?? '').toString().trim();
    if (!name) {
        throw new Error('Category name is required');
    }

    const slug =
        (data.slug ?? '').toString().trim() ||
        name
            .toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^a-z0-9\-]+/g, '');

    let parentId = data.parent_id;

    if (
        parentId === undefined ||
        parentId === null ||
        parentId === '' ||
        parentId === 'null'
    ) {
        parentId = null;
    } else {
        parentId = Number(parentId);
        if (!Number.isInteger(parentId) || parentId <= 0) {
            parentId = null;
        }
    }

    const isActive =
        data.is_active === true ||
        data.is_active === '1' ||
        data.is_active === 'on';

    const { rows } = await dbQuery(
        `
    INSERT INTO categories (name, slug, parent_id, is_active)
    VALUES ($1, $2, $3, $4)
    RETURNING id, name, slug, parent_id, is_active
  `,
        [name, slug, parentId, isActive]
    );
    return rows[0] || null;
}

/**
 * createAdminProduct function.
 * @param {*} data
 * @returns {Promise<*>}
 */
export async function createAdminProduct(data = {}) {
    const name = (data.name ?? '').toString().trim();
    if (!name) throw new Error('Product name is required');

    const slugify = (s) => String(s || '')
        .toLowerCase().trim()
        .replace(/\s+/g, '-')
        .replace(/[^a-z0-9\-]/g, '')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');

    const rawSlug = (data.slug ?? '').toString().trim();
    const slug = rawSlug ? slugify(rawSlug) : slugify(name);

    const sku = (data.sku ?? '').toString().trim() || null;

    const brandId = (data.brand_id == null || Number.isNaN(Number(data.brand_id)))
        ? null : Number(data.brand_id);

    const categoryId = (data.category_id == null || Number.isNaN(Number(data.category_id)))
        ? null : Number(data.category_id);

    const shortDesc = data.short_description ?? null; 
    const description = data.description ?? null;

    const isActive =
        data.is_active === true ||
        data.is_active === '1' ||
        data.is_active === 'on';

    
    const { rows } = await dbQuery(
        `
    INSERT INTO products
      (name, slug, sku, brand_id, category_id, short_desc, description, is_active)
    VALUES
      ($1,   $2,   $3,  $4,       $5,         $6,         $7,          $8)
    RETURNING
      id, name, slug, sku, brand_id, category_id,
      short_desc AS short_description,  -- ← відразу віддаємо як short_description
      description, is_active, created_at, updated_at
    `,
        [name, slug, sku, brandId, categoryId, shortDesc, description, isActive]
    );

    return rows[0] || null;
}

/**
 * deleteAdminBrand function.
 * @param {*} id
 * @returns {Promise<*>}
 */
export async function deleteAdminBrand(id) {
    const { rowCount } = await dbQuery(
        `DELETE FROM brands WHERE id = $1`,
        [id],
    );
    return rowCount > 0;
}

/**
 * deleteAdminCategory function.
 * @param {*} id
 * @returns {Promise<*>}
 */
export async function deleteAdminCategory(id) {
    const { rowCount } = await dbQuery(
        `DELETE FROM categories WHERE id = $1`,
        [id],
    );
    return rowCount > 0;
}

/**
 * deleteAdminDiscount function.
 * @param {*} id
 * @returns {Promise<*>}
 */
export async function deleteAdminDiscount(id) {
    const { rowCount } = await dbQuery(`DELETE FROM discounts WHERE id = $1`, [id]);
    return rowCount > 0;
}

/**
 * deleteReview function.
 * @param {*} reviewId
 * @returns {Promise<*>}
 */
export async function deleteReview(reviewId) {
    const { rowCount } = await dbQuery(
        `DELETE FROM product_reviews WHERE id = $1`,
        [reviewId],
    );
    return rowCount > 0;
}

/**
 * deleteUserSessions function.
 * @param {*} userId
 * @returns {Promise<*>}
 */
export async function deleteUserSessions(userId) {
    await dbQuery(`DELETE FROM user_sessions WHERE user_id = $1`, [userId]);
}

/**
 * getAdminAttributeOptions function.
 * @returns {Promise<*>}
 */
export async function getAdminAttributeOptions() {
    
    const { rows } = await dbQuery(
        `
    SELECT
      ao.id,
      ao.attribute_id,
      ao.value,
      ao.sort_order
    FROM attribute_options ao
    ORDER BY ao.attribute_id ASC, ao.sort_order ASC, ao.value ASC
    `
    );

    const byAttr = {};
    for (const r of rows) {
        if (!byAttr[r.attribute_id]) byAttr[r.attribute_id] = [];
        byAttr[r.attribute_id].push(r);
    }
    return byAttr;
}

/**
 * getAdminAttributes function.
 * @returns {Promise<*>}
 */
export async function getAdminAttributes() {
    const { rows } = await dbQuery(
        `
    SELECT
      a.id,
      a.code,
      a.name,
      a.unit,
      a.data_type
    FROM attributes a
    ORDER BY a.name ASC
    `
    );
    return rows;
}

/**
 * getAdminBrand function.
 * @param {*} id
 * @returns {Promise<*>}
 */
export async function getAdminBrand(id) {
    const sql = `
    SELECT
      id,
      name,
      slug,
      is_active
    FROM brands
    WHERE id = $1
  `;
    const { rows } = await dbQuery(sql, [id]);
    return rows[0] || null;
}

/**
 * getAdminBrands function.
 * @param {*} filters
 * @returns {Promise<*>}
 */
export async function getAdminBrands(filters = {}) {
    const { q, with_products, popular } = filters;

    const params = [];
    const where = [];
    let idx = 1;

    if (q) {
        where.push(`(b.name ILIKE $${idx} OR b.slug ILIKE $${idx})`);
        params.push(`%${q}%`);
        idx++;
    }

    const whereSql = where.length ? `WHERE ${where.join(' AND ')}` : '';

    const sql = `
    SELECT
      b.id,
      b.name,
      b.slug,
      b.is_active,
      COUNT(p.id) AS products_count
    FROM brands b
    LEFT JOIN products p ON p.brand_id = b.id
    ${whereSql}
    GROUP BY b.id
    HAVING
      (${with_products ? 'COUNT(p.id) > 0' : 'COUNT(p.id) >= 0'})
    ORDER BY
      ${popular ? 'products_count DESC, b.name ASC' : 'b.name ASC'}
  `;

    const { rows } = await dbQuery(sql, params);
    return rows;
}

/**
 * getAdminCategories function.
 * @param {*} filters
 * @returns {Promise<*>}
 */
export async function getAdminCategories(filters = {}) {
    const { q, parent } = filters;

    const params = [];
    const where = [];
    let idx = 1;

    if (q) {
        where.push(`(c.name ILIKE $${idx} OR c.slug ILIKE $${idx})`);
        params.push(`%${q}%`);
        idx++;
    }

    if (parent) {
        where.push(`c.parent_id = $${idx}`);
        params.push(Number(parent));
        idx++;
    }

    const whereSql = where.length ? `WHERE ${where.join(' AND ')}` : '';

    const sql = `
    SELECT
      c.id,
      c.name,
      c.slug,
      c.parent_id,
      c.is_active,
      COUNT(p.id) AS products_count
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id
    ${whereSql}
    GROUP BY c.id
    ORDER BY c.name ASC
  `;

    const { rows } = await dbQuery(sql, params);
    return rows;
}

/**
 * getAdminCategoryById function.
 * @param {*} id
 * @returns {Promise<*>}
 */
export async function getAdminCategoryById(id) {
    const sql = `
    SELECT
      c.id,
      c.name,
      c.slug,
      c.parent_id,
      c.is_active,
      p.name AS parent_name
    FROM categories c
    LEFT JOIN categories p ON p.id = c.parent_id
    WHERE c.id = $1
  `;
    const { rows } = await dbQuery(sql, [id]);
    return rows[0] || null;
}

/**
 * getAdminDiscountById function.
 * @param {*} id
 * @returns {Promise<*>}
 */
export async function getAdminDiscountById(id) {
    const sql = `
    SELECT
      d.id,
      d.name,
      d.description,
      d.discount_type,
      d.value,
      d.date_from,
      d.date_to,
      -- локальні дати для форми
      TO_CHAR(d.date_from AT TIME ZONE 'Europe/Kyiv','YYYY-MM-DD') AS date_from_local,
      TO_CHAR(d.date_to   AT TIME ZONE 'Europe/Kyiv','YYYY-MM-DD') AS date_to_local,
      d.active AS is_active,
      d.min_order_sum,
      d.code
    FROM discounts d
    WHERE d.id = $1
  `;
    const { rows } = await dbQuery(sql, [id]);
    return rows[0] || null;
}

/**
 * getAdminDiscounts function.
 * @param {*} filters
 * @returns {Promise<*>}
 */
export async function getAdminDiscounts(filters = {}) {
    let { q, status, page = 1, perPage = 20 } = filters;

    page = Number(page) || 1;
    perPage = Number(perPage) || 20;
    if (perPage > 100) perPage = 100;
    const offset = (page - 1) * perPage;

    const params = [];
    const where = [];
    let idx = 1;

    if (q) {
        where.push(`(d.name ILIKE $${idx} OR d.description ILIKE $${idx})`);
        params.push(`%${q}%`);
        idx++;
    }

    if (status === 'active') where.push(`d.active = TRUE`);
    else if (status === 'inactive') where.push(`d.active = FALSE`);

    const whereSql = where.length ? `WHERE ${where.join(' AND ')}` : '';

    const baseSql = `
    FROM discounts d
    ${whereSql}
  `;

    const dataSql = `
    SELECT
      d.id,
      d.name,
      d.description,
      d.discount_type,
      d.value,
      d.date_from,
      d.date_to,
      -- локальні дати для <input type="date">
      TO_CHAR(d.date_from AT TIME ZONE 'Europe/Kyiv','YYYY-MM-DD') AS date_from_local,
      TO_CHAR(d.date_to   AT TIME ZONE 'Europe/Kyiv','YYYY-MM-DD') AS date_to_local,
      d.active AS is_active,
      d.min_order_sum,
      CASE
        WHEN d.discount_type = 'percent' THEN d.value::text || '%'
        WHEN d.discount_type = 'fixed'   THEN d.value::text
        ELSE d.value::text
      END AS value_label
    ${baseSql}
    ORDER BY
      COALESCE(d.date_from, d.date_to) DESC NULLS LAST,
      d.id DESC
    LIMIT ${perPage} OFFSET ${offset}
  `;

    const countSql = `
    SELECT COUNT(*) AS cnt
    ${baseSql}
  `;

    const [dataRes, countRes] = await Promise.all([
        dbQuery(dataSql, params),
        dbQuery(countSql, params),
    ]);

    const total = Number(countRes.rows[0]?.cnt || 0);
    const totalPages = total > 0 ? Math.ceil(total / perPage) : 1;

    return { items: dataRes.rows, page, perPage, total, totalPages };
}

/**
 * getAdminOrderDetails function.
 * @param {*} orderId
 * @returns {Promise<*>}
 */
export async function getAdminOrderDetails(orderId) {
    const orderSql = `
    SELECT
      o.id,
      o.order_number,
      o.status_code,
      o.total_products,
      o.total_discount,
      o.total_delivery,
      o.total_amount,
      o.currency,
      o.customer_comment,
      o.manager_comment,
      o.created_at,
      o.updated_at,
      COALESCE(
        NULLIF(TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))), ''),
        u.email,
        u.login
      ) AS customer_name,
      addr.city           AS shipping_city,
      addr.street_address AS shipping_street,
      addr.postal_code    AS shipping_postal_code
    FROM orders o
    LEFT JOIN users u   ON u.id   = o.user_id
    LEFT JOIN addresses addr ON addr.id = o.shipping_address_id
    WHERE o.id = $1
  `;

    const itemsSql = `
    SELECT
      id,
      product_id,
      product_name,
      sku,
      quantity,
      price,
      discount,
      total,
      currency
    FROM order_items
    WHERE order_id = $1
    ORDER BY id
  `;

    const statusSql = `
    SELECT
      h.id,
      h.old_status,
      h.new_status,
      h.comment,
      h.changed_at,
      COALESCE(
        NULLIF(TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))), ''),
        u.email,
        u.login
      ) AS changed_by_name
    FROM order_status_history h
    LEFT JOIN users u ON u.id = h.changed_by
    WHERE h.order_id = $1
    ORDER BY h.changed_at
  `;

    const paymentsSql = `
    SELECT
      id,
      amount,
      currency,
      provider,
      provider_ref,
      status,
      created_at
    FROM payments
    WHERE order_id = $1
    ORDER BY created_at
  `;

    const [orderRes, itemsRes, statusRes, paymentsRes] = await Promise.all([
        dbQuery(orderSql, [orderId]),
        dbQuery(itemsSql, [orderId]),
        dbQuery(statusSql, [orderId]),
        dbQuery(paymentsSql, [orderId]),
    ]);

    if (!orderRes.rows[0]) return null;

    return {
        order: orderRes.rows[0],
        items: itemsRes.rows,
        status_history: statusRes.rows,
        payments: paymentsRes.rows,
    };
}

/**
 * getAdminOrders function.
 * @param {*} filters
 * @returns {Promise<*>}
 */
export async function getAdminOrders(filters = {}) {
    let {
        status,
        from_date,
        to_date,
        q,
        limit,
        from,
        to,
    } = filters;

    if (!from_date && from) from_date = from;
    if (!to_date && to) to_date = to;

    const isValidDate = (value) => {
        if (!value || typeof value !== 'string') return false;
        const d = new Date(value);
        return !Number.isNaN(d.getTime());
    };

    const where = [];
    const params = [];
    let idx = 1;

    if (status) {
        where.push(`o.status_code = $${idx}`);
        params.push(status);
        idx++;
    }
    if (isValidDate(from_date)) {
        where.push(`o.created_at >= $${idx}`);
        params.push(from_date);
        idx++;
    }
    if (isValidDate(to_date)) {
        where.push(`o.created_at <= $${idx}`);
        params.push(to_date);
        idx++;
    }
    if (q) {
        where.push(
            `(o.order_number ILIKE $${idx} OR COALESCE(u.email, '') ILIKE $${idx} OR COALESCE(u.phone, '') ILIKE $${idx})`,
        );
        params.push(`%${q}%`);
        idx++;
    }

    const whereSql = where.length ? `WHERE ${where.join(' AND ')}` : '';

    let safeLimit = 200;
    const l = Number(limit);
    if (!Number.isNaN(l) && l > 0 && l <= 500) safeLimit = l;

    const sql = `
    SELECT
      o.id,
      o.order_number,
      o.status_code,
      o.total_amount,
      o.currency,
      o.created_at,
      COALESCE(
        NULLIF(TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))), ''),
        u.email,
        u.login
      ) AS customer_name
    FROM orders o
    LEFT JOIN users u ON u.id = o.user_id
    ${whereSql}
    ORDER BY o.created_at DESC
    LIMIT ${safeLimit}
  `;

    const { rows } = await dbQuery(sql, params);
    return rows;
}

/**
 * getAdminProductDetails function.
 * @param {*} id
 * @returns {Promise<*>}
 */
export async function getAdminProductDetails(id) {
    const productSql = `
    SELECT
      p.id,
      p.name,
      p.slug,
      p.sku,
      p.brand_id,
      p.category_id,
      p.short_desc AS short_description,  -- ← аліас
      p.description,
      p.is_active,
      p.created_at,
      p.updated_at,
      b.name AS brand_name,
      c.name AS category_name
    FROM products p
    LEFT JOIN brands b     ON b.id = p.brand_id
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.id = $1
  `;

    const offersSql = `
    SELECT
      po.*
    FROM product_offers po
    WHERE po.product_id = $1
    ORDER BY po.id
  `;

    const [prodRes, offersRes] = await Promise.all([
        dbQuery(productSql, [id]),
        dbQuery(offersSql, [id]),
    ]);

    if (!prodRes.rows[0]) return null;

    return {
        product: prodRes.rows[0],
        offers: offersRes.rows,
    };
}

/**
 * getAdminProducts function.
 * @param {*} filters
 * @returns {Promise<*>}
 */
export async function getAdminProducts(filters = {}) {
    let {
        q,
        status,
        category_id,
        brand_id,
        stock,
        page = 1,
        perPage = 20,
    } = filters;

    page = Number(page) || 1;
    perPage = Number(perPage) || 20;
    if (perPage > 100) perPage = 100;
    const offset = (page - 1) * perPage;

    const where = [];
    const params = [];
    let idx = 1;

    if (q) {
        where.push(
            `(p.name ILIKE $${idx} OR p.slug ILIKE $${idx} OR p.sku ILIKE $${idx})`,
        );
        params.push(`%${q}%`);
        idx++;
    }

    if (status === 'active') {
        where.push(`p.is_active = TRUE`);
    } else if (status === 'inactive') {
        where.push(`p.is_active = FALSE`);
    }

    if (category_id) {
        where.push(`p.category_id = $${idx}`);
        params.push(Number(category_id));
        idx++;
    }

    if (brand_id) {
        where.push(`p.brand_id = $${idx}`);
        params.push(Number(brand_id));
        idx++;
    }

    let stockCondition = '';
    if (stock === 'low') {
        stockCondition = `
      AND EXISTS (
        SELECT 1
        FROM product_offers po
        WHERE po.product_id = p.id
          AND po.is_active = TRUE
          AND po.quantity <= 1
      )
    `;
    } else if (stock === 'out') {
        stockCondition = `
      AND NOT EXISTS (
        SELECT 1
        FROM product_offers po
        WHERE po.product_id = p.id
          AND po.is_active = TRUE
          AND po.quantity > 0
      )
    `;
    }

    const whereSql =
        where.length || stockCondition
            ? `WHERE ${where.join(' AND ') || 'TRUE'} ${stockCondition}`
            : '';

    const baseSql = `
    FROM products p
    LEFT JOIN brands b     ON b.id = p.brand_id
    LEFT JOIN categories c ON c.id = p.category_id
    ${whereSql}
  `;

    const dataSql = `
    SELECT
      p.id,
      p.name,
      p.slug,
      p.sku,
      p.is_active,
      p.created_at,
      b.name AS brand_name,
      c.name AS category_name,
      (
        SELECT MIN(po.sale_price)
        FROM product_offers po
        WHERE po.product_id = p.id
          AND po.is_active = TRUE
      ) AS price,
      (
        SELECT po.currency
        FROM product_offers po
        WHERE po.product_id = p.id
          AND po.is_active = TRUE
        LIMIT 1
      ) AS currency
    ${baseSql}
    ORDER BY p.created_at DESC
    LIMIT ${perPage} OFFSET ${offset}
  `;

    const countSql = `
    SELECT COUNT(*) AS cnt
    ${baseSql}
  `;

    const [dataRes, countRes] = await Promise.all([
        dbQuery(dataSql, params),
        dbQuery(countSql, params),
    ]);

    const total = Number(countRes.rows[0]?.cnt || 0);
    const totalPages = total > 0 ? Math.ceil(total / perPage) : 1;

    return {
        items: dataRes.rows,
        page,
        perPage,
        total,
        totalPages,
    };
}

/**
 * getAdminStock function.
 * @param {*} filters
 * @returns {Promise<*>}
 */
export async function getAdminStock(filters = {}) {
    const { warehouse_id, stock, q } = filters;

    const params = [];
    const where = [];
    let idx = 1;

    if (warehouse_id) {
        where.push(`po.warehouse_id = $${idx}`);
        params.push(Number(warehouse_id));
        idx++;
    }

    if (q) {
        where.push(`(p.name ILIKE $${idx} OR p.sku ILIKE $${idx})`);
        params.push(`%${q}%`);
        idx++;
    }

    if (stock === 'low') {
        where.push(`po.quantity <= 1`);
    } else if (stock === 'out') {
        where.push(`po.quantity <= 0`);
    }

    const whereSql = where.length ? `WHERE ${where.join(' AND ')}` : '';

    const sql = `
    SELECT
      po.id,
      po.product_id,
      p.sku,
      po.quantity,
      po.is_active,
      po.warehouse_id,
      p.name AS product_name,
      w.name AS warehouse_name
    FROM product_offers po
    JOIN products   p ON p.id   = po.product_id
    LEFT JOIN warehouses w ON w.id = po.warehouse_id
    ${whereSql}
    ORDER BY po.quantity ASC, p.name ASC
  `;

    const { rows } = await dbQuery(sql, params);
    return rows;
}

/**
 * getAdminWarehouses function.
 * @returns {Promise<*>}
 */
export async function getAdminWarehouses() {
    const { rows } = await dbQuery(
        `
    SELECT
      id,
      name,
      code,
      city
    FROM warehouses
    ORDER BY name ASC
  `,
    );
    return rows;
}

/**
 * getAllUsersWithRoles function.
 * @param {*} arg1
 * @returns {Promise<*>}
 */
export async function getAllUsersWithRoles({ q = '', role = '', status = '' } = {}) {
    const params = [];
    const where = [];
    let i = 1;

    if (q) {
        where.push(`(
      u.login ILIKE $${i} OR u.email ILIKE $${i} OR COALESCE(u.phone,'') ILIKE $${i}
      OR COALESCE(u.first_name,'') ILIKE $${i} OR COALESCE(u.last_name,'') ILIKE $${i}
    )`);
        params.push(`%${q}%`);
        i++;
    }

    if (role) {
        where.push(`EXISTS (
      SELECT 1
      FROM user_roles ur
      JOIN roles r ON r.id = ur.role_id
      WHERE ur.user_id = u.id AND r.code = $${i}
    )`);
        params.push(role);
        i++;
    }

    if (status === 'active') {
        where.push(`u.is_active = TRUE`);
    } else if (status === 'blocked') {
        where.push(`u.is_active = FALSE`);
    } else if (status === 'inactive') {
        where.push(`u.is_active = TRUE AND NOT EXISTS (
      SELECT 1 FROM user_sessions s
      WHERE s.user_id = u.id AND s.created_at >= NOW() - INTERVAL '30 days'
    )`);
    }

    const whereSql = where.length ? `WHERE ${where.join(' AND ')}` : '';

    const { rows } = await dbQuery(
        `
    SELECT
      u.id,
      u.email,
      u.login,
      u.first_name,
      u.last_name,
      u.phone,
      u.is_active,
      TO_CHAR(u.created_at,'YYYY-MM-DD HH24:MI') AS created_at,
      COALESCE(
        json_agg(json_build_object('id', r.id, 'code', r.code, 'name', r.name))
        FILTER (WHERE r.id IS NOT NULL),
        '[]'
      ) AS roles
    FROM users u
    LEFT JOIN user_roles ur ON ur.user_id = u.id
    LEFT JOIN roles r ON r.id = ur.role_id
    ${whereSql}
    GROUP BY u.id
    ORDER BY u.created_at DESC
    `,
        params
    );

    return rows;
}

/**
 * getDashboardStats function.
 * @param {*} period
 * @returns {Promise<*>}
 */
export async function getDashboardStats(period = 'today') {
    const allowed = ['today', 'week', 'month', 'all'];
    const p = allowed.includes(period) ? period : 'today';

    const periodExpr = `
    CASE
      WHEN $1 = 'today' THEN NOW()::date
      WHEN $1 = 'week'  THEN NOW() - INTERVAL '7 days'
      WHEN $1 = 'month' THEN NOW() - INTERVAL '30 days'
      ELSE NOW() - INTERVAL '100 years'
    END
  `;

    const sql = `
    SELECT
      (SELECT COUNT(*) FROM users) AS users_count,
      (SELECT COUNT(*) FROM users WHERE created_at >= (${periodExpr})) AS users_new_today,

      (SELECT COUNT(*) FROM orders WHERE created_at >= (${periodExpr})) AS orders_today,
      (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE created_at >= (${periodExpr})) AS sales_today,
      (SELECT COALESCE(AVG(total_amount), 0) FROM orders WHERE created_at >= (${periodExpr})) AS avg_order_value,

      (SELECT COUNT(*) FROM orders WHERE status_code = 'pending' AND created_at >= (${periodExpr}))
        AS orders_pending_today,
      (SELECT COUNT(*) FROM orders WHERE status_code = 'paid' AND created_at >= (${periodExpr}))
        AS orders_paid_today,
      (SELECT COUNT(*) FROM orders WHERE status_code = 'shipped' AND created_at >= (${periodExpr}))
        AS orders_shipped_today,

      (SELECT COUNT(*) FROM orders) AS orders_count,

      (SELECT COUNT(*) FROM products) AS products_count,
      (SELECT COUNT(*) FROM products WHERE is_active = TRUE) AS products_active,
      (SELECT COUNT(*) FROM categories) AS categories_count,
      (SELECT COUNT(*) FROM brands) AS brands_count,

      (SELECT COUNT(*) FROM product_reviews WHERE is_approved = FALSE) AS pending_reviews,
      (SELECT COUNT(*) FROM product_reviews WHERE is_approved = TRUE) AS reviews_count,
      (SELECT COALESCE(ROUND(AVG(rating)::numeric, 1), 0.0)
         FROM product_reviews
         WHERE is_approved = TRUE) AS avg_rating,

      (SELECT COUNT(*) FROM support_tickets WHERE status = 'open') AS open_tickets,

      (SELECT COUNT(*) FROM product_offers WHERE is_active = TRUE AND quantity <= 1) AS low_stock_offers,
      (
        SELECT COALESCE(
          ROUND(
            (SELECT COUNT(*) FROM product_offers WHERE is_active = TRUE AND quantity <= 1)::numeric * 100 /
            NULLIF((SELECT COUNT(*) FROM product_offers WHERE is_active = TRUE), 0),
            0
          ),
          0
        )
      ) AS low_stock_percent,

      (
        SELECT c.name
        FROM categories c
        JOIN products p ON p.category_id = c.id
        GROUP BY c.id
        ORDER BY COUNT(*) DESC
        LIMIT 1
      ) AS top_category_name,

      (
        SELECT b.name
        FROM brands b
        JOIN products p ON p.brand_id = b.id
        GROUP BY b.id
        ORDER BY COUNT(*) DESC
        LIMIT 1
      ) AS top_brand_name,

      0 AS active_coupons,
      0 AS active_campaigns
  `;

    const { rows } = await dbQuery(sql, [p]);
    return rows[0] || {};
}

/**
 * getPendingReviews function.
 * @returns {Promise<*>}
 */
export async function getPendingReviews() {
    const { rows } = await dbQuery(
        `
    SELECT
      pr.id,
      pr.product_id,
      pr.user_id,
      pr.rating,
      pr.title,
      pr.body,
      pr.created_at,
      p.name AS product_name,
      u.email AS user_email,
      COALESCE(
        NULLIF(TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))), ''),
        u.login
      ) AS user_name
    FROM product_reviews pr
    LEFT JOIN products p ON p.id = pr.product_id
    LEFT JOIN users u    ON u.id = pr.user_id
    WHERE pr.is_approved = false
    ORDER BY pr.created_at ASC
  `,
    );
    return rows;
}

/**
 * getSupportTickets function.
 * @param {*} arg1
 * @returns {Promise<*>}
 */
export async function getSupportTickets({ status, q } = {}) {
    const params = [];
    const where = [];
    let i = 1;

    if (status) {
        where.push(`st.status = $${i++}`);
        params.push(status);
    }
    if (q) {
        where.push(`(st.email ILIKE $${i} OR st.subject ILIKE $${i} OR st.message ILIKE $${i})`);
        params.push(`%${q}%`);
        i++;
    }
    const whereSql = where.length ? `WHERE ${where.join(' AND ')}` : '';

    const { rows } = await dbQuery(
        `SELECT
       st.id,
       st.user_id,
       COALESCE(NULLIF(TRIM(CONCAT(u.first_name,' ',u.last_name)),''), u.email, u.login) AS customer_name,
       COALESCE(u.email, st.email) AS customer_email,
       st.subject,
       st.message,
       st.status,
       TO_CHAR(st.created_at,'YYYY-MM-DD HH24:MI') AS created_at
     FROM support_tickets st
     LEFT JOIN users u ON u.id = st.user_id
     ${whereSql}
     ORDER BY st.created_at DESC`,
        params
    );
    return rows;
}

/**
 * getSupportTicketThread function.
 * @param {*} ticketId
 * @returns {Promise<*>}
 */
export async function getSupportTicketThread(ticketId) {
    const tRes = await dbQuery(
        `SELECT
       st.id, st.user_id, st.email, st.subject, st.message, st.status,
       TO_CHAR(st.created_at,'YYYY-MM-DD HH24:MI') AS created_at,
       COALESCE(NULLIF(TRIM(CONCAT(u.first_name,' ',u.last_name)),''), u.email, u.login) AS customer_name,
       COALESCE(u.email, st.email) AS customer_email
     FROM support_tickets st
     LEFT JOIN users u ON u.id = st.user_id
     WHERE st.id = $1`,
        [ticketId]
    );
    const ticket = tRes.rows[0];
    if (!ticket) return null;

    const mRes = await dbQuery(
        `SELECT
       sm.id,
       sm.author_id,
       sm.is_staff,
       sm.body,
       TO_CHAR(sm.created_at,'YYYY-MM-DD HH24:MI') AS created_at,
       CASE
         WHEN sm.is_staff THEN
           COALESCE(NULLIF(TRIM(CONCAT(a.first_name,' ',a.last_name)),''), a.email, a.login, 'Підтримка')
         ELSE
           COALESCE(NULLIF(TRIM(CONCAT(u.first_name,' ',u.last_name)),''), u.email, u.login, 'Клієнт')
       END AS author_name
     FROM support_messages sm
     LEFT JOIN users u ON u.id = (SELECT user_id FROM support_tickets WHERE id = sm.ticket_id)
     LEFT JOIN users a ON a.id = sm.author_id
     WHERE sm.ticket_id = $1
     ORDER BY sm.created_at ASC`,
        [ticketId]
    );

    const messages = [
        {
            id: 0,
            author_id: ticket.user_id,
            is_staff: false,
            body: ticket.message,
            created_at: ticket.created_at,
            author_name: ticket.customer_name || 'Клієнт',
        },
        ...mRes.rows,
    ];

    return { ticket, messages };
}

/**
 * getUserSessions function.
 * @param {*} userId
 * @returns {Promise<*>}
 */
export async function getUserSessions(userId) {
    const { rows } = await dbQuery(
        `SELECT id, token, ip_address, user_agent,
            TO_CHAR(expires_at,'YYYY-MM-DD HH24:MI') AS expires_at,
            TO_CHAR(created_at,'YYYY-MM-DD HH24:MI') AS created_at
     FROM user_sessions
     WHERE user_id = $1
     ORDER BY created_at DESC`,
        [userId]
    );
    return rows;
}

/**
 * replyToSupportTicket function.
 * @param {*} ticketId
 * @param {*} arg2
 * @returns {Promise<*>}
 */
export async function replyToSupportTicket(ticketId, { staffUserId, body, closeTicket = false }) {
    const ins = await dbQuery(
        `INSERT INTO support_messages (ticket_id, author_id, is_staff, body, created_at)
     VALUES ($1, $2, TRUE, $3, NOW())
     RETURNING id`,
        [ticketId, staffUserId || null, body]
    );

    if (closeTicket) {
        await dbQuery(`UPDATE support_tickets SET status='closed', updated_at=NOW() WHERE id=$1`, [ticketId]);
    } else {
        await dbQuery(`UPDATE support_tickets SET updated_at=NOW() WHERE id=$1`, [ticketId]);
    }

    return ins.rows[0];
}

/**
 * updateAdminBrand function.
 * @param {*} id
 * @param {*} data
 * @returns {Promise<*>}
 */
export async function updateAdminBrand(id, data = {}) {
    const fields = [];
    const params = [];
    let idx = 1;

    if (data.name !== undefined) {
        fields.push(`name = $${idx++}`);
        params.push((data.name || '').toString().trim());
    }

    if (data.slug !== undefined) {
        const slug = (data.slug || '').toString().trim() || null;
        fields.push(`slug = $${idx++}`);
        params.push(slug);
    }

    if (data.is_active !== undefined) {
        fields.push(`is_active = $${idx++}`);
        params.push(Boolean(data.is_active));
    }

    if (fields.length === 0) return null;

    const sql = `
    UPDATE brands
    SET ${fields.join(', ')}
    WHERE id = $${idx}
    RETURNING id, name, slug, is_active
  `;

    params.push(id);

    const { rows } = await dbQuery(sql, params);
    return rows[0] || null;
}

/**
 * updateAdminCategory function.
 * @param {*} id
 * @param {*} data
 * @returns {Promise<*>}
 */
export async function updateAdminCategory(id, data = {}) {
    const fields = [];
    const params = [];
    let idx = 1;

    if (data.name !== undefined) {
        fields.push(`name = $${idx++}`);
        params.push(data.name);
    }

    if (data.slug !== undefined) {
        fields.push(`slug = $${idx++}`);
        params.push(data.slug);
    }

    if (data.parent_id !== undefined) {
        let parentId = data.parent_id;

        if (
            parentId === undefined ||
            parentId === null ||
            parentId === '' ||
            parentId === 'null'
        ) {
            parentId = null;
        } else {
            parentId = Number(parentId);
            if (!Number.isInteger(parentId) || parentId <= 0) {
                parentId = null;
            }
        }

        fields.push(`parent_id = $${idx++}`);
        params.push(parentId);
    }

    if (data.is_active !== undefined) {
        fields.push(`is_active = $${idx++}`);
        params.push(
            data.is_active === true ||
            data.is_active === '1' ||
            data.is_active === 'on',
        );
    }

    if (fields.length === 0) {
        return null;
    }

    const sql = `
    UPDATE categories
    SET ${fields.join(', ')}
    WHERE id = $${idx}
    RETURNING id, name, slug, parent_id, is_active
  `;

    params.push(id);

    const { rows } = await dbQuery(sql, params);
    return rows[0] || null;
}

/**
 * updateAdminDiscount function.
 * @param {*} id
 * @param {*} data
 * @returns {Promise<*>}
 */
export async function updateAdminDiscount(id, data = {}) {
    const fields = [];
    const params = [];
    let idx = 1;

    if (data.name !== undefined) {
        fields.push(`name = $${idx++}`);
        params.push((data.name ?? '').toString().trim());
    }

    if (data.description !== undefined) {
        fields.push(`description = $${idx++}`);
        params.push((data.description ?? '').toString().trim() || null);
    }

    if (data.discount_type !== undefined) {
        fields.push(`discount_type = $${idx++}`);
        params.push((data.discount_type ?? '').toString().trim());
    }

    if (data.value !== undefined) {
        fields.push(`value = $${idx++}`);
        params.push(Number(data.value));
    }

    if (data.date_from !== undefined) {
        fields.push(`
    date_from = CASE
      WHEN NULLIF($${idx}::text,'') IS NULL THEN NULL
      ELSE ((NULLIF($${idx}::text,'')::date)::timestamp AT TIME ZONE 'Europe/Kyiv')
    END
  `.trim());
        params.push((data.date_from ?? '').toString().trim());
        idx++;
    }

    if (data.date_to !== undefined) {
        fields.push(`
    date_to = CASE
      WHEN NULLIF($${idx}::text,'') IS NULL THEN NULL
      ELSE (((NULLIF($${idx}::text,'')::date + INTERVAL '1 day' - INTERVAL '1 microsecond')::timestamp) AT TIME ZONE 'Europe/Kyiv')
    END
  `.trim());
        params.push((data.date_to ?? '').toString().trim());
        idx++;
    }

    if (data.is_active !== undefined) {
        fields.push(`active = $${idx++}`);
        params.push(
            data.is_active === true || data.is_active === '1' || data.is_active === 'on'
        );
    }

    if (data.min_order_sum !== undefined) {
        fields.push(`min_order_sum = $${idx++}`);
        params.push(
            data.min_order_sum === '' || data.min_order_sum === null
                ? null
                : Number(data.min_order_sum)
        );
    }

    if (data.code !== undefined) {
        fields.push(`code = $${idx++}`);
        params.push((data.code ?? '').toString().trim() || null);
    }

    if (fields.length === 0) return null;

    const sql = `
    UPDATE discounts
    SET ${fields.join(', ')}
    WHERE id = $${idx}
    RETURNING id, name, description, discount_type, value, date_from, date_to, active, min_order_sum, code
  `;
    params.push(id);

    const { rows } = await dbQuery(sql, params);
    return rows[0] || null;
}

/**
 * updateAdminOrderStatus function.
 * @param {*} orderId
 * @param {*} options
 * @returns {Promise<*>}
 */
export async function updateAdminOrderStatus(orderId, options = {}) {
    const { status, is_paid, adminUserId } = options;

    const newStatus = (status ?? '').toString().trim();
    if (!newStatus) return null;

    const client = await pool.connect();
    try {
        await client.query('BEGIN');

        const currentRes = await client.query(
            `SELECT status_code FROM orders WHERE id = $1 FOR UPDATE`,
            [orderId],
        );
        if (!currentRes.rows[0]) {
            await client.query('ROLLBACK');
            return null;
        }
        const oldStatus = currentRes.rows[0].status_code;

        await client.query(
            `
      UPDATE orders
      SET status_code = $2,
          updated_at  = NOW()
      WHERE id = $1
    `,
            [orderId, newStatus],
        );

        await client.query(
            `
      INSERT INTO order_status_history (order_id, old_status, new_status, comment, changed_by, changed_at)
      VALUES ($1, $2, $3, $4, $5, NOW())
    `,
            [
                orderId,
                oldStatus,
                newStatus,
                is_paid ? 'Status updated & paid' : 'Status updated',
                adminUserId || null,
            ],
        );

        await client.query('COMMIT');
        return { id: orderId, status: newStatus };
    } catch (err) {
        await client.query('ROLLBACK');
        throw err;
    } finally {
        client.release();
    }
}

/**
 * updateAdminProduct function.
 * @param {*} id
 * @param {*} data
 * @returns {Promise<*>}
 */
export async function updateAdminProduct(id, data = {}) {
    const fields = [];
    const params = [];
    let idx = 1;

    if (data.name !== undefined) { fields.push(`name = $${idx++}`); params.push(data.name); }
    if (data.slug !== undefined) { fields.push(`slug = $${idx++}`); params.push(data.slug); }
    if (data.sku !== undefined) { fields.push(`sku  = $${idx++}`); params.push(data.sku); }

    if (data.brand_id !== undefined) {
        fields.push(`brand_id = $${idx++}`);
        params.push(data.brand_id === null || Number.isNaN(Number(data.brand_id)) ? null : Number(data.brand_id));
    }
    if (data.category_id !== undefined) {
        fields.push(`category_id = $${idx++}`);
        params.push(data.category_id === null || Number.isNaN(Number(data.category_id)) ? null : Number(data.category_id));
    }

    if (data.short_description !== undefined) {
        fields.push(`short_desc = $${idx++}`);         
        params.push(data.short_description);
    }
    if (data.description !== undefined) {
        fields.push(`description = $${idx++}`);
        params.push(data.description);
    }

    if (data.is_active !== undefined) {
        fields.push(`is_active = $${idx++}`);
        params.push(Boolean(data.is_active));
    }

    if (fields.length === 0) return null;

    const sql = `
    UPDATE products
    SET ${fields.join(', ')}, updated_at = NOW()
    WHERE id = $${idx}
    RETURNING id, name, slug, sku, brand_id, category_id,
              short_desc AS short_description,  -- ← віддаємо як short_description
              description, is_active, created_at, updated_at
  `;
    params.push(id);

    const { rows } = await dbQuery(sql, params);
    return rows[0] || null;
}

/**
 * updateSupportTicketStatus function.
 * @param {*} id
 * @param {*} status
 * @returns {Promise<*>}
 */
export async function updateSupportTicketStatus(id, status) {
    const { rows } = await dbQuery(
        `UPDATE support_tickets
     SET status = $2, updated_at = NOW()
     WHERE id = $1
     RETURNING id, status, updated_at`,
        [id, status]
    );
    return rows[0] || null;
}



export async function createAdminDiscount(data = {}) {
    const name = (data.name ?? '').toString().trim();
    if (!name) throw new Error('Discount name is required');

    const discount_type = (data.discount_type ?? '').toString().trim() || 'percent';
    const value = Number(data.value ?? 0);
    const description = (data.description ?? '').toString().trim() || null;

    
    const dateFromStr = (data.date_from ?? '').toString().trim();
    const dateToStr = (data.date_to ?? '').toString().trim();

    
    const date_from = dateFromStr === '' ? null : dateFromStr;
    const date_to = dateToStr === '' ? null : dateToStr;

    const active =
        data.is_active === true ||
        data.is_active === '1' ||
        data.is_active === 'on';

    const min_order_sum =
        data.min_order_sum !== undefined &&
            data.min_order_sum !== null &&
            data.min_order_sum !== ''
            ? Number(data.min_order_sum)
            : null;

    const code = (data.code ?? '').toString().trim() || null;

    const sql = `
    INSERT INTO discounts
      (name, description, discount_type, value, date_from, date_to, active, min_order_sum, code)
    VALUES
      ($1,   $2,          $3,            $4,    $5,        $6,      $7,     $8,           $9)
    RETURNING
      id, name, description, discount_type, value,
      date_from, date_to, active, min_order_sum, code
  `;

    const params = [
        name,           
        description,    
        discount_type,  
        value,          
        date_from,      
        date_to,        
        active,         
        min_order_sum,  
        code,           
    ];

    const { rows } = await dbQuery(sql, params);
    return rows[0] || null;
}


