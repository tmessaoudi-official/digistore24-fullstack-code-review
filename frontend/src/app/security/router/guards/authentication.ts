import { inject } from '@angular/core';
import { Router, CanActivateFn } from '@angular/router';
import { Authentication as AuthenticationService } from '@/app/security/services/authentication';

export const authentication: CanActivateFn = () => {
  const authenticationService = inject(AuthenticationService);
  const router = inject(Router);

  if (authenticationService.hasToken()) {
    return true;
  }

  router.navigate(['/login'], { queryParams: { redirect: router.url } });
  return false;
};
