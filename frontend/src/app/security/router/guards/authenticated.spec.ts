import { TestBed } from '@angular/core/testing';
import { Router, ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';
import { authenticated } from './authenticated';
import { Authentication } from '@/app/security/services/authentication';

describe('Authenticated Guard', () => {
  let mockAuthService: jest.Mocked<Partial<Authentication>>;
  let mockRouter: jest.Mocked<Partial<Router>>;
  let mockRoute: ActivatedRouteSnapshot;
  let mockState: RouterStateSnapshot;

  beforeEach(() => {
    mockAuthService = {
      hasToken: jest.fn()
    };

    mockRouter = {
      navigate: jest.fn()
    };

    TestBed.configureTestingModule({
      providers: [
        { provide: Authentication, useValue: mockAuthService },
        { provide: Router, useValue: mockRouter }
      ]
    });

    mockRoute = {} as ActivatedRouteSnapshot;
    mockState = { url: '/chatbot' } as RouterStateSnapshot;
  });

  it('should allow access when user has token', () => {
    mockAuthService.hasToken?.mockReturnValue(true);

    const result = TestBed.runInInjectionContext(() => 
      authenticated(mockRoute, mockState)
    );

    expect(result).toBeTruthy();
    expect(mockRouter.navigate).not.toHaveBeenCalled();
  });

  it('should redirect to login when user has no token', () => {
    mockAuthService.hasToken?.mockReturnValue(false);

    const result = TestBed.runInInjectionContext(() => 
      authenticated(mockRoute, mockState)
    );

    expect(result).toBeFalsy();
    expect(mockRouter.navigate).toHaveBeenCalledWith(
      ['/login'],
      { queryParams: { redirect: '/chatbot' } }
    );
  });

  it('should pass redirect URL in query params', () => {
    mockAuthService.hasToken?.mockReturnValue(false);
    mockState = { url: '/chatbot/messages' } as RouterStateSnapshot;

    TestBed.runInInjectionContext(() => 
      authenticated(mockRoute, mockState)
    );

    expect(mockRouter.navigate).toHaveBeenCalledWith(
      ['/login'],
      { queryParams: { redirect: '/chatbot/messages' } }
    );
  });
});
