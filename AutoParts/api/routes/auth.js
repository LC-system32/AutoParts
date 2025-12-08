import express from 'express';
import { createUser, findUserByEmail, createSession,getUserById } from '../models/userModel.js';
import bcrypt from 'bcrypt';

export const authRouter = express.Router();


authRouter.post('/register', async (req, res) => {
  try {
    const { name, email, password } = req.body;
    console.log('REGISTER BODY:', req.body);

    if (!name || !email || !password) {
      return res
        .status(400)
        .json({ success: false, error: 'Missing required fields' });
    }

    const login = name;
    const existing = await findUserByEmail(email);
    if (existing) {
      return res
        .status(400)
        .json({ success: false, error: 'Email already registered' });
    }

    const user = await createUser({
      login,
      email,
      password,
      firstName: name, 
      
      
    });

    const token = await createSession(user.id);

    return res.json({
      success: true,
      data: { user, token },
    });
  } catch (err) {
    console.error('REGISTER ERROR:', err);
    return res
      .status(500)
      .json({ success: false, error: 'Internal server error' });
  }
});




authRouter.post('/login', async (req, res) => {
  try {
    const { email, password } = req.body ?? {};

    if (!email || !password) {
      return res.status(400).json({
        success: false,
        error: 'Email та пароль обовʼязкові',
      });
    }

    const user = await findUserByEmail(email);
    if (!user) {
      return res.status(401).json({
        success: false,
        error: 'Невірний email або пароль',
      });
    }

    const ok = await bcrypt.compare(password, user.password_hash);
    if (!ok) {
      return res.status(401).json({
        success: false,
        error: 'Невірний email або пароль',
      });
    }

    const token = await createSession(user.id);
    const fullUser = await getUserById(user.id);

    return res.json({
      success: true,
      data: {
        token,
        user: fullUser,   
      },
    });
  } catch (err) {
    console.error('POST /api/auth/login error', err);
    return res.status(500).json({
      success: false,
      error: 'Внутрішня помилка сервера',
    });
  }
});

authRouter.post('/google', async (req, res) => {
  try {
    const { email, first_name, last_name } = req.body;

    if (!email) {
      return res
        .status(400)
        .json({ success: false, error: 'Email is required' });
    }
    let user = await findUserByEmail(email);

    
    if (!user) {
      const loginBase = (email.split('@')[0] || 'user').replace(/[^a-zA-Z0-9_]/g, '');
      const login =
        'google_' +
        loginBase +
        '_' +
        Math.floor(Math.random() * 1000000);
      const randomPassword = Math.random().toString(36).slice(-12);

      user = await createUser({
        login,
        email,
        password: randomPassword,
        firstName: first_name || '',
        lastName: last_name || '',
        phone: null,
      });
    }
    const token = await createSession(user.id);
    const profile = await getUserById(user.id);

    return res.json({
      success: true,
      token,
      user: profile,
    });
  } catch (err) {
    console.error('POST /api/auth/google error', err);
    return res
      .status(500)
      .json({ success: false, error: 'Google auth failed' });
  }
});
