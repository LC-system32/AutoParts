// API/models/methodModel.js
import { dbQuery } from '../db/index.js';

/**
 * Отримати список активних методів доставки.
 * Повертає масив об'єктів з полями id, name, code, description, base_price.
 */
export async function listDeliveryMethods() {
  const { rows } = await dbQuery(
    `SELECT id, name, code, description, base_price
     FROM delivery_methods
     WHERE active = TRUE
     ORDER BY id`
  );
  return rows;
}

/**
 * listPaymentMethods function.
 * @returns {Promise<*>}
 */
export async function listPaymentMethods() {
  const { rows } = await dbQuery(
    `SELECT id, name, code, description
     FROM payment_methods
     WHERE active = TRUE
     ORDER BY id`
  );
  return rows;
}

