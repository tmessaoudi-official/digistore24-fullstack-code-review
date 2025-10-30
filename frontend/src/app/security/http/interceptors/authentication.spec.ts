import { TestBed } from '@angular/core/testing';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { HttpClient, provideHttpClient, withInterceptors } from '@angular/common/http';
import { Router } from '@angular/router';
import { authentication } from './authentication';
import { Authentication as AuthenticationService } from '@/app/security/services/authentication';
import { ToastrService } from 'ngx-toastr';

describe('Authentication Interceptor', () => {
  let httpClient: HttpClient;
  let httpMock: HttpTestingController;
  let mockAuthService: jest.Mocked<Partial<AuthenticationService>>;
  let mockRouter: jest.Mocked<Partial<Router>>;
  let mockToastr: jest.Mocked<Partial<ToastrService>>;

  beforeEach(() => {
    mockAuthService = {
      getToken: jest.fn(),
      logout: jest.fn()
    };

    mockRouter = {
      navigate: jest.fn()
    };

    mockToastr = {
      error: jest.fn()
    };

    TestBed.configureTestingModule({
      providers: [
        provideHttpClient(withInterceptors([authentication])),
        provideHttpClientTesting(),
        { provide: AuthenticationService, useValue: mockAuthService },
        { provide: Router, useValue: mockRouter },
        { provide: ToastrService, useValue: mockToastr }
      ]
    });

    httpClient = TestBed.inject(HttpClient);
    httpMock = TestBed.inject(HttpTestingController);
  });

  afterEach(() => {
    httpMock.verify();
  });

  it('should add Authorization header when token exists', () => {
    const token = 'test-token';
    mockAuthService.getToken?.mockReturnValue(token);

    httpClient.get('/api/test').subscribe();

    const req = httpMock.expectOne('/api/test');
    expect(req.request.headers.get('Authorization')).toBe(`Bearer ${token}`);
    req.flush({});
  });

  it('should not add Authorization header when no token', () => {
    mockAuthService.getToken?.mockReturnValue(null);

    httpClient.get('/api/test').subscribe();

    const req = httpMock.expectOne('/api/test');
    expect(req.request.headers.has('Authorization')).toBeFalsy();
    req.flush({});
  });

  it('should logout and redirect on 401 error', () => {
    const token = 'test-token';
    mockAuthService.getToken?.mockReturnValue(token);

    httpClient.get('/api/test').subscribe({
      error: () => {
        expect(mockAuthService.logout).toHaveBeenCalled();
        expect(mockRouter.navigate).toHaveBeenCalledWith(['/login']);
      }
    });

    const req = httpMock.expectOne('/api/test');
    req.flush({}, { status: 401, statusText: 'Unauthorized' });
  });

  it('should show error toast on non-401 errors', () => {
    const token = 'test-token';
    mockAuthService.getToken?.mockReturnValue(token);

    httpClient.get('/api/test').subscribe({
      error: () => {
        expect(mockToastr.error).toHaveBeenCalledWith('Something went wrong ...');
      }
    });

    const req = httpMock.expectOne('/api/test');
    req.flush({}, { status: 500, statusText: 'Server Error' });
  });
});
