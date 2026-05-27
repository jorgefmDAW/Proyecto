import { inject } from '@angular/core';
import {
  HttpInterceptorFn,
  HttpRequest,
  HttpHandlerFn,
  HttpErrorResponse,
} from '@angular/common/http';
import { throwError } from 'rxjs';
import { catchError, switchMap } from 'rxjs/operators';
import { Users } from '../services/users-service';

export const authInterceptor: HttpInterceptorFn = (
  req: HttpRequest<any>,
  next: HttpHandlerFn
) => {
  const usersService = inject(Users);
  const token = usersService.getAccessToken();

  const isRefreshCall = req.url.includes('/token/refresh');
  const isLoginCall = req.url.includes('/login');

  const authReq = token && !isRefreshCall && !isLoginCall
    ? req.clone({ setHeaders: { Authorization: `Bearer ${token}` } })
    : req;

  return next(authReq).pipe(
    catchError((error: HttpErrorResponse) => {
      if (error.status === 401 && !isRefreshCall && !isLoginCall) {
        return usersService.refrescarToken().pipe(
          switchMap(() => {
            const newReq = req.clone({
              setHeaders: {
                Authorization: `Bearer ${usersService.getAccessToken()}`,
              },
            });
            return next(newReq);
          }),
          catchError((refreshError) => throwError(() => refreshError))
        );
      }

      return throwError(() => error);
    })
  );
};