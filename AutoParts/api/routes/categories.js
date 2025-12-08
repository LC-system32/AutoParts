import express from 'express';
import { getAllCategories, getCategoryBySlug } from '../models/categoryModel.js';
import { dbQuery } from '../db/index.js';

export const categoriesRouter = express.Router();

// GET /api/categories
categoriesRouter.get('/', async (req, res) => {
  try {
    const categories = await getAllCategories();
    res.json({ success: true, data: categories });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: 'Failed to fetch categories' });
  }
});

categoriesRouter.get('/:slug/children', async (req, res) => {
  const { slug } = req.params;
  const search = req.query.search || req.query.q;

  try {
    const params = [slug];
    let sql = `
      SELECT
        c.id,
        c.name,
        c.slug,
        c.parent_id,
        c.description,
        c.sort_order,
        c.is_active
      FROM categories c
      WHERE c.is_active = TRUE
        AND c.parent_id = (
          SELECT id FROM categories WHERE slug = $1
        )
    `;

    if (search) {
      params.push(`%${search}%`);
      sql += ` AND c.name ILIKE $2`;
    }

    sql += ` ORDER BY c.sort_order, c.name`;

    const { rows } = await dbQuery(sql, params);

    res.json({ success: true, data: rows });
  } catch (err) {
    console.error('Failed to fetch child categories', err);
    res.status(500).json({ success: false, error: 'Failed to fetch child categories' });
  }
});

// GET /api/categories/:slug
categoriesRouter.get('/:slug', async (req, res) => {
  const { slug } = req.params;
  try {
    const category = await getCategoryBySlug(slug);
    if (!category) {
      return res.status(404).json({ success: false, error: 'Category not found' });
    }
    res.json({ success: true, data: category });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: 'Failed to fetch category' });
  }
});
