import { Component, inject, signal, WritableSignal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { Authentication as AuthenticationService } from '@/app/security/services/authentication';

@Component({
  selector: 'app-login',
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './login.html',
  styleUrls: ['./login.scss']
})
export class Login {
  public loginForm: FormGroup;
  public errorMessage: WritableSignal<string> = signal<string>('');
  public isLoading: WritableSignal<boolean> = signal<boolean>(false);

  private fb: FormBuilder = inject(FormBuilder);
  private authenticationService: AuthenticationService = inject(AuthenticationService);
  private router: Router = inject(Router);

  constructor(

  ) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required]]
    });
  }

  onSubmit(): void {
    if (this.loginForm.valid) {
      this.isLoading.update((_value) => true);
      this.errorMessage.update((_value) => '');

      this.authenticationService.login(this.loginForm.value).subscribe({
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

  switchToRegister(): void {
    this.router.navigate(['/register']);
  }
}
