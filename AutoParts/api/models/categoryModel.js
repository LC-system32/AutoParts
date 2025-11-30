import { dbQuery } from '../db/index.js';

// Get all categories

/**
 * getAllCategories function.
 * @returns {Promise<*>}
 */
export async function getAllCategories() {
  const { rows } = await dbQuery(
    `SELECT id, name, slug, parent_id FROM categories ORDER BY name`
  );
  return rows;
}

/**
 * getCategoryBySlug function.
 * @param {*} slug
 * @returns {Promise<*>}
 */
export async function getCategoryBySlug(slug) {
  const { rows } = await dbQuery(
    `SELECT id, name, slug, parent_id FROM categories WHERE slug = $1 LIMIT 1`,
    [slug]
  );
  return rows[0] || null;
}

