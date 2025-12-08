
import { dbQuery } from '../db/index.js';

/**
 * verifyCouponForTotal function.
 * @param {*} code
 * @param {*} cartTotalRaw
 * @returns {Promise<*>}
 */
export async function verifyCouponForTotal(code, cartTotalRaw) {
  const codeNorm = String(code || '').trim().toLowerCase();
  const cartTotal = Number(cartTotalRaw) || 0;

  if (!codeNorm) return { valid: false, error: 'Порожній код' };
  if (cartTotal <= 0) return { valid: false, error: 'Кошик порожній' };

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
  if (!d) return { valid: false, error: 'Купон не знайдено' };

  const now = new Date();

  if (d.date_from && now < new Date(d.date_from)) {
    return { valid: false, error: 'Купон ще не активний' };
  }
  if (d.date_to && now > new Date(d.date_to)) {
    return { valid: false, error: 'Строк дії купона минув' };
  }

  if (d.min_order_sum != null && cartTotal < Number(d.min_order_sum)) {
    return { valid: false, error: `Мінімальна сума замовлення: ${Number(d.min_order_sum).toFixed(2)} грн` };
  }

  let amount = 0;
  if (d.discount_type === 'percent') {
    const pct = Math.max(0, Math.min(100, Number(d.value || 0)));
    amount = +(cartTotal * pct / 100).toFixed(2);
  } else if (d.discount_type === 'fixed') {
    amount = Math.min(cartTotal, Number(d.value || 0));
    amount = +amount.toFixed(2);
  } else {
    return { valid: false, error: 'Невідомий тип знижки' };
  }

  if (amount <= 0) return { valid: false, error: 'Купон не дає знижки' };

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
    }
  };
}

