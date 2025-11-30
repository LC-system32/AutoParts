// API/models/carsModel.js
import { dbQuery } from '../db/index.js';

export async function getCarMakes() {
  const { rows } = await dbQuery(
    `SELECT id, name, slug
     FROM car_makes
     ORDER BY name ASC`
  );
  return rows;
}

export async function getCarModelsByMake(makeId) {
  const { rows } = await dbQuery(
    `SELECT id, name, slug, make_id
     FROM car_models
     WHERE make_id = $1
     ORDER BY name ASC`,
    [makeId]
  );
  return rows;
}

export async function getCarGenerationsByModel(modelId) {
  const { rows } = await dbQuery(
    `SELECT id, name, year_from, year_to, model_id
     FROM car_generations
     WHERE model_id = $1
     ORDER BY year_from NULLS FIRST, name ASC`,
    [modelId]
  );
  return rows;
}

export async function getCarModificationsByGeneration(genId) {
  const { rows } = await dbQuery(
    `SELECT id, generation_id, engine_code, engine_volume,
            power_hp, fuel_type, drive_type, transmission,
            year_from, year_to
     FROM car_modifications
     WHERE generation_id = $1
     ORDER BY year_from NULLS FIRST, power_hp DESC`,
    [genId]
  );
  return rows;
}
