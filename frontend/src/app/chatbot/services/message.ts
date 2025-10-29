import { inject, Injectable, signal, WritableSignal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { Message as MessageInterface, MessageModel, MessageStatus } from '@/app/chatbot/models/message';
import { environment } from '@/environments/environment';


@Injectable({
  providedIn: 'root'
})
export class Message {
  private messagesSignal: WritableSignal<MessageModel[]> = signal<MessageModel[]>([]);
  public messages$ = this.messagesSignal.asReadonly();

  private loadingSIgnal: WritableSignal<boolean> = signal<boolean>(false);
  public loading$ = this.loadingSIgnal.asReadonly();

  private http: HttpClient = inject(HttpClient);

  loadMessages(): Observable<MessageInterface[]> {
    this.loadingSIgnal.update((_prev) => !_prev);
    
    return this.http.get<MessageInterface[]>(`${environment.apiUrl}/messages`)
      .pipe(
        tap(messages => {
          const messageModels = messages.map(message => new MessageModel(message));
          this.messagesSignal.update((_prev) => messageModels);
          this.loadingSIgnal.update((_prev) => !_prev);
        })
      );
  }

  sendMessage(content: string): Observable<never> {
    const payload = { message: content };
    
    return this.http.post<never>(`${environment.apiUrl}/messages`, payload)
      .pipe(
        tap(() => {
          this.loadMessages().subscribe();
        })
      );
  }

  addMessageLocally(message: MessageModel): void {
    this.messagesSignal.update((_prev) => [message, ...(this.messagesSignal() || [])]);
  }

  updateMessageStatus(messageId: number | undefined, status: MessageStatus): void {
    if (!messageId) return;
    
    const messages = this.messagesSignal().map(msg => {
      if (msg.id === messageId) {
        msg.status = status;
      }
      return msg;
    });
    
    this.messagesSignal.update((_prev) => messages || []);
  }

  clearMessages(): void {
    this.messagesSignal.update((_prev) => []);
  }
}
