import pkg from 'pg';
import dotenv from 'dotenv';

dotenv.config();

const { Pool } = pkg;

// Create a connection pool using environment variables
export const pool = new Pool({
  host: process.env.PGHOST,
  user: process.env.PGUSER,
  password: process.env.PGPASSWORD,
  database: process.env.PGDATABASE,
  port: process.env.PGPORT ? parseInt(process.env.PGPORT, 10) : undefined,
});

/**
 * Execute a parameterized query
 *
 * @param {string} text - The SQL statement with placeholders ($1, $2, ...)
 * @param {Array<any>} params - An array of parameters
 * @returns {Promise<{ rows: any[], rowCount: number }>}
 */
export async function dbQuery(text, params = []) {
  const client = await pool.connect();
  try {
    const res = await client.query(text, params);
    return { rows: res.rows, rowCount: res.rowCount };
  } catch (err) {
    console.error('Database query error', err);
    throw err;
  } finally {
    client.release();
  }
}