import { TestBed } from '@angular/core/testing';
import { Router, ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';
import { anonymous } from './anonymous';
import { Authentication } from '@/app/security/services/authentication';

describe('Anonymous Guard', () => {
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
    mockState = { url: '/login' } as RouterStateSnapshot;
  });

  it('should allow access when user has no token', () => {
    mockAuthService.hasToken?.mockReturnValue(false);

    const result = TestBed.runInInjectionContext(() => 
      anonymous(mockRoute, mockState)
    );

    expect(result).toBeTruthy();
    expect(mockRouter.navigate).not.toHaveBeenCalled();
  });

  it('should redirect to chatbot when user has token', () => {
    mockAuthService.hasToken?.mockReturnValue(true);

    const result = TestBed.runInInjectionContext(() => 
      anonymous(mockRoute, mockState)
    );

    expect(result).toBeFalsy();
    expect(mockRouter.navigate).toHaveBeenCalledWith(['/chatbot']);
  });

  it('should prevent authenticated users from accessing login page', () => {
    mockAuthService.hasToken?.mockReturnValue(true);
    mockState = { url: '/login' } as RouterStateSnapshot;

    const result = TestBed.runInInjectionContext(() => 
      anonymous(mockRoute, mockState)
    );

    expect(result).toBeFalsy();
    expect(mockRouter.navigate).toHaveBeenCalledWith(['/chatbot']);
  });

  it('should prevent authenticated users from accessing register page', () => {
    mockAuthService.hasToken?.mockReturnValue(true);
    mockState = { url: '/register' } as RouterStateSnapshot;

    const result = TestBed.runInInjectionContext(() => 
      anonymous(mockRoute, mockState)
    );

    expect(result).toBeFalsy();
    expect(mockRouter.navigate).toHaveBeenCalledWith(['/chatbot']);
  });
});
