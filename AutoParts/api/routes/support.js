
import express from 'express';
import { authenticate, requireAuth } from '../middlewares/auth.js';
import {
  listTickets,
  createTicket,
  getTicketWithMessages,
  addUserMessage,
} from '../models/supportModel.js';

export const supportRouter = express.Router();
supportRouter.use(authenticate);

// GET /api/support 
supportRouter.get('/', requireAuth, async (req, res) => {
  try {
    const tickets = await listTickets(req.user.id);
    res.json({ success: true, data: tickets });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: 'Failed to fetch tickets' });
  }
});

// POST /api/support 
supportRouter.post('/', requireAuth, async (req, res) => {
  const { subject, message } = req.body || {};
  if (!subject || !message) {
    return res.status(400).json({ success: false, error: 'Missing subject or message' });
  }
  try {
    const ticket = await createTicket(req.user.id, subject, message);
    res.status(201).json({ success: true, data: ticket });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: 'Failed to create ticket' });
  }
});

// GET /api/support/:id 
supportRouter.get('/:id', requireAuth, async (req, res) => {
  try {
    const id = Number(req.params.id);
    const data = await getTicketWithMessages(req.user.id, id);
    if (!data) return res.status(404).json({ success: false, error: 'Not found' });
    res.json({ success: true, data });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: 'Failed to fetch ticket' });
  }
});

// POST /api/support/:id/messages 
supportRouter.post('/:id/messages', requireAuth, async (req, res) => {
  try {
    const id = Number(req.params.id);
    const { message } = req.body || {};
    if (!message || !message.trim()) {
      return res.status(400).json({ success: false, error: 'Message is required' });
    }
    const result = await addUserMessage(req.user.id, id, message.trim());
    if (!result) return res.status(404).json({ success: false, error: 'Not found' });
    res.status(201).json({ success: true, data: result });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: 'Failed to add message' });
  }
});
