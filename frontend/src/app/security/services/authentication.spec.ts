import { TestBed } from '@angular/core/testing';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { provideHttpClient } from '@angular/common/http';
import { Authentication } from './authentication';
import { User, LoginCredentials, RegisterRequest } from '@/app/security/models/user';
import { environment } from '@/environments/environment';

describe('Authentication Service', () => {
  let service: Authentication;
  let httpMock: HttpTestingController;

  const mockUser: User = {
    id: 1,
    email: 'test@example.com',
    name: 'Test User'
  };

  const mockToken = 'mock-jwt-token';

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [
        Authentication,
        provideHttpClient(),
        provideHttpClientTesting()
      ]
    });

    service = TestBed.inject(Authentication);
    httpMock = TestBed.inject(HttpTestingController);
    localStorage.clear();
  });

  afterEach(() => {
    httpMock.verify();
    localStorage.clear();
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });

  it('should initialize with no user when localStorage is empty', () => {
    expect(service.currentUser$()).toBeNull();
    expect(service.isAuthenticated$()).toBeFalsy();
  });

  describe('register()', () => {
    it('should send POST request to register endpoint', () => {
      const registerData: RegisterRequest = {
        name: 'New User',
        email: 'new@example.com',
        password: 'password123'
      };

      service.register(registerData).subscribe();

      const req = httpMock.expectOne(`${environment.apiUrl}/auth/register`);
      expect(req.request.method).toBe('POST');
      expect(req.request.body).toEqual(registerData);
      req.flush(null);
    });
  });

  describe('login()', () => {
    it('should login successfully and return user with token', (done) => {
      const credentials: LoginCredentials = {
        email: 'test@example.com',
        password: 'password123'
      };

      service.login(credentials).subscribe({
        next: (result) => {
          expect(result.user).toEqual(mockUser);
          expect(result.token).toBe(mockToken);
          expect(service.getToken()).toBe(mockToken);
          expect(service.isAuthenticated$()).toBeTruthy();
          done();
        }
      });

      const loginReq = httpMock.expectOne(`${environment.apiUrl}/auth/login`);
      loginReq.flush({ token: mockToken });

      const meReq = httpMock.expectOne(`${environment.apiUrl}/auth/me`);
      meReq.flush(mockUser);
    });

    it('should store token in localStorage', (done) => {
      const credentials: LoginCredentials = {
        email: 'test@example.com',
        password: 'password123'
      };

      service.login(credentials).subscribe({
        next: () => {
          expect(localStorage.getItem('auth_token')).toBe(mockToken);
          done();
        }
      });

      const loginReq = httpMock.expectOne(`${environment.apiUrl}/auth/login`);
      loginReq.flush({ token: mockToken });

      const meReq = httpMock.expectOne(`${environment.apiUrl}/auth/me`);
      meReq.flush(mockUser);
    });
  });

  describe('logout()', () => {
    it('should clear token and user from localStorage', () => {
      localStorage.setItem('auth_token', mockToken);
      localStorage.setItem('current_user', JSON.stringify(mockUser));

      service.logout();

      expect(localStorage.getItem('auth_token')).toBeNull();
      expect(localStorage.getItem('current_user')).toBeNull();
      expect(service.currentUser$()).toBeNull();
      expect(service.isAuthenticated$()).toBeFalsy();
    });
  });

  describe('getToken()', () => {
    it('should return null when no token exists', () => {
      expect(service.getToken()).toBeNull();
    });

    it('should return token from localStorage', () => {
      localStorage.setItem('auth_token', mockToken);
      expect(service.getToken()).toBe(mockToken);
    });
  });

  describe('hasToken()', () => {
    it('should return false when no token exists', () => {
      expect(service.hasToken()).toBeFalsy();
    });

    it('should return true when token exists', () => {
      localStorage.setItem('auth_token', mockToken);
      expect(service.hasToken()).toBeTruthy();
    });
  });
});
