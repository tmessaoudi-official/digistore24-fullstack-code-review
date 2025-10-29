import { AfterViewInit, Component, inject, OnDestroy, signal, WritableSignal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { Authentication as AuthenticationService } from '@/app/security/services/authentication';
import { Subject, takeUntil } from 'rxjs';

@Component({
  selector: 'app-login',
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './login.html',
  styleUrls: ['./login.scss']
})
export class Login implements OnDestroy, AfterViewInit {
  public loginForm: FormGroup;
  public errorMessage: WritableSignal<string> = signal<string>('');
  public isLoading: WritableSignal<boolean> = signal<boolean>(false);

  private fb: FormBuilder = inject(FormBuilder);
  private authenticationService: AuthenticationService = inject(AuthenticationService);
  private router: Router = inject(Router);
  private destroy$ = new Subject<void>();

  public constructor(

  ) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required]]
    });
  }

  public ngAfterViewInit(): void {
    if (this.authenticationService.isAuthenticated$() || this.authenticationService.hasToken()) {
      this.router.navigate(['/chatbot']);
    }
  }

  public onSubmit(): void {
    if (this.loginForm.valid) {
      this.isLoading.update((_value) => true);
      this.errorMessage.update((_value) => '');

      this.authenticationService.login(this.loginForm.value).pipe(
        takeUntil(this.destroy$)
      ).subscribe({
        next: () => {
          this.isLoading.update((_value) => false);
          this.router.navigate(['/chatbot']);
        },
        error: (error) => {
          this.isLoading.update((_value) => false);
          this.errorMessage.update((_value) => error.error?.message || 'Login failed. Please check your credentials.');
        }
      });
    }
  }

  public switchToRegister(): void {
    this.router.navigate(['/register']);
  }

  public ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
