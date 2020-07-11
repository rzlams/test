import * as dotenv from "dotenv";
import path from "path";
import express, {
  Application,
  Errback,
  Request,
  Response,
  NextFunction,
} from "express"; // eslint-disable-line
import logger from "./libs/winstonLogger";
import router from "./routes";
import { errorHandler } from "./libs/ErrorHandler";

class App {
  private dotenv: any;
  private app: Application;
  private port: string | number;

  constructor() {
    this.dotenv = dotenv.config();
    this.port = process.env.PORT || 4444;
    this.app = express();
    this.loadMiddlewares();
    this.loadRoutes();
    this.errorMiddleware();
  }

  private loadMiddlewares(): void {
    // this.app.use(logger);
    this.app.use(express.json());
    this.app.use(express.urlencoded({ extended: true }));
  }

  private loadRoutes(): void {
    this.app.use('/soap', router);
  }

  private errorMiddleware(): void {
    this.app.use(errorHandler);
  }

  public listen(): void {
    this.app.listen(this.port, (): void => {
      console.log(`Server listening on port: ${this.port}`);
    });
  }
}

new App().listen();
