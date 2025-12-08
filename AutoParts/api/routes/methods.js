import express from 'express';
import { authenticate } from '../middlewares/auth.js';
import { listDeliveryMethods, listPaymentMethods } from '../models/methodModel.js';

export const methodsRouter = express.Router();

methodsRouter.use(authenticate);

// GET /api/delivery-methods
methodsRouter.get('/delivery-methods', async (req, res) => {
  try {
    const methods = await listDeliveryMethods();
    res.json({ success: true, data: methods });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: 'Failed to fetch delivery methods' });
  }
});

// GET /api/payment-methods
methodsRouter.get('/payment-methods', async (req, res) => {
  try {
    const methods = await listPaymentMethods();
    res.json({ success: true, data: methods });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: 'Failed to fetch payment methods' });
  }
});