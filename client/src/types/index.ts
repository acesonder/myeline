import { User } from './user';
import { SymptomLog, MoodLog, VitalSigns, HydrationLog } from './health';
import { Medication, MedicationLog } from './medication';
import { Message, Appointment } from './communication';
import { ComfortActivity, WeatherInfo } from './comfort';
import { DashboardLayout, CareCard } from './dashboard';

// Export all types
export * from './user';
export * from './health';
export * from './medication';
export * from './dashboard';
export * from './communication';
export * from './comfort';

// API Response types
export interface ApiResponse<T = any> {
  success: boolean;
  data?: T;
  message?: string;
  errors?: string[];
  pagination?: {
    page: number;
    limit: number;
    total: number;
    totalPages: number;
  };
}

// Authentication types
export interface LoginCredentials {
  email: string;
  password: string;
}

export interface SignupData {
  email: string;
  password: string;
  firstName: string;
  lastName: string;
  username: string;
  role: 'patient' | 'caregiver';
  securityQuestion: string;
  securityAnswer: string;
  agreeToTerms: boolean;
}

export interface AuthResponse {
  user: User;
  token: string;
  refreshToken: string;
  expiresAt: string;
}

// Socket event types
export interface SocketEvents {
  // Incoming events
  'user:connect': (data: { userId: string }) => void;
  'user:disconnect': (data: { userId: string }) => void;
  'message:new': (message: Message) => void;
  'symptom:logged': (symptom: SymptomLog) => void;
  'medication:taken': (log: MedicationLog) => void;
  'mood:updated': (mood: MoodLog) => void;
  'vitals:updated': (vitals: VitalSigns) => void;
  'appointment:reminder': (appointment: Appointment) => void;
  'care-card:updated': (careCard: CareCard) => void;
  'weather:updated': (weather: WeatherInfo) => void;
  'emergency:activated': (data: { userId: string; location?: string }) => void;
  
  // Outgoing events
  'join:user-room': (userId: string) => void;
  'leave:user-room': (userId: string) => void;
  'message:send': (message: Omit<Message, 'id' | 'timestamp'>) => void;
  'symptom:log': (symptom: Omit<SymptomLog, 'id' | 'timestamp'>) => void;
  'medication:log': (log: Omit<MedicationLog, 'id' | 'timestamp'>) => void;
  'mood:log': (mood: Omit<MoodLog, 'id' | 'timestamp'>) => void;
  'vitals:log': (vitals: Omit<VitalSigns, 'id' | 'timestamp'>) => void;
  'emergency:trigger': (data: { location?: string; message?: string }) => void;
}

// Form validation types
export interface FormValidation {
  isValid: boolean;
  errors: Record<string, string>;
}

// Chart data types
export interface ChartDataPoint {
  date: string;
  value: number;
  label?: string;
  category?: string;
}

export interface ChartConfig {
  type: 'line' | 'bar' | 'pie' | 'area';
  dataKey: string;
  color: string;
  showTooltip: boolean;
  showLegend: boolean;
}

// Notification types
export interface Notification {
  id: string;
  userId: string;
  title: string;
  message: string;
  type: 'info' | 'success' | 'warning' | 'error' | 'medication' | 'appointment';
  priority: 'low' | 'medium' | 'high' | 'urgent';
  isRead: boolean;
  actionUrl?: string;
  actionLabel?: string;
  createdAt: string;
  expiresAt?: string;
}

// Error types
export interface AppError {
  code: string;
  message: string;
  details?: any;
  timestamp: string;
  userId?: string;
}

// Loading states
export interface LoadingState {
  isLoading: boolean;
  error?: string | null;
  lastUpdated?: string;
}

// Generic pagination
export interface PaginationParams {
  page: number;
  limit: number;
  sortBy?: string;
  sortOrder?: 'asc' | 'desc';
  filter?: Record<string, any>;
}

// File upload types
export interface FileUpload {
  file: File;
  progress: number;
  status: 'pending' | 'uploading' | 'completed' | 'error';
  url?: string;
  error?: string;
}