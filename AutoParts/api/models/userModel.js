// file: API/models/userModel.js
import { dbQuery, pool } from '../db/index.js';
import bcrypt from 'bcrypt';
import { v4 as uuidv4 } from 'uuid';

/**
 * createSession function.
 * @param {*} userId
 * @returns {Promise<*>}
 */
export async function createSession(userId) {
    const token = uuidv4();
    const expiresAt = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000); // 7 днів

    await dbQuery(
        `
      INSERT INTO user_sessions (user_id, token, created_at, expires_at)
      VALUES ($1, $2, NOW(), $3)
    `,
        [userId, token, expiresAt]
    );

    return token;
}

/**
 * Створити нового користувача і призначити роль "customer"
 *
 * ⚠️ Тут використовується поле users.name.
 * Якщо в твоїй таблиці його немає — або додай поле,
 * або перероби під login/first_name/last_name.
 */
export async function createUser({
    login,
    email,
    password,
    firstName = null,
    lastName = null,
    phone = null,
}) {
    if (!email || !login || !password) {
        throw new Error('email, login і password — обовʼязкові');
    }

    const passwordHash = await bcrypt.hash(password, 10);

    const insertSql = `
  INSERT INTO users (
    email,
    login,
    password_hash,
    first_name,
    last_name,
    phone
  )
  VALUES ($1, $2, $3, $4, $5, $6)
  RETURNING
    id,
    email,
    login,
    first_name,
    last_name,
    phone,
    is_active,
    created_at,
    updated_at
`;


    const insertParams = [
        email,
        login,
        passwordHash,
        firstName,
        lastName,
        phone,
    ];

    const { rows: userRows } = await dbQuery(insertSql, insertParams);
    const user = userRows[0];

    // Призначаємо дефолтну роль "customer"
    const { rows: roleRows } = await dbQuery(
        `SELECT id FROM roles WHERE code = 'customer' LIMIT 1`
    );
    const roleId = roleRows[0]?.id;

    if (roleId) {
        await dbQuery(
            `INSERT INTO user_roles (user_id, role_id) VALUES ($1, $2)`,
            [user.id, roleId]
        );
    }

    return user;
}

/**
 * deleteUserSessions function.
 * @param {*} userId
 * @returns {Promise<*>}
 */
export async function deleteUserSessions(userId) {
    await dbQuery(
        `
    DELETE FROM user_sessions
    WHERE user_id = $1
    `,
        [userId]
    );
    return true;
}

/**
 * findUserByEmail function.
 * @param {*} email
 * @returns {Promise<*>}
 */
export async function findUserByEmail(email) {
    const sql = `
    SELECT
      u.id,
      u.login,
      u.email,
      u.password_hash,
      u.first_name,
      u.last_name,
      u.phone,
      COALESCE(
        NULLIF(TRIM(u.first_name || ' ' || u.last_name), ''),
        u.login
      ) AS full_name,
      COALESCE(array_remove(array_agg(r.code), NULL), '{}') AS roles
    FROM users u
    LEFT JOIN user_roles ur ON ur.user_id = u.id
    LEFT JOIN roles r       ON r.id = ur.role_id
    WHERE u.email = $1
    GROUP BY
      u.id,
      u.login,
      u.email,
      u.password_hash,
      u.first_name,
      u.last_name,
      u.phone
    LIMIT 1;
  `;

    const { rows } = await dbQuery(sql, [email]);
    return rows[0] || null;
}

/**
 * getAllUsersWithRoles function.
 * @returns {Promise<*>}
 */
export async function getAllUsersWithRoles() {
    const { rows } = await dbQuery(
        `
    SELECT
      u.id,
      u.email,
      u.login,
      u.first_name,
      u.last_name,
      u.phone,
      u.is_active,
      u.created_at,
      u.updated_at,
      COALESCE(
        ARRAY_AGG(r.code ORDER BY r.code)
          FILTER (WHERE r.code IS NOT NULL),
        '{}'
      ) AS roles
    FROM users u
    LEFT JOIN user_roles ur ON ur.user_id = u.id
    LEFT JOIN roles r ON r.id = ur.role_id
    GROUP BY
      u.id,
      u.email,
      u.login,
      u.first_name,
      u.last_name,
      u.phone,
      u.is_active,
      u.created_at,
      u.updated_at
    ORDER BY u.id DESC;
    `
    );

    return rows;
}

