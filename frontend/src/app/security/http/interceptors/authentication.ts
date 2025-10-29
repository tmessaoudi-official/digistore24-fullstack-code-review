import { HttpErrorResponse, HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { Authentication as AuthenticationService } from '@/app/security/services/authentication';
import { catchError, throwError } from 'rxjs';
import { Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';

export const authentication: HttpInterceptorFn = (req, next) => {
  const authenticationService = inject(AuthenticationService);
  const router = inject(Router);
  const tostrService = inject(ToastrService);
  const token = authenticationService.getToken();

  if (token) {
    req = req.clone({
      setHeaders: {
        Authorization: `Bearer ${token}`
      }
    });

    return next(req).pipe(
      catchError((error) => {
        if (error instanceof HttpErrorResponse && error.status === 401) {
          authenticationService.logout();
          router.navigate(['/login']);
        } else {
          tostrService.error('Something went wrong ...');
        }
        return throwError(() => error);
      })
    );
  }

  return next(req);
};
