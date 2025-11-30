// api/models/supportModel.js
import { dbQuery } from '../db/index.js';

/**
 * addUserMessage function.
 * @param {*} userId
 * @param {*} ticketId
 * @param {*} body
 * @returns {Promise<*>}
 */
export async function addUserMessage(userId, ticketId, body) {
  // Перевіряємо, що тікет належить юзеру
  const { rows: own } = await dbQuery(
    `SELECT 1 FROM support_tickets WHERE id = $1 AND user_id = $2`,
    [ticketId, userId]
  );
  if (!own[0]) return null;

  const ins = await dbQuery(
    `INSERT INTO support_messages (ticket_id, author_id, is_staff, body, created_at)
     VALUES ($1, $2, FALSE, $3, NOW())
     RETURNING id`,
    [ticketId, userId, body]
  );

  await dbQuery(
    `UPDATE support_tickets SET updated_at = NOW() WHERE id = $1`,
    [ticketId]
  );

  return ins.rows[0];
}

/**
 * createTicket function.
 * @param {*} userId
 * @param {*} subject
 * @param {*} message
 * @returns {Promise<*>}
 */
export async function createTicket(userId, subject, message) {
  const { rows } = await dbQuery(
    `INSERT INTO support_tickets (user_id, subject, message, status, created_at)
     VALUES ($1, $2, $3, 'open', NOW())
     RETURNING id`,
    [userId, subject, message]
  );
  return rows[0];
}

/** Один тікет + усі повідомлення (для ВЛАСНИКА) */
export async function getTicketWithMessages(userId, ticketId) {
  const tRes = await dbQuery(
    `SELECT id, user_id, subject, message, status, created_at, updated_at
     FROM support_tickets
     WHERE id = $1 AND user_id = $2`,
    [ticketId, userId]
  );
  const ticket = tRes.rows[0];
  if (!ticket) return null;

  const mRes = await dbQuery(
    `SELECT id, author_id, is_staff, body, created_at
     FROM support_messages
     WHERE ticket_id = $1
     ORDER BY created_at ASC`,
    [ticketId]
  );

  // додаємо перше повідомлення з самого тікета (від користувача)
  const messages = [
    {
      id: 0,
      author_id: ticket.user_id,
      is_staff: false,
      body: ticket.message,
      created_at: ticket.created_at,
    },
    ...mRes.rows,
  ];

  return { ...ticket, messages };
}

/**
 * listTickets function.
 * @param {*} userId
 * @returns {Promise<*>}
 */
export async function listTickets(userId) {
  const { rows } = await dbQuery(
    `SELECT id, subject, message, status, created_at
     FROM support_tickets
     WHERE user_id = $1
     ORDER BY created_at DESC`,
    [userId]
  );
  return rows;
}

