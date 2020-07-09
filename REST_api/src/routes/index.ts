import express from 'express';
import {
  HomeController,
  UserController,
  TransactionController,
} from '../controllers';

const router = express.Router(); // eslint-disable-line

router.get('/users/:id/edit', UserController.edit);
router.get('/users/create', UserController.create);
router.put('/user/update', UserController.update);
router.get('/users', UserController.index);
router.post('/users', UserController.store);
router.get('/users/:id', UserController.show);
router.delete('/users/:id', UserController.destroy);

export default router;
