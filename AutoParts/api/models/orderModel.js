// API/models/orderModel.js
import { dbQuery, pool } from '../db/index.js';

/**
 * Створити нове замовлення з кошика
 * payload:
 * {
 *   cart_id,
 *   user_id,
 *   delivery_method_id,
 *   payment_method_id,
 *   address,        // текстова адреса з форми
 *   notes,          // примітки до замовлення
 *   full_name,      // ПІБ отримувача (опційно)
 *   phone           // телефон (опційно)
 * }
 *
 * @returns {Promise<{ id: number, order_number: string }>}
 */
export async function createOrder(payload) {
  const {
    cart_id,
    user_id,
    delivery_method_id,
    payment_method_id,
    address,
    notes,
    full_name,
    phone,
  } = payload;

  const client = await pool.connect();

  try {
    await client.query('BEGIN');

    // 1) Витягуємо товари з кошика + дані продуктів
    const { rows: cartItems } = await client.query(
      `
      SELECT
        ci.product_id,
        ci.quantity,
        ci.price,
        ci.currency,
        p.name  AS product_name,
        p.sku   AS sku
      FROM cart_items ci
      JOIN products p ON p.id = ci.product_id
      WHERE ci.cart_id = $1
      `,
      [cart_id]
    );

    if (cartItems.length === 0) {
      throw new Error('Cart is empty');
    }

    // 2) Рахуємо суму по товарах
    let totalProducts = 0;
    for (const item of cartItems) {
      totalProducts += Number(item.price) * Number(item.quantity);
    }

    const total_discount = 0;          // поки без знижок
    const total_delivery = 0;          // доставка рахується окремо
    const total_amount = totalProducts - total_discount + total_delivery;
    const currency = cartItems[0]?.currency || 'UAH';

    // 3) Якщо є адреса / ПІБ / телефон – створюємо запис у addresses
    let shippingAddressId = null;

    if (address || full_name || phone) {
      const { rows: addrRows } = await client.query(
        `
        INSERT INTO addresses (
          user_id,
          full_name,
          phone,
          street_address
        )
        VALUES ($1, $2, $3, $4)
        RETURNING id
        `,
        [
          user_id || null,
          full_name || null,
          phone || null,
          address || null,
        ]
      );
      shippingAddressId = addrRows[0].id;
    }

    // 4) Генеруємо номер замовлення (щось унікальне типу AP-20250201-...)
    const orderNumber = `AP-${new Date()
      .toISOString()
      .slice(0, 10)
      .replace(/-/g, '')}-${Math.floor(Math.random() * 100000)}`;

    const statusCode = 'pending';

    // 5) Створюємо замовлення
    const { rows: orderRows } = await client.query(
      `
      INSERT INTO orders (
        order_number,
        user_id,
        status_code,
        delivery_method_id,
        payment_method_id,
        shipping_address_id,
        billing_address_id,
        total_products,
        total_discount,
        total_delivery,
        total_amount,
        currency,
        customer_comment
      )
      VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13)
      RETURNING id, order_number
      `,
      [
        orderNumber,
        user_id || null,
        statusCode,
        delivery_method_id || null,
        payment_method_id || null,
        shippingAddressId,
        shippingAddressId, // поки білінг = шипінг
        totalProducts,
        total_discount,
        total_delivery,
        total_amount,
        currency,
        notes || null,
      ]
    );

    const orderId = orderRows[0].id;

    // 6) Позиції замовлення
    for (const item of cartItems) {
      const lineTotal = Number(item.price) * Number(item.quantity);

      await client.query(
        `
        INSERT INTO order_items (
          order_id,
          product_id,
          product_name,
          sku,
          quantity,
          price,
          discount,
          total,
          currency
        )
        VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9)
        `,
        [
          orderId,
          item.product_id,
          item.product_name,
          item.sku,
          item.quantity,
          item.price,
          0,              // поки без знижок по рядку
          lineTotal,
          item.currency || currency,
        ]
      );
    }

    // 7) Очищаємо кошик (видаляємо товари, просто оновлюємо updated_at)
    await client.query(
      `UPDATE carts SET updated_at = NOW() WHERE id = $1`,
      [cart_id]
    );

    await client.query(
      `DELETE FROM cart_items WHERE cart_id = $1`,
      [cart_id]
    );

    await client.query('COMMIT');

    return { id: orderId, order_number: orderNumber };
  } catch (err) {
    await client.query('ROLLBACK');
    throw err;
  } finally {
    client.release();
  }
}

/**
 * getOrderById function.
 * @param {*} id
 * @returns {Promise<*>}
 */
export async function getOrderById(id) {
  const sql = `
    SELECT
      o.id,
      o.order_number,
      o.status_code,
      o.total_amount,
      o.currency,
      o.created_at,
      o.customer_email,
      o.customer_phone
    FROM orders o
    WHERE o.id = $1
    LIMIT 1
  `;

  const { rows } = await dbQuery(sql, [id]);
  return rows[0] || null;
}

/**
 * getOrders function.
 * @param {*} filters
 * @returns {Promise<*>}
 */
export async function getOrders(filters = {}) {
  const { user_id, email, phone } = filters;

  const where = [];
  const params = [];
  let idx = 1;

  if (user_id) {
    where.push(`o.user_id = $${idx}`);
    params.push(user_id);
    idx++;
  }

  if (email) {
    where.push(`LOWER(o.customer_email) = LOWER($${idx})`);
    params.push(email);
    idx++;
  }

  if (phone) {
    where.push(`o.customer_phone = $${idx}`);
    params.push(phone);
    idx++;
  }

  const whereSql = where.length ? `WHERE ${where.join(' AND ')}` : '';

  const sql = `
    SELECT
      o.id, o.order_number, o.status_code, o.total_amount, o.currency, o.created_at
    FROM orders o
    ${whereSql}
    ORDER BY o.created_at DESC
    LIMIT 50
  `;

  const { rows } = await dbQuery(sql, params);
  return rows;
}

/**
 * listOrders function.
 * @param {*} userId
 * @returns {Promise<*>}
 */
export async function listOrders(userId) {
  const { rows } = await dbQuery(
    `
    SELECT
      id,
      order_number,
      status_code AS status,
      total_amount AS total,
      created_at
    FROM orders
    WHERE user_id = $1
    ORDER BY created_at DESC
    `,
    [userId]
  );

  return rows;
}

