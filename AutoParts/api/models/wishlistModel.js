import { dbQuery } from '../db/index.js';

/**
 * addToWishlist function.
 * @param {*} userId
 * @param {*} productId
 * @returns {Promise<*>}
 */
export async function addToWishlist(userId, productId) {
  // 1. Пробуємо знайти існуючий wishlist для користувача
  const { rows: existing } = await dbQuery(
    `
    SELECT id
    FROM wishlists
    WHERE user_id = $1
    LIMIT 1
    `,
    [userId],
  );

  let wishlistId;

  if (existing.length > 0) {
    // вже є wishlist
    wishlistId = existing[0].id;
  } else {
    // 2. Нема — створюємо новий
    const { rows: inserted } = await dbQuery(
      `
      INSERT INTO wishlists (user_id, created_at)
      VALUES ($1, NOW())
      RETURNING id
      `,
      [userId],
    );
    wishlistId = inserted[0].id;
  }

  // 3. Додаємо товар у wishlist_items, уникаючи дубля по (wishlist_id, product_id)
  await dbQuery(
    `
    INSERT INTO wishlist_items (wishlist_id, product_id, added_at)
    VALUES ($1, $2, NOW())
    ON CONFLICT (wishlist_id, product_id) DO NOTHING
    `,
    [wishlistId, productId],
  );

  return true;
}

/**
 * clearWishlist function.
 * @param {*} userId
 * @returns {Promise<*>}
 */
export async function clearWishlist(userId) {
    await dbQuery(
        `
    DELETE FROM wishlist_items wi
    USING wishlists w
    WHERE wi.wishlist_id = w.id
      AND w.user_id = $1
    `,
        [userId],
    );

    return true;
}

/**
 * getWishlist function.
 * @param {*} userId
 * @returns {Promise<*>}
 */
export async function getWishlist(userId) {
  const { rows } = await dbQuery(
    `
    SELECT
      wi.wishlist_id,
      wi.product_id,
      wi.added_at,

      p.id,
      p.slug,
      p.name,
      p.short_desc,
      pi.image_url AS image,                 -- ✅ тут ставиш СВОЮ реальну колонку
      b.name AS brand_name,

      price_data.price,
      price_data.in_stock
    FROM wishlist_items wi
    JOIN wishlists w
      ON w.id = wi.wishlist_id
    JOIN product_images pi
      ON pi.product_id = wi.product_id AND pi.is_main = TRUE
    JOIN products p
      ON p.id = wi.product_id
    LEFT JOIN brands b
      ON b.id = p.brand_id
    LEFT JOIN LATERAL (
      SELECT
        COALESCE(po.sale_price, po.base_price) AS price,
        (po.quantity > 0 AND po.is_active) AS in_stock
      FROM product_offers po
      WHERE po.product_id = p.id
        AND po.is_active = TRUE
      ORDER BY COALESCE(po.sale_price, po.base_price) ASC
      LIMIT 1
    ) AS price_data ON TRUE
    WHERE w.user_id = $1
    ORDER BY wi.added_at DESC
    `,
    [userId],
  );

  return rows;
}

/**
 * removeFromWishlist function.
 * @param {*} userId
 * @param {*} productId
 * @returns {Promise<*>}
 */
export async function removeFromWishlist(userId, productId) {
    const { rowCount } = await dbQuery(
        `
    DELETE FROM wishlist_items wi
    USING wishlists w
    WHERE wi.wishlist_id = w.id
      AND w.user_id = $1
      AND wi.product_id = $2
    `,
        [userId, productId],
    );

    return rowCount > 0;
}

