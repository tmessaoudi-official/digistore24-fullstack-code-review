import { inject } from '@angular/core';
import { Router, CanActivateFn, ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';
import { Authentication as AuthenticationService } from '@/app/security/services/authentication';

export const authenticated: CanActivateFn = (route: ActivatedRouteSnapshot, state: RouterStateSnapshot) => {
  const authenticationService = inject(AuthenticationService);
  const router = inject(Router);

  if (authenticationService.hasToken()) {
    return true;
  }

  router.navigate(['/login'], { queryParams: { redirect: state.url } });
  return false;
};
