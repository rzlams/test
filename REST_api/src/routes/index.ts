import express from 'express';
import {
  UserController,
  TransactionController,
} from '../controllers';

const router = express.Router(); // eslint-disable-line

router.get('/users/:id/saldo', UserController.consultaSaldo);
router.post('/users/login', UserController.login);
router.post('/users/logout', UserController.logout);
router.post('/users', UserController.registroCliente);

router.post('/recarga', TransactionController.recargaBilletera);
router.post('/pago', TransactionController.solicitaPago);
router.post('/correo-confirmacion', TransactionController.enviaCorreoConfirmacion);
router.post('/confirma-pago', TransactionController.confirmaPago);


export default router;
