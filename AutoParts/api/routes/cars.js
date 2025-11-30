// API/routes/cars.js
import express from 'express';
import {
  getCarMakes,
  getCarModelsByMake,
  getCarGenerationsByModel,
  getCarModificationsByGeneration,
} from '../models/carsModel.js';

export const carsRouter = express.Router();

// Список марок
carsRouter.get('/makes', async (req, res) => {
  try {
    const makes = await getCarMakes();
    res.json(makes);
  } catch (err) {
    console.error('GET /api/cars/makes error:', err);
    res.status(500).json({ error: 'Failed to load car makes' });
  }
});

// /api/cars/models?make_id=1
carsRouter.get('/models', async (req, res) => {
  const { make_id } = req.query;
  if (!make_id) return res.status(400).json({ error: 'make_id is required' });

  try {
    const rows = await getCarModelsByMake(make_id);
    res.json(rows);
  } catch (err) {
    console.error('GET /api/cars/models error:', err);
    res.status(500).json({ error: 'Failed to load models' });
  }
});

// Покоління по model_id
carsRouter.get('/generations', async (req, res) => {
  const { model_id } = req.query;
  if (!model_id) return res.status(400).json({ error: 'model_id is required' });

  try {
    const rows = await getCarGenerationsByModel(model_id);
    res.json(rows);
  } catch (err) {
    console.error('GET /api/cars/generations error:', err);
    res.status(500).json({ error: 'Failed to load generations' });
  }
});

// Модифікації по generation_id
carsRouter.get('/modifications', async (req, res) => {
  const { generation_id } = req.query;
  if (!generation_id) return res.status(400).json({ error: 'generation_id is required' });

  try {
    const rows = await getCarModificationsByGeneration(generation_id);
    res.json(rows);
  } catch (err) {
    console.error('GET /api/cars/modifications error:', err);
    res.status(500).json({ error: 'Failed to load modifications' });
  }
});
