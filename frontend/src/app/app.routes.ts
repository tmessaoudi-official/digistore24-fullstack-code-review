import { Routes } from '@angular/router';
import { Login as LoginComponent } from '@/app/security/components/login/login';
import { Register as RegisterComponent } from '@/app/security/components/register/register';
import { Chatbot as ChatbotComponent } from '@/app/chatbot/components/chatbot/chatbot';
import { authenticated as authenticatedGuard } from '@/app/security/router/guards/authenticated';
import { anonymous as anonymousGuard } from '@/app/security/router/guards/anonymous';

export const routes: Routes = [
  { path: '', redirectTo: '/login', pathMatch: 'full' },
  { path: 'login', component: LoginComponent, canActivate: [anonymousGuard] },
  { path: 'register', component: RegisterComponent, canActivate: [anonymousGuard] },
  { path: 'chatbot', component: ChatbotComponent, canActivate: [authenticatedGuard] },
  { path: '**', redirectTo: '/login' }
];