/**
 * getUserById function.
 * @param {*} userId
 * @returns {Promise<*>}
 */
export async function getUserById(userId) {
    const sql = `
    SELECT
      u.id,
      u.email,
      u.login,
      u.first_name,
      u.last_name,
      u.is_active,

      COALESCE(addr.phone, u.phone) AS phone,
      array_remove(array_agg(r.code), NULL) AS roles,

      TRIM(BOTH ', ' FROM concat_ws(
        ', ',
        NULLIF(TRIM(addr.city), ''),
        NULLIF(TRIM(addr.street_address), ''),
        NULLIF(TRIM(addr.postal_code), ''),
        NULLIF(TRIM(addr.region), ''),
        NULLIF(TRIM(addr.country), '')
      )) AS address,

      addr.id             AS address_id,
      addr.full_name      AS address_full_name,
      addr.phone          AS address_phone,
      addr.country        AS address_country,
      addr.region         AS address_region,
      addr.city           AS address_city,
      addr.postal_code    AS address_postal_code,
      addr.street_address AS address_street_address,
      addr.comment        AS address_comment

    FROM users u
    LEFT JOIN user_roles ur ON ur.user_id = u.id
    LEFT JOIN roles r       ON r.id = ur.role_id

    LEFT JOIN LATERAL (
      SELECT a.*
      FROM addresses a
      WHERE a.user_id = u.id
      ORDER BY a.id
      LIMIT 1
    ) addr ON TRUE

    WHERE u.id = $1

    GROUP BY
      u.id,
      u.email,
      u.login,
      u.first_name,
      u.last_name,
      u.phone,
      u.is_active,
      addr.id,
      addr.full_name,
      addr.phone,
      addr.country,
      addr.region,
      addr.city,
      addr.postal_code,
      addr.street_address,
      addr.comment
  `;

    const { rows } = await dbQuery(sql, [userId]);
    return rows[0] || null;
}

/**
 * getUserByToken function.
 * @param {*} token
 * @returns {Promise<*>}
 */
export async function getUserByToken(token) {
    const sql = `
    SELECT
      u.id,
      u.login,
      u.email,
      u.first_name,
      u.last_name,

      -- телефон для профілю: спочатку з адреси, якщо є, інакше з users.phone
      COALESCE(addr.phone, u.phone) AS phone,

      COALESCE(
        NULLIF(TRIM(u.first_name || ' ' || u.last_name), ''),
        u.login
      ) AS full_name,

      COALESCE(array_remove(array_agg(r.code), NULL), '{}') AS roles,

      -- зібрана в один рядок адреса
      TRIM(BOTH ', ' FROM concat_ws(
        ', ',
        NULLIF(TRIM(addr.city), ''),
        NULLIF(TRIM(addr.street_address), ''),
        NULLIF(TRIM(addr.postal_code), ''),
        NULLIF(TRIM(addr.region), ''),
        NULLIF(TRIM(addr.country), '')
      )) AS address,

      -- "сирі" поля адреси (на всякий випадок, якщо десь ще знадобляться)
      addr.id             AS address_id,
      addr.full_name      AS address_full_name,
      addr.phone          AS address_phone,
      addr.country        AS address_country,
      addr.region         AS address_region,
      addr.city           AS address_city,
      addr.postal_code    AS address_postal_code,
      addr.street_address AS address_street_address,
      addr.comment        AS address_comment

    FROM user_sessions us
    JOIN users u            ON u.id = us.user_id
    LEFT JOIN user_roles ur ON ur.user_id = u.id
    LEFT JOIN roles r       ON r.id = ur.role_id

    -- одна (перша) адреса користувача
    LEFT JOIN LATERAL (
      SELECT a.*
      FROM addresses a
      WHERE a.user_id = u.id
      ORDER BY a.id
      LIMIT 1
    ) addr ON TRUE

    WHERE us.token = $1
      AND (us.expires_at IS NULL OR us.expires_at > NOW())

    GROUP BY
      u.id,
      u.login,
      u.email,
      u.first_name,
      u.last_name,
      u.phone,
      addr.id,
      addr.full_name,
      addr.phone,
      addr.country,
      addr.region,
      addr.city,
      addr.postal_code,
      addr.street_address,
      addr.comment
  `;

    const { rows } = await dbQuery(sql, [token]);
    return rows[0] || null;
}

