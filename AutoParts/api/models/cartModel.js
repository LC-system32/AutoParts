
import { dbQuery } from "../db/index.js";

/** Допоміжна: отримати актуальну ціну товару з product_offers */
async function getProductPrice(productId) {
  const { rows } = await dbQuery(
    `SELECT sale_price, base_price, currency
     FROM product_offers
     WHERE product_id = $1 AND is_active = TRUE
     ORDER BY sale_price NULLS LAST, base_price NULLS LAST
     LIMIT 1`,
    [productId]
  );

  const offer = rows[0];
  if (!offer) throw new Error("No active offers for this product");

  const price = offer.sale_price ?? offer.base_price;
  if (price == null) throw new Error("Offer has no price");

  return { price: Number(price), currency: offer.currency || "UAH" };
}

/** Створити кошик */
export async function createCart({ userId = null, sessionToken = null } = {}) {
  const { rows } = await dbQuery(
    `INSERT INTO carts (user_id, session_token)
     VALUES ($1, $2)
     RETURNING id, user_id, session_token, created_at, updated_at`,
    [userId, sessionToken]
  );
  return rows[0];
}

/** Отримати один рядок carts */
async function getCartRow(cartId) {
  const { rows } = await dbQuery(
    `SELECT id, user_id, session_token, created_at, updated_at
     FROM carts
     WHERE id = $1`,
    [cartId]
  );
  return rows[0] || null;
}

/** Повний кошик по ID (товари + сума) */
export async function getCart(cartId) {
  const numericCartId = Number(cartId);
  if (Number.isNaN(numericCartId) || numericCartId <= 0) {
    throw new Error("Invalid cartId");
  }

  const cart = await getCartRow(numericCartId);
  if (!cart) return null;

  const { rows: itemRows } = await dbQuery(
    `SELECT 
        ci.id,
        ci.product_id,
        ci.quantity,
        ci.price,
        ci.currency,
        p.id          AS product_exists_id,
        p.name        AS product_name,
        p.slug        AS product_slug,
        img.image_url AS image
     FROM cart_items ci
     LEFT JOIN products p 
            ON p.id = ci.product_id
     LEFT JOIN product_images img 
            ON img.product_id = ci.product_id 
           AND img.is_main = TRUE
     WHERE ci.cart_id = $1
     ORDER BY ci.id`,
    [numericCartId]
  );

  let total = 0;

  const items = itemRows.map((row) => {
    const price = Number(row.price);
    const qty = Number(row.quantity);
    const sum = price * qty;
    if (!Number.isNaN(sum)) total += sum;

    const hasProduct = row.product_exists_id != null;

    return {
      id: row.id,
      product_id: row.product_id,
      quantity: qty,
      price,
      currency: row.currency || "UAH",
      product: hasProduct
        ? {
            id: row.product_exists_id,
            name: row.product_name,
            slug: row.product_slug,
            image: row.image || null,
          }
        : null,
    };
  });

  return {
    id: cart.id,
    user_id: cart.user_id,
    session_token: cart.session_token,
    items,
    total,
    currency: items[0]?.currency || "UAH",
  };
}

/** Додати товар */
export async function addItem(cartId, productId, quantity) {
  const numericCartId = Number(cartId);
  if (Number.isNaN(numericCartId) || numericCartId <= 0) {
    throw new Error("Invalid cartId");
  }

  const pid = Number(productId);
  if (Number.isNaN(pid) || pid <= 0) {
    throw new Error("Invalid productId");
  }

  const qty = Number(quantity);
  if (Number.isNaN(qty) || qty <= 0) {
    throw new Error("Quantity must be positive");
  }

  const cart = await getCartRow(numericCartId);
  if (!cart) throw new Error(`Cart with id=${numericCartId} not found`);

  const { price, currency } = await getProductPrice(pid);

  const { rows: existingRows } = await dbQuery(
    `SELECT id, quantity
     FROM cart_items
     WHERE cart_id = $1 AND product_id = $2`,
    [numericCartId, pid]
  );

  const existing = existingRows[0];

  if (existing) {
    const newQty = Number(existing.quantity) + qty;
    await dbQuery(
      `UPDATE cart_items
       SET quantity = $1
       WHERE id = $2`,
      [newQty, existing.id]
    );
    return { id: existing.id, product_id: pid, quantity: newQty };
  }

  const { rows } = await dbQuery(
    `INSERT INTO cart_items (cart_id, product_id, quantity, price, currency)
     VALUES ($1, $2, $3, $4, $5)
     RETURNING id, product_id, quantity, price, currency`,
    [numericCartId, pid, qty, price, currency]
  );

  const row = rows[0];
  return {
    id: row.id,
    product_id: row.product_id,
    quantity: Number(row.quantity),
    price: Number(row.price),
    currency: row.currency,
  };
}

/** Оновити кількість */
export async function updateItem(cartId, itemId, quantity) {
  const numericCartId = Number(cartId);
  if (Number.isNaN(numericCartId) || numericCartId <= 0) {
    throw new Error("Invalid cartId");
  }

  const numericItemId = Number(itemId);
  if (Number.isNaN(numericItemId) || numericItemId <= 0) {
    throw new Error("Invalid itemId");
  }

  const qty = Number(quantity);
  if (Number.isNaN(qty) || qty <= 0) {
    throw new Error("Quantity must be positive");
  }

  const { rowCount } = await dbQuery(
    `UPDATE cart_items
     SET quantity = $1
     WHERE id = $2 AND cart_id = $3`,
    [qty, numericItemId, numericCartId]
  );

  if (!rowCount) throw new Error("Cart item not found");
  return { id: numericItemId, quantity: qty };
}

