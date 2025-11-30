import { dbQuery } from '../db/index.js';

/**
 * Address model
 *
 * Provides CRUD operations for the addresses table. Each address belongs to
 * a user via the user_id foreign key. These helpers are used by the
 * addresses API routes to list, create, update and delete addresses.
 */

/**
 * Get all addresses for a given user.
 *
 * @param {number} userId
 * @returns {Promise<array>}
 */

/**
 * createAddress function.
 * @param {*} userId
 * @param {*} data
 * @returns {Promise<*>}
 */
export async function createAddress(userId, data) {
    const {
        full_name = null,
        phone = null,
        country = null,
        region = null,
        city = null,
        postal_code = null,
        street_address = null,
        comment = null,
    } = data;

    const { rows } = await dbQuery(
        `INSERT INTO addresses
      (user_id, full_name, phone, country, region, city, postal_code, street_address, comment)
     VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9)
     RETURNING id, user_id, full_name, phone, country, region, city, postal_code, street_address, comment`,
        [
            userId,
            full_name,
            phone,
            country,
            region,
            city,
            postal_code,
            street_address,
            comment,
        ]
    );
    return rows[0];
}

/**
 * deleteAddress function.
 * @param {*} id
 * @param {*} userId
 * @returns {Promise<*>}
 */
export async function deleteAddress(id, userId) {
    // 1) Перевіряємо, чи є ХОЧ ОДНЕ НЕзавершене замовлення з цією адресою
    //    В цій логіці вважаємо, що незавершені = все, що НЕ 'completed' і НЕ 'cancelled'
    //    (за потреби можеш підлаштувати список статусів)
    const { rows: activeOrders } = await dbQuery(
        `
    SELECT o.id
    FROM orders o
    WHERE (o.shipping_address_id = $1 OR o.billing_address_id = $1)
      AND o.status_code NOT IN ('completed', 'cancelled')
    LIMIT 1
    `,
        [id]
    );

    if (activeOrders[0]) {
        // Є хоча б одне активне замовлення – адресу видаляти не можна
        const err = new Error('ADDRESS_IN_ACTIVE_ORDERS');
        err.code = 'ADDRESS_IN_ACTIVE_ORDERS';
        throw err;
    }

    // 2) Відчіпляємо адресу від УСІХ завершених замовлень (щоб не заважав FK)
    await dbQuery(
        `
    UPDATE orders
    SET shipping_address_id = NULL
    WHERE shipping_address_id = $1
    `,
        [id]
    );

    await dbQuery(
        `
    UPDATE orders
    SET billing_address_id = NULL
    WHERE billing_address_id = $1
    `,
        [id]
    );

    // 3) Видаляємо адресу користувача
    const { rowCount } = await dbQuery(
        `DELETE FROM addresses WHERE id = $1 AND user_id = $2`,
        [id, userId]
    );

    return rowCount > 0;
}



/**
 * getAddressById function.
 * @param {*} id
 * @param {*} userId
 * @returns {Promise<*>}
 */
export async function getAddressById(id, userId) {
    const sql = `
    SELECT
      id,
      user_id,
      full_name,
      phone,
      country,
      region,
      city,
      postal_code,
      street_address,
      comment
    FROM addresses
    WHERE id = $1
      AND user_id = $2
    LIMIT 1
  `;
    const { rows } = await dbQuery(sql, [id, userId]);
    return rows[0] || null;
}

/**
 * getAddressesByUser function.
 * @param {*} userId
 * @returns {Promise<*>}
 */
export async function getAddressesByUser(userId) {
    const sql = `
    SELECT
      id,
      user_id,
      full_name,
      phone,
      country,
      region,
      city,
      postal_code,
      street_address,
      comment
    FROM addresses
    WHERE user_id = $1
    ORDER BY id
  `;
    const { rows } = await dbQuery(sql, [userId]);
    return rows;
}

/**
 * updateAddress function.
 * @param {*} id
 * @param {*} userId
 * @param {*} data
 * @returns {Promise<*>}
 */
export async function updateAddress(id, userId, data) {
    const fields = [];
    const params = [];
    let idx = 1;
    if (data.full_name !== undefined) {
        fields.push(`full_name = $${idx++}`);
        params.push(data.full_name);
    }
    if (data.phone !== undefined) {
        fields.push(`phone = $${idx++}`);
        params.push(data.phone);
    }
    if (data.country !== undefined) {
        fields.push(`country = $${idx++}`);
        params.push(data.country);
    }
    if (data.region !== undefined) {
        fields.push(`region = $${idx++}`);
        params.push(data.region);
    }
    if (data.city !== undefined) {
        fields.push(`city = $${idx++}`);
        params.push(data.city);
    }
    if (data.postal_code !== undefined) {
        fields.push(`postal_code = $${idx++}`);
        params.push(data.postal_code);
    }
    if (data.street_address !== undefined) {
        fields.push(`street_address = $${idx++}`);
        params.push(data.street_address);
    }
    if (data.comment !== undefined) {
        fields.push(`comment = $${idx++}`);
        params.push(data.comment);
    }

    if (fields.length === 0) {
        // nothing to update
        const { rows } = await dbQuery(
            `SELECT id, user_id, full_name, phone, country, region, city, postal_code, street_address, comment
       FROM addresses WHERE id = $1 AND user_id = $2`,
            [id, userId]
        );
        return rows[0] || null;
    }

    // Build dynamic SQL
    const sql = `UPDATE addresses SET ${fields.join(', ')} WHERE id = $${idx} AND user_id = $${idx + 1}
    RETURNING id, user_id, full_name, phone, country, region, city, postal_code, street_address, comment`;
    params.push(id, userId);

    const { rows } = await dbQuery(sql, params);
    return rows[0] || null;
}

