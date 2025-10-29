import { Routes } from '@angular/router';
import { Login as LoginComponent } from '@/app/security/components/login/login';
import { Register as RegisterComponent } from '@/app/security/components/register/register';

export const routes: Routes = [
  { path: '', redirectTo: '/login', pathMatch: 'full' },
  { path: 'login', component: LoginComponent },
  { path: 'register', component: RegisterComponent },
  { path: '**', redirectTo: '/login' }
];