/** Видалити товар */
export async function removeItem(cartId, itemId) {
  const numericCartId = Number(cartId);
  if (Number.isNaN(numericCartId) || numericCartId <= 0) {
    throw new Error("Invalid cartId");
  }

  const numericItemId = Number(itemId);
  if (Number.isNaN(numericItemId) || numericItemId <= 0) {
    throw new Error("Invalid itemId");
  }

  const { rowCount } = await dbQuery(
    `DELETE FROM cart_items
     WHERE id = $1 AND cart_id = $2`,
    [numericItemId, numericCartId]
  );

  if (!rowCount) throw new Error("Cart item not found");
  return { id: numericItemId };
}

/** Очистити кошик */
export async function clearCart(cartId) {
  const numericCartId = Number(cartId);
  if (Number.isNaN(numericCartId) || numericCartId <= 0) {
    throw new Error("Invalid cartId");
  }

  await dbQuery(
    `DELETE FROM cart_items
     WHERE cart_id = $1`,
    [numericCartId]
  );

  return { id: numericCartId };
}

/** Перевірка купона за переданою сумою */
export async function verifyCouponForTotal(code, cartTotalRaw) {
  const codeNorm = String(code || "").trim().toLowerCase();
  const cartTotal = Number(cartTotalRaw) || 0;

  if (!codeNorm) return { valid: false, error: "Порожній код" };
  if (cartTotal <= 0) return { valid: false, error: "Кошик порожній" };

  const sql = `
    SELECT
      d.id, d.name, d.code, d.discount_type, d.value, d.min_order_sum,
      d.active,
      d.date_from AT TIME ZONE 'Europe/Kyiv' AS date_from,
      d.date_to   AT TIME ZONE 'Europe/Kyiv' AS date_to
    FROM discounts d
    WHERE d.active = TRUE
      AND d.code IS NOT NULL
      AND lower(d.code) = $1
    LIMIT 1
  `;
  const { rows } = await dbQuery(sql, [codeNorm]);
  const d = rows[0];
  if (!d) return { valid: false, error: "Купон не знайдено" };

  const now = new Date();

  if (d.date_from && now < new Date(d.date_from)) {
    return { valid: false, error: "Купон ще не активний" };
  }
  if (d.date_to && now > new Date(d.date_to)) {
    return { valid: false, error: "Строк дії купона минув" };
  }

  if (d.min_order_sum != null && cartTotal < Number(d.min_order_sum)) {
    return {
      valid: false,
      error: `Мінімальна сума замовлення: ${Number(d.min_order_sum).toFixed(2)} грн`,
    };
  }

  let amount = 0;
  if (d.discount_type === "percent") {
    const pct = Math.max(0, Math.min(100, Number(d.value || 0)));
    amount = +(cartTotal * pct / 100).toFixed(2);
  } else if (d.discount_type === "fixed") {
    amount = Math.min(cartTotal, Number(d.value || 0));
    amount = +amount.toFixed(2);
  } else {
    return { valid: false, error: "Невідомий тип знижки" };
  }

  if (amount <= 0) return { valid: false, error: "Купон не дає знижки" };

  return {
    valid: true,
    data: {
      id: d.id,
      code: d.code,
      name: d.name,
      discount_type: d.discount_type,
      value: Number(d.value),
      min_order_sum: d.min_order_sum != null ? Number(d.min_order_sum) : null,
      amount,
    },
  };
}

/** Перевірка купона за cartId (бекенд сам рахує суму) */
export async function verifyCouponForCartId(code, cartId) {
  const cid = Number(cartId);
  if (!Number.isFinite(cid) || cid <= 0) {
    return { valid: false, error: "Невірний ID кошика" };
  }

  const cart = await getCart(cid);
  if (!cart || (cart.total || 0) <= 0) {
    return { valid: false, error: "Кошик порожній" };
  }

  return await verifyCouponForTotal(code, cart.total);
}



export async function applyCouponToCart(cartId, couponCode) {
  const cid = Number(cartId);
  if (!Number.isFinite(cid) || cid <= 0) {
    throw new Error("Invalid cart id");
  }

  
  const cart = await getCart(cid);
  if (!cart || (cart.total || 0) <= 0) {
    throw new Error("Cart is empty");
  }

  const totalBefore = Number(cart.total) || 0;
  if (!Number.isFinite(totalBefore) || totalBefore <= 0) {
    throw new Error("Cart total must be positive");
  }

  const codeNorm = String(couponCode || "").trim();
  if (!codeNorm) {
    throw new Error("Empty coupon code");
  }

  
  const coupon = await verifyCouponForTotal(codeNorm, totalBefore);
  if (!coupon.valid) {
    throw new Error(coupon.error || "Invalid coupon");
  }

  const discount = Number(coupon.data?.amount || 0);
  if (!Number.isFinite(discount) || discount <= 0) {
    throw new Error("Coupon does not give discount");
  }

  
  const totalWithDiscount = Math.max(0, totalBefore - discount);

  return {
    ...cart,                           
    discount,                          
    total_with_discount: totalWithDiscount,
    coupon: {
      id: coupon.data.id,
      code: coupon.data.code,
      name: coupon.data.name,
      discount_type: coupon.data.discount_type,
      value: coupon.data.value,
      amount: discount,
      min_order_sum:
        coupon.data.min_order_sum != null
          ? Number(coupon.data.min_order_sum)
          : null,
    },
  };
}
