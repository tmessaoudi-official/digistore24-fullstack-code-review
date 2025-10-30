import { Routes } from '@angular/router';
import { authenticated as authenticatedGuard } from '@/app/security/router/guards/authenticated';
import { anonymous as anonymousGuard } from '@/app/security/router/guards/anonymous';

export const routes: Routes = [
  { path: '', redirectTo: '/login', pathMatch: 'full' },
  { path: 'login', loadComponent: () => import('@/app/security/components/login/login').then(m => m.Login), canActivate: [anonymousGuard] },
  { path: 'register', loadComponent: () => import('@/app/security/components/register/register').then(m => m.Register), canActivate: [anonymousGuard] },
  { path: 'chatbot', loadComponent: () => import('@/app/chatbot/components/chatbot/chatbot').then(m => m.Chatbot), canActivate: [authenticatedGuard] },
  { path: '**', redirectTo: '/login' }
];
