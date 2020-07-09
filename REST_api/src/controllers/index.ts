import {HomeController as Home} from './HomeController';
import {PruebaController as Prueba} from './PruebaController';
import {TestController as Test} from './TestController';
import {UserController as User} from './UserController';
import {TransactionController as Transaction} from './TransactionController';

export const HomeController = new Home();
export const PruebaController = new Prueba();
export const TestController = new Test();
export const UserController = new User();
export const TransactionController = new Transaction();
