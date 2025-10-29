import { Component, inject, Signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { MessageList as MessageListComponent } from '@/app/chatbot/components/message-list/message-list';
import { MessageForm as MessageFormComponent } from '@/app/chatbot/components/message-form/message-form';
import { Authentication as AuthenticationService } from '@/app/security/services/authentication';
import { User } from '@/app/security/models/user';

@Component({
  selector: 'app-chatbot',
  imports: [CommonModule, MessageListComponent, MessageFormComponent],
  templateUrl: './chatbot.html',
  styleUrls: ['./chatbot.scss'],
})
export class Chatbot {
  private authenticationService: AuthenticationService = inject(AuthenticationService);
  public currentUser: Signal<User | null> = this.authenticationService.currentUser$;
  private router: Router = inject(Router);

  logout(): void {
    this.authenticationService.logout();
    this.router.navigate(['/login']);
  }
}
