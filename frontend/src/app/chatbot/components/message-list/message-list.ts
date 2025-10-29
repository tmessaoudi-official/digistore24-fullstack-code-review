import { Component, OnInit, OnDestroy, inject, Signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Subject, takeUntil } from 'rxjs';
import { Message as MessageComponent } from '@/app/chatbot/components/message/message';
import { Message as MessageService } from '@/app/chatbot/services/message';
import { MessageModel } from '@/app/chatbot/models/message';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-message-list',
  standalone: true,
  imports: [
    CommonModule,
    MessageComponent,
  ],
  templateUrl: './message-list.html',
  styleUrls: ['./message-list.scss'],
})
export class MessageList implements OnInit, OnDestroy {
  private messageService: MessageService = inject(MessageService);
  public messages: Signal<MessageModel[]> = this.messageService.messages$;
  public loading: Signal<boolean> = this.messageService.loading$;
  private destroy$ = new Subject<void>();

  private toastrService: ToastrService = inject(ToastrService);

  ngOnInit(): void {
    this.refreshMessages();
  }

  refreshMessages(): void {
    this.messageService.loadMessages().pipe(
      takeUntil(this.destroy$)
    ).subscribe({
      error: (error) => {
        this.toastrService.error('Failed to load messages:', JSON.stringify(error));
      }
    });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
