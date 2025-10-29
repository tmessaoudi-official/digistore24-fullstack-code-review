import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MessageModel, MessageStatus } from '@/app/chatbot/models/message';

@Component({
  selector: 'app-message',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './message.html',
  styleUrls: ['./message.scss'],
})
export class Message {
  @Input({ required: true }) public message!: MessageModel;

  getStatusLabel(): string {
    const labels: Record<MessageStatus, string> = {
      [MessageStatus.DRAFT]: 'Draft',
      [MessageStatus.PENDING]: 'Sending...',
      [MessageStatus.SENT]: 'Sent',
      [MessageStatus.RECEIVED]: 'Received',
      [MessageStatus.FAILED]: 'Failed'
    };
    return labels[this.message.status];
  }

  formatTimestamp(timestamp: string): string {
    const date = new Date(timestamp);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    const diffMins = Math.floor(diffMs / 60000);
    
    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins}m ago`;
    
    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) return `${diffHours}h ago`;
    
    return date.toLocaleDateString();
  }
}
