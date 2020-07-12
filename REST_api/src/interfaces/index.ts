export interface User {
  id?: string;
  name?: string;
  documento?: string;
  celular?: string;
  email?: string;
  password?: string;
  balance?: number;
  confirmation_token?: string;
  session_token?: string;
}

export interface Transaction {
  id?: string;
  amount?: number;
  sender_id?: string;
  receiver_id?: string;
}
