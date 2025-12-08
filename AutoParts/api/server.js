
import express from 'express';
import cors from 'cors';

import { authenticate } from './middlewares/auth.js';

import { authRouter } from './routes/auth.js';
import { brandsRouter } from './routes/brands.js';
import { categoriesRouter } from './routes/categories.js';
import { productsRouter } from './routes/products.js';
import { cartsRouter } from './routes/carts.js';
import { ordersRouter } from './routes/orders.js';
import { wishlistRouter } from './routes/wishlist.js';
import { supportRouter } from './routes/support.js';
import { addressesRouter } from './routes/addresses.js';
import { usersRouter } from './routes/users.js';
import { methodsRouter } from './routes/methods.js';
import { carsRouter } from './routes/cars.js';
import { adminRouter } from './routes/admin.js';

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(express.json());


app.use(authenticate);


app.use('/api/auth', authRouter);
app.use('/api/brands', brandsRouter);
app.use('/api/categories', categoriesRouter);
app.use('/api/products', productsRouter);
app.use('/api/carts', cartsRouter);
app.use('/api/orders', ordersRouter);
app.use('/api/wishlist', wishlistRouter);
app.use('/api/support', supportRouter);
app.use('/api/addresses', addressesRouter);
app.use('/api/users', usersRouter);
app.use('/api/admin', adminRouter);
app.use('/api/cars', carsRouter);
app.use('/api', methodsRouter);


app.get('/', (req, res) => {
  res.json({ success: true, message: 'AutoParts API is running' });
});

app.listen(PORT, () => {
  console.log(`API listening on port ${PORT}`);
});
