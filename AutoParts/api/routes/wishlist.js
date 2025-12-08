import express from 'express';
import { authenticate, requireAuth } from '../middlewares/auth.js';
import { getWishlist, addToWishlist, removeFromWishlist } from '../models/wishlistModel.js';

export const wishlistRouter = express.Router();


wishlistRouter.use(authenticate);


wishlistRouter.get('/', requireAuth, async (req, res) => {
  try {
    const items = await getWishlist(req.user.id);
    res.json({ success: true, data: items });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: 'Failed to fetch wishlist' });
  }
});


wishlistRouter.post('/items', requireAuth, async (req, res) => {
  const { product_id } = req.body;
  if (!product_id) {
    return res.status(400).json({ success: false, error: 'Missing product_id' });
  }
  try {
    const item = await addToWishlist(req.user.id, parseInt(product_id, 10));
    res.status(201).json({ success: true, data: item });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: 'Failed to add to wishlist' });
  }
});


wishlistRouter.delete('/items/:productId', requireAuth, async (req, res) => {
  const productId = parseInt(req.params.productId, 10);
  try {
    await removeFromWishlist(req.user.id, productId);
    res.json({ success: true, data: { product_id: productId } });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: 'Failed to remove from wishlist' });
  }
});