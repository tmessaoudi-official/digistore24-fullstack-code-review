import { Component, inject, OnDestroy, signal, WritableSignal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { Authentication as AuthenticationService } from '@/app/security/services/authentication';
import { Subject, takeUntil } from 'rxjs';

@Component({
  selector: 'app-register',
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './register.html',
  styleUrls: ['./register.scss']
})
export class Register implements OnDestroy {
  public registerForm: FormGroup;
  public errorMessage: WritableSignal<string> = signal<string>('');
  public successMessage: WritableSignal<string> = signal<string>('');
  public isLoading: WritableSignal<boolean> = signal<boolean>(false);

  private fb: FormBuilder = inject(FormBuilder);
  private authenticationService: AuthenticationService = inject(AuthenticationService);
  private router: Router = inject(Router);
  private destroy$ = new Subject<void>();

  constructor(

  ) {
    this.registerForm = this.fb.group({
      name: ['', [Validators.required, Validators.minLength(2)]],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [
        Validators.required,
        Validators.minLength(8),
        Validators.pattern(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#~@$!%*?&])[A-Za-z\d#~@$!%*?&]{8,}$/)
      ]]
    });
  }

  onSubmit(): void {
    if (this.registerForm.valid) {
      this.isLoading.update((_value) => true);
      this.errorMessage.update((_value) => '');
      this.successMessage.update((_value) => '');

      this.authenticationService.register(this.registerForm.value).pipe(
        takeUntil(this.destroy$)
      ).subscribe({
        next: () => {
          this.isLoading.update((_value) => false);
          this.successMessage.update((_value) => 'Account created successfully! Redirecting to login...');
          setTimeout(() => {
            
          }, 2000);
        },
        error: (error) => {
          this.isLoading.update((_value) => false);
          this.errorMessage.update((_value) => error.error?.error || 'Registration failed. Please try again.');
        }
      });
    }
  }

  switchToLogin(): void {
    this.router.navigate(['/login']);
  }

  public ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
