import { inject, Injectable, signal, WritableSignal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { map, mergeMap, Observable, tap } from 'rxjs';
import { AuthenticationResponse, LoginCredentials, RegisterRequest, User } from '@/app/security/models/user';
import { environment } from '@/environments/environment';


@Injectable({
  providedIn: 'root'
})
export class Authentication {
  private readonly TOKEN_KEY = 'auth_token';
  private readonly USER_KEY = 'current_user';

  private currentUserSubject: WritableSignal<User | null> = signal<User | null>(this.getUserFromStorage());
  public currentUser$ = this.currentUserSubject.asReadonly();

  private isAuthenticatedSubject: WritableSignal<boolean> = signal<boolean>(this.hasToken());
  public isAuthenticated$ = this.isAuthenticatedSubject.asReadonly();

  private http: HttpClient = inject(HttpClient);

  public register(data: RegisterRequest): Observable<never> {
    return this.http.post<never>(`${environment.apiUrl}/auth/register`, data);
  }

  public login(credentials: LoginCredentials): Observable<{ user: User; token: string }> {
    return this.http.post<AuthenticationResponse>(`${environment.apiUrl}/auth/login`, credentials)
      .pipe(
        tap(response => {
          if (response.token) {
            this.setToken(response.token);
            this.isAuthenticatedSubject.update((_value) => true);
          }
        }),
        mergeMap(response => {
          return this.getMe().pipe(
            tap(user => {
              this.setUser(user);
            }),
            map(user => ({
              user,
              token: response.token
            }))
          );
        })
      );
  }

  public getMe(): Observable<User> {
    return this.http.get<User>(`${environment.apiUrl}/auth/me`)
      .pipe(
        tap(user => {
          this.setUser(user);
        })
      );
  }

  public logout(): void {
    localStorage.removeItem(this.TOKEN_KEY);
    localStorage.removeItem(this.USER_KEY);
    this.currentUserSubject.update((_value) => null);
    this.isAuthenticatedSubject.update((_value) => false);
  }

  public getToken(): string | null {
    return localStorage.getItem(this.TOKEN_KEY);
  }

  public hasToken(): boolean {
    return !!this.getToken();
  }

  private setToken(token: string): void {
    localStorage.setItem(this.TOKEN_KEY, token);
  }

  private setUser(user: User): void {
    localStorage.setItem(this.USER_KEY, JSON.stringify(user));
    this.currentUserSubject.update((_value) => user);
  }

  private getUserFromStorage(): User | null {
    const userJson = localStorage.getItem(this.USER_KEY);
    return userJson ? JSON.parse(userJson) : null;
  }
}
