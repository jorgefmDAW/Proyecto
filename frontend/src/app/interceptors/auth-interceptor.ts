<<<<<<< HEAD
import { inject } from '@angular/core';
import {
  HttpInterceptorFn,
  HttpRequest,
  HttpHandlerFn,
  HttpErrorResponse,
} from '@angular/common/http';
import { throwError, BehaviorSubject } from 'rxjs';
import { catchError, switchMap, filter, take } from 'rxjs/operators';
import { Users } from '../services/users-service';

let isRefreshing = false;
let refreshSubject = new BehaviorSubject<string | null>(null);

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

        if (isRefreshing) {
          // Si ya hay un refresh en curso, espera a que termine y reintenta
          return refreshSubject.pipe(
            filter(token => token !== null),
            take(1),
            switchMap(token => {
              return next(req.clone({
                setHeaders: { Authorization: `Bearer ${token}` }
              }));
            })
          );
        }

        isRefreshing = true;
        refreshSubject.next(null);

        return usersService.refrescarToken().pipe(
          switchMap(() => {
            isRefreshing = false;
            const newToken = usersService.getAccessToken();
            refreshSubject.next(newToken);
            return next(req.clone({
              setHeaders: { Authorization: `Bearer ${newToken}` }
            }));
          }),
          catchError((refreshError) => {
            isRefreshing = false;
            refreshSubject.next(null);
            return throwError(() => refreshError);
          })
        );
      }
      return throwError(() => error);
    })
  );
=======
import { inject } from '@angular/core';
import {
  HttpInterceptorFn,
  HttpRequest,
  HttpHandlerFn,
  HttpErrorResponse,
} from '@angular/common/http';
import { throwError, BehaviorSubject } from 'rxjs';
import { catchError, switchMap, filter, take } from 'rxjs/operators';
import { Users } from '../services/users-service';

let isRefreshing = false;
let refreshSubject = new BehaviorSubject<string | null>(null);

export const authInterceptor: HttpInterceptorFn = (
  req: HttpRequest<any>,
  next: HttpHandlerFn
) => {
  const usersService = inject(Users);
  const token = usersService.getAccessToken();
  const refreshToken = usersService.getRefreshToken();
  const isRefreshCall = req.url.includes('/token/refresh');
  const isLoginCall = req.url.includes('/login');

  const authReq = token && !isRefreshCall && !isLoginCall
    ? req.clone({ setHeaders: { Authorization: `Bearer ${token}` } })
    : req;

  return next(authReq).pipe(
    catchError((error: HttpErrorResponse) => {
      if (error.status === 401 && !isRefreshCall && !isLoginCall) {
        
        if (!refreshToken) {
          return throwError(() => error);
        }

        if (isRefreshing) {
          return refreshSubject.pipe(
            filter(t => t !== null),
            take(1),
            switchMap(t => {
              return next(req.clone({
                setHeaders: { Authorization: `Bearer ${t}` }
              }));
            })
          );
        }

        isRefreshing = true;
        refreshSubject.next(null);

        return usersService.refrescarToken().pipe(
          switchMap(() => {
            isRefreshing = false;
            const newToken = usersService.getAccessToken();
            refreshSubject.next(newToken);
            return next(req.clone({
              setHeaders: { Authorization: `Bearer ${newToken}` }
            }));
          }),
          catchError((refreshError) => {
            isRefreshing = false;
            refreshSubject.next(null);
            return throwError(() => refreshError);
          })
        );
      }
      return throwError(() => error);
    })
  );
>>>>>>> main
};