import { TestBed } from '@angular/core/testing';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { provideHttpClient } from '@angular/common/http';
import { Message } from './message';
import { MessageModel, MessageStatus } from '@/app/chatbot/models/message';
import { environment } from '@/environments/environment';

describe('Message Service', () => {
  let service: Message;
  let httpMock: HttpTestingController;

  const mockMessages = [
    {
      id: 1,
      message: 'Hello',
      user: 'User 1',
      status: 'sent' as MessageStatus,
      created_at: '2024-01-01T00:00:00Z',
      updated_at: '2024-01-01T00:00:00Z'
    },
    {
      id: 2,
      message: 'Hi there',
      user: 'User 2',
      status: 'received' as MessageStatus,
      created_at: '2024-01-01T00:01:00Z',
      updated_at: '2024-01-01T00:01:00Z'
    }
  ];

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [
        Message,
        provideHttpClient(),
        provideHttpClientTesting()
      ]
    });

    service = TestBed.inject(Message);
    httpMock = TestBed.inject(HttpTestingController);
  });

  afterEach(() => {
    httpMock.verify();
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });

  it('should initialize with empty messages', () => {
    expect(service.messages$()).toEqual([]);
    expect(service.loading$()).toBeFalsy();
  });

  describe('loadMessages()', () => {
    it('should load messages from API', (done) => {
      service.loadMessages().subscribe({
        next: () => {
          const messages = service.messages$();
          expect(messages.length).toBe(2);
          expect(messages[0].message).toBe('Hello');
          expect(service.loading$()).toBeFalsy();
          done();
        }
      });

      const req = httpMock.expectOne(`${environment.apiUrl}/messages`);
      expect(req.request.method).toBe('GET');
      req.flush(mockMessages);
    });

    it('should set loading state', () => {
      service.loadMessages().subscribe();

      expect(service.loading$()).toBeTruthy();

      const req = httpMock.expectOne(`${environment.apiUrl}/messages`);
      req.flush(mockMessages);
    });
  });

  describe('sendMessage()', () => {
    it('should send message to API', () => {
      const content = 'New message';

      service.sendMessage(content).subscribe();

      const sendReq = httpMock.expectOne(`${environment.apiUrl}/messages`);
      expect(sendReq.request.method).toBe('POST');
      expect(sendReq.request.body).toEqual({ message: content });
      sendReq.flush(null);

      const loadReq = httpMock.expectOne(`${environment.apiUrl}/messages`);
      loadReq.flush(mockMessages);
    });
  });

  describe('addMessageLocally()', () => {
    it('should add message to local state', () => {
      const newMessage = new MessageModel({
        id: 3,
        message: 'Local message',
        user: 'User 3',
        status: MessageStatus.PENDING,
        created_at: '2024-01-01T00:02:00Z',
        updated_at: '2024-01-01T00:02:00Z'
      });

      service.addMessageLocally(newMessage);

      const messages = service.messages$();
      expect(messages.length).toBe(1);
      expect(messages[0].message).toBe('Local message');
    });
  });

  describe('updateMessageStatus()', () => {
    it('should update message status', () => {
      const message = new MessageModel({
        id: 1,
        message: 'Test',
        user: 'User',
        status: MessageStatus.PENDING,
        created_at: '2024-01-01T00:00:00Z',
        updated_at: '2024-01-01T00:00:00Z'
      });

      service.addMessageLocally(message);
      service.updateMessageStatus(1, MessageStatus.SENT);

      const messages = service.messages$();
      expect(messages[0].status).toBe(MessageStatus.SENT);
    });

    it('should not update if messageId is undefined', () => {
      const message = new MessageModel({
        id: 1,
        message: 'Test',
        user: 'User',
        status: MessageStatus.PENDING,
        created_at: '2024-01-01T00:00:00Z',
        updated_at: '2024-01-01T00:00:00Z'
      });

      service.addMessageLocally(message);
      service.updateMessageStatus(undefined, MessageStatus.SENT);

      const messages = service.messages$();
      expect(messages[0].status).toBe(MessageStatus.PENDING);
    });
  });

  describe('clearMessages()', () => {
    it('should clear all messages', () => {
      const message = new MessageModel({
        id: 1,
        message: 'Test',
        user: 'User',
        status: MessageStatus.SENT,
        created_at: '2024-01-01T00:00:00Z',
        updated_at: '2024-01-01T00:00:00Z'
      });

      service.addMessageLocally(message);
      expect(service.messages$().length).toBe(1);

      service.clearMessages();
      expect(service.messages$().length).toBe(0);
    });
  });
});
