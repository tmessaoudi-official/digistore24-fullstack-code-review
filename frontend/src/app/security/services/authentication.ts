import { inject, Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, map, mergeMap, Observable, tap } from 'rxjs';
import { AuthenticationResponse, LoginCredentials, RegisterRequest, User } from '@/app/models/user';
import { environment } from '@/environments/environment';


@Injectable({
  providedIn: 'root'
})
export class Authentication {
  private readonly TOKEN_KEY = 'auth_token';
  private readonly USER_KEY = 'current_user';
  
  private currentUserSubject = new BehaviorSubject<User | null>(this.getUserFromStorage());
  public currentUser$ = this.currentUserSubject.asObservable();

  private isAuthenticatedSubject = new BehaviorSubject<boolean>(this.hasToken());
  public isAuthenticated$ = this.isAuthenticatedSubject.asObservable();

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
            this.isAuthenticatedSubject.next(true);
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
    this.currentUserSubject.next(null);
    this.isAuthenticatedSubject.next(false);
  }

  public getToken(): string | null {
    return localStorage.getItem(this.TOKEN_KEY);
  }

  public hasToken(): boolean {
    return !!this.getToken();
  }

  public getCurrentUser(): User | null {
    return this.currentUserSubject.value;
  }

  private setToken(token: string): void {
    localStorage.setItem(this.TOKEN_KEY, token);
  }

  private setUser(user: User): void {
    localStorage.setItem(this.USER_KEY, JSON.stringify(user));
    this.currentUserSubject.next(user);
  }

  private getUserFromStorage(): User | null {
    const userJson = localStorage.getItem(this.USER_KEY);
    return userJson ? JSON.parse(userJson) : null;
  }
}
