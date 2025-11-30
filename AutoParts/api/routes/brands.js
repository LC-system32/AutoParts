import express from 'express';
import { getAllBrands, getBrandBySlug } from '../models/brandModel.js';

export const brandsRouter = express.Router();

// GET /api/brands
brandsRouter.get('/', async (req, res) => {
  try {
    const brands = await getAllBrands();
    res.json({ success: true, data: brands });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: 'Failed to fetch brands' });
  }
});

// GET /api/brands/:slug
brandsRouter.get('/:slug', async (req, res) => {
  const { slug } = req.params;
  try {
    const brand = await getBrandBySlug(slug);
    if (!brand) {
      return res.status(404).json({ success: false, error: 'Brand not found' });
    }
    res.json({ success: true, data: brand });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: 'Failed to fetch brand' });
  }
});