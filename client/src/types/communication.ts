// Communication types
export interface Message {
  id: string;
  conversationId: string;
  senderId: string;
  receiverId: string;
  content: string;
  timestamp: string;
  isRead: boolean;
  type: 'text' | 'image' | 'file' | 'audio';
  attachments?: MessageAttachment[];
  priority: 'normal' | 'urgent';
}

export interface MessageAttachment {
  id: string;
  filename: string;
  url: string;
  size: number;
  mimeType: string;
}

export interface Conversation {
  id: string;
  participants: string[];
  lastMessage?: Message;
  unreadCount: number;
  isActive: boolean;
  createdAt: string;
  updatedAt: string;
}

// Appointment types
export interface Appointment {
  id: string;
  userId: string;
  title: string;
  description?: string;
  startTime: string;
  endTime: string;
  type: AppointmentType;
  location?: AppointmentLocation;
  provider: HealthcareProvider;
  status: AppointmentStatus;
  reminders: AppointmentReminder[];
  notes?: string[];
  isRecurring?: boolean;
  recurringPattern?: RecurringPattern;
}

export type AppointmentType = 
  | 'oncology'
  | 'primary-care'
  | 'specialist'
  | 'lab-work'
  | 'imaging'
  | 'therapy'
  | 'support-group'
  | 'telehealth'
  | 'other';

export interface AppointmentLocation {
  name: string;
  address: string;
  room?: string;
  phone?: string;
  telehealth?: {
    platform: string;
    meetingId: string;
    joinUrl: string;
  };
}

export interface HealthcareProvider {
  id: string;
  name: string;
  specialty: string;
  phone: string;
  email?: string;
  hospital?: string;
}

export type AppointmentStatus = 
  | 'scheduled'
  | 'confirmed'
  | 'rescheduled'
  | 'cancelled'
  | 'completed'
  | 'no-show';

export interface AppointmentReminder {
  id: string;
  appointmentId: string;
  time: string; // ISO string
  method: 'email' | 'sms' | 'push';
  message: string;
  isSent: boolean;
}

export interface RecurringPattern {
  frequency: 'daily' | 'weekly' | 'monthly' | 'yearly';
  interval: number;
  daysOfWeek?: number[];
  endDate?: string;
  maxOccurrences?: number;
}