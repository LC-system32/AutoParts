// file: API/models/brandModel.js
import { dbQuery } from '../db/index.js';

/**
 * Отримати всі активні бренди
 */
export async function getAllBrands() {
  const sql = `
    SELECT
      id,
      name,
      slug,
      logo,
      is_active
    FROM brands
    WHERE is_active = TRUE
    ORDER BY name
  `;

  const { rows } = await dbQuery(sql);
  return rows;
}

/**
 * getBrandBySlug function.
 * @param {*} slug
 * @returns {Promise<*>}
 */
export async function getBrandBySlug(slug) {
  const sql = `
    SELECT
      id,
      name,
      slug,
      logo,
      is_active
    FROM brands
    WHERE slug = $1
    LIMIT 1
  `;

  const { rows } = await dbQuery(sql, [slug]);
  return rows[0] || null;
}

/**
 * getBrandsWithProductCount function.
 * @returns {Promise<*>}
 */
export async function getBrandsWithProductCount() {
  const sql = `
    SELECT
      b.id,
      b.name,
      b.slug,
      b.logo,
      b.is_active,
      COUNT(p.id) AS products_count
    FROM brands b
    LEFT JOIN products p ON p.brand_id = b.id
    WHERE b.is_active = TRUE
    GROUP BY b.id, b.name, b.slug, b.logo, b.is_active
    ORDER BY b.name
  `;

  const { rows } = await dbQuery(sql);
  return rows;
}

