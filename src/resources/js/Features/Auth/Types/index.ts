export type ResetPasswordProps = {
  token: string;
  email: string;
};

export type User = {
  id: number;
  name: string;
  email: string;
  password: string;
  created_at: string;
  updated_at: string;
  email_verified_at?: string;
};
