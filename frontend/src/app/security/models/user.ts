export interface User {
  id?: number;
  email: string;
  name: string;
}

export interface AuthenticationResponse {
  token: string;
  user?: User;
}

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterRequest extends LoginCredentials {
  name: string;
}
