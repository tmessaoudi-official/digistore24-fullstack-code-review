import { Component, inject, OnDestroy, signal, WritableSignal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Message as MessageService } from '@/app/chatbot/services/message';
import { MessageModel, MessageStatus } from '@/app/chatbot/models/message';
import { Subject, takeUntil } from 'rxjs';

@Component({
  selector: 'app-message-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './message-form.html',
  styleUrls: ['./message-form.scss']
})
export class MessageForm implements OnDestroy {
  public messageForm: FormGroup;
  public previewMessage: WritableSignal<MessageModel | null> = signal<MessageModel | null>(null);
  public isSending: WritableSignal<boolean> = signal<boolean>(false);
  public errorMessage: WritableSignal<string> = signal<string>('');

  private fb: FormBuilder = inject(FormBuilder);
  private messageService: MessageService = inject(MessageService);
    private destroy$ = new Subject<void>();

  constructor(

  ) {
    this.messageForm = this.fb.group({
      message: ['', [Validators.required, Validators.minLength(1)]]
    });

    this.previewMessage.update((_value) => new MessageModel({
      message: '',
      user: 'You',
      status: MessageStatus.DRAFT,
      created_at: new Date().toISOString()
    }));

    this.messageForm.get('message')?.valueChanges.pipe(
      takeUntil(this.destroy$)
    ).subscribe(value => {
      this.previewMessage.update((_value) => new MessageModel({
        message: value || '',
        user: 'You',
        status: MessageStatus.DRAFT,
        created_at: new Date().toISOString()
      }));
    });
  }

  onSubmit(): void {
    if (this.messageForm.valid) {
      this.isSending.update((_value) => true);
      this.errorMessage.update((_value) => '');

      const messageContent = this.messageForm.get('message')?.value;

      const currentMessages = this.messageService.messages$();
      const lastMessage = currentMessages[currentMessages.length - 1];

      this.previewMessage.update((value: MessageModel | null) => {
        (value as MessageModel).status = MessageStatus.PENDING;
        (value as MessageModel).id = lastMessage?.id ? lastMessage.id + 1 : 1;
        return value;
      });

      this.messageService.addMessageLocally(this.previewMessage() as MessageModel);

      this.messageService.sendMessage(messageContent).pipe(
        takeUntil(this.destroy$)
      ).subscribe({
        next: () => {
          this.isSending.update((_value) => false);
          this.messageForm.reset();
        },
        error: (error) => {
          this.isSending.update((_value) => false);
          setTimeout(() => {
            this.messageService.updateMessageStatus((this.previewMessage() as MessageModel).id, MessageStatus.FAILED);

          }, 1000);
          this.errorMessage.update((_value) => error.error?.error || 'Failed to send message. Please try again.');
        },
        complete: () => {
          setTimeout(() => {
            this.previewMessage.update((_value) => new MessageModel({
              message: '',
              user: 'You',
              status: MessageStatus.DRAFT,
              created_at: new Date().toISOString()
            }));
          }, 2000);
        }
      });
    }
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
