export interface User {
  name: string;
  documento: string;
  celular: string;
  email: string;
  password?: string;
  balance?: number;
}

export interface Transaction {
  amount: number;
  sender_id?: number;
  receiver_id?: number;
}
