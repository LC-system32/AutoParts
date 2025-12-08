import express from 'express';
import { authenticate, requireAuth } from '../middlewares/auth.js';
import {
  createOrder,
  listOrders,
  getOrders,
  getOrderById,
} from '../models/orderModel.js';

export const ordersRouter = express.Router();

ordersRouter.use(authenticate);

//  1) POST /api/orders 
ordersRouter.post('/', async (req, res) => {
  const { cart_id, delivery_method_id, payment_method_id, address, notes } = req.body;
  if (!cart_id) {
    return res.status(400).json({ success: false, error: 'Missing cart_id' });
  }

  const userId = req.user ? req.user.id : req.body.user_id;

  try {
    const order = await createOrder({
      cart_id: parseInt(cart_id, 10),
      user_id: userId || null,
      delivery_method_id: delivery_method_id ? parseInt(delivery_method_id, 10) : null,
      payment_method_id: payment_method_id ? parseInt(payment_method_id, 10) : null,
      address,
      notes,
      full_name: req.body.full_name,
      phone: req.body.phone,
    });
    res.status(201).json({ success: true, data: order });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: err.message || 'Failed to create order' });
  }
});

//  2) GET /api/orders
ordersRouter.get('/', requireAuth, async (req, res) => {
  const userId = req.user.id;
  try {
    const orders = await listOrders(userId);
    res.json({ success: true, data: orders });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: 'Failed to fetch orders' });
  }
});

//  3) GET /api/admin/orders
ordersRouter.get('/', async (req, res) => {
  try {
    const orders = await getOrders({
      user_id: req.query.user_id,
      email: req.query.email,
      phone: req.query.phone,
    });
    res.json({ success: true, data: orders });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: 'Failed to load orders' });
  }
});

//  4) GET /api/admin/orders/:id
ordersRouter.get('/:id', async (req, res) => {
  const id = parseInt(req.params.id, 10);
  if (Number.isNaN(id)) {
    return res.status(400).json({ success: false, error: 'Invalid order id' });
  }

  try {
    const order = await getOrderById(id);
    if (!order) {
      return res.status(404).json({ success: false, error: 'Order not found' });
    }
    res.json({ success: true, data: order });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: 'Failed to load order' });
  }
});
