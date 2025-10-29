export interface Message {
  id?: number;
  message: string;
  user: string;
  status: MessageStatus;
  created_at: string;
  updated_at: string;
  in_reply_to?: number | null;
  replies?: Message[];
}

export enum MessageStatus {
  DRAFT = 'draft',
  PENDING = 'pending',
  SENT = 'sent',
  RECEIVED = 'received',
  FAILED = 'failed'
}

export class MessageModel implements Message {
  id?: number;
  message: string;
  user: string;
  status: MessageStatus;
  created_at: string;
  updated_at: string;
  in_reply_to?: number | null;
  replies?: MessageModel[];

  constructor(data: Partial<Message>) {
    this.id = data.id;
    this.message = data.message || '';
    this.user = data.user || '';
    this.status = data.status || MessageStatus.DRAFT;
    this.created_at = data.created_at || new Date().toISOString();
    this.updated_at = data.updated_at || new Date().toISOString();
    this.in_reply_to = data.in_reply_to;
    this.replies = data.replies?.map(reply => new MessageModel(reply)) || [];
  }

  isEmpty(): boolean {
    return this.message.trim() === '';
  }

  isPending(): boolean {
    return this.status === MessageStatus.PENDING;
  }

  isSent(): boolean {
    return this.status === MessageStatus.SENT;
  }

  isFailed(): boolean {
    return this.status === MessageStatus.FAILED;
  }
}