/**
 * getUserSessions function.
 * @param {*} userId
 * @returns {Promise<*>}
 */
export async function getUserSessions(userId) {
    const { rows } = await dbQuery(
        `
    SELECT
      id,
      ip_address,
      user_agent,
      expires_at,
      created_at
    FROM user_sessions
    WHERE user_id = $1
    ORDER BY created_at DESC
    `,
        [userId]
    );

    return rows;
}

/**
 * updateUser function.
 * @param {*} userId
 * @param {*} data
 * @returns {Promise<*>}
 */
export async function updateUser(userId, data) {
    const fields = [];
    const params = [];
    let idx = 1;

    if (data.email !== undefined) {
        fields.push(`email = $${idx++}`);
        params.push(data.email);
    }
    if (data.login !== undefined) {
        fields.push(`login = $${idx++}`);
        params.push(data.login);
    }
    if (data.first_name !== undefined) {
        fields.push(`first_name = $${idx++}`);
        params.push(data.first_name);
    }
    if (data.last_name !== undefined) {
        fields.push(`last_name = $${idx++}`);
        params.push(data.last_name);
    }
    if (data.phone !== undefined) {
        fields.push(`phone = $${idx++}`);
        params.push(data.phone);
    }
    if (data.is_active !== undefined) {
        fields.push(`is_active = $${idx++}`);
        params.push(data.is_active);
    }

    if (fields.length === 0) {
        // нічого не змінювали — повертаємо поточний профіль
        return getUserById(userId);
    }

    fields.push(`updated_at = NOW()`);

    const sql = `
    UPDATE users
    SET ${fields.join(', ')}
    WHERE id = $${idx}
    RETURNING
      id,
      email,
      login,
      first_name,
      last_name,
      phone,
      is_active
  `;
    params.push(userId);

    const { rows } = await dbQuery(sql, params);
    return rows[0] || null;
}

/**
 * updateUserRoles function.
 * @param {*} userId
 * @param {*} roles
 * @returns {Promise<*>}
 */
export async function updateUserRoles(userId, roles) {
    const client = await pool.connect();

    try {
        await client.query('BEGIN');

        // Очистити поточні ролі
        await client.query('DELETE FROM user_roles WHERE user_id = $1', [userId]);

        if (roles.length > 0) {
            // Вставити нові ролі за кодами
            await client.query(
                `
        INSERT INTO user_roles (user_id, role_id)
        SELECT $1, r.id
        FROM roles r
        WHERE r.code = ANY($2::text[])
        `,
                [userId, roles]
            );
        }

        await client.query('COMMIT');
    } catch (err) {
        await client.query('ROLLBACK');
        throw err;
    } finally {
        client.release();
    }

    // Повертаємо оновленого користувача
    const { rows } = await dbQuery(
        `
    SELECT
      u.id,
      u.email,
      u.login,
      u.first_name,
      u.last_name,
      u.phone,
      u.is_active,
      COALESCE(
        ARRAY_AGG(r.code ORDER BY r.code)
          FILTER (WHERE r.code IS NOT NULL),
        '{}'
      ) AS roles
    FROM users u
    LEFT JOIN user_roles ur ON ur.user_id = u.id
    LEFT JOIN roles r ON r.id = ur.role_id
    WHERE u.id = $1
    GROUP BY
      u.id,
      u.email,
      u.login,
      u.first_name,
      u.last_name,
      u.phone,
      u.is_active
    `,
        [userId]
    );

    return rows[0] || null;
}

