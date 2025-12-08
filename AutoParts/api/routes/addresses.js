import express from 'express';
import {
  getAddressesByUser,
  getAddressById,
  createAddress,
  updateAddress,
  deleteAddress,
} from '../models/addressModel.js';
import { requireAuth } from '../middlewares/auth.js';

/**
 * Addresses router
 *
 * Provides endpoints for managing user addresses. All routes require
 * authentication. Users can list, add, update and delete their own
 * addresses. Admins could extend this further if needed.
 */

export const addressesRouter = express.Router();

// All routes require an authenticated user
addressesRouter.use(requireAuth);

// GET /addresses — list addresses for current user
addressesRouter.get('/', async (req, res) => {
  try {
    const userId = req.user.id;
    const addresses = await getAddressesByUser(userId);
    res.json({ success: true, data: addresses });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: 'Failed to fetch addresses' });
  }
});

// GET /addresses/:id — get a single address belonging to current user
addressesRouter.get('/:id', requireAuth, async (req, res) => {
  try {
    const userId = req.user.id;
    const id = Number(req.params.id);

    if (!Number.isInteger(id) || id <= 0) {
      return res.status(400).json({
        success: false,
        error: 'Invalid address id',
      });
    }

    const address = await getAddressById(id, userId);

    if (!address) {
      return res.status(404).json({
        success: false,
        error: 'Address not found',
      });
    }
    console.log('sdfsf',address);
    
    res.json({
      success: true,
      data: address,
    });
  } catch (err) {
    console.error('GET /api/addresses/:id error', err);
    res.status(500).json({
      success: false,
      error: 'Failed to fetch address',
    });
  }
});
// POST /addresses — create new address for current user
addressesRouter.post('/', async (req, res) => {
  try {
    const userId = req.user.id;
    const address = await createAddress(userId, req.body || {});
    res.status(201).json({ success: true, data: address });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: 'Failed to create address' });
  }
});

// PUT /addresses/:id — update an address belonging to current user
addressesRouter.put('/:id', async (req, res) => {
  try {
    const userId = req.user.id;
    const id = Number(req.params.id);
    const updated = await updateAddress(id, userId, req.body || {});
    if (!updated) {
      return res.status(404).json({ success: false, error: 'Address not found' });
    }
    res.json({ success: true, data: updated });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: 'Failed to update address' });
  }
});

// DELETE /addresses/:id — delete an address belonging to current user
addressesRouter.delete('/:id', async (req, res) => {
  try {
    const userId = req.user.id;
    const id = Number(req.params.id);
    const removed = await deleteAddress(id, userId);
    if (!removed) {
      return res.status(404).json({ success: false, error: 'Address not found' });
    }
    res.json({ success: true });
} catch (err) {
  console.error('DELETE /api/addresses/:id error', err);

  if (err.code === 'ADDRESS_IN_USE') {
    return res.status(400).json({
      success: false,
      error: 'Цю адресу не можна видалити, бо вона вже використовується в оформлених замовленнях.',
    });
  }

  if (err.code === '23503') {
    return res.status(400).json({
      success: false,
      error: 'Адреса привʼязана до замовлень і не може бути видалена.',
    });
  }

  return res.status(500).json({
    success: false,
    error: 'Внутрішня помилка сервера при видаленні адреси.',
  });
}

});