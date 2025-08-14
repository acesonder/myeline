// Core user types
export interface User {
  id: string;
  email: string;
  username: string;
  firstName: string;
  lastName: string;
  role: 'patient' | 'caregiver';
  createdAt: string;
  updatedAt: string;
  profile: UserProfile;
}

export interface UserProfile {
  dateOfBirth?: string;
  phone?: string;
  address?: Address;
  emergencyContact?: EmergencyContact;
  medicalInfo?: MedicalInfo;
  preferences: UserPreferences;
  caregiverAccess?: CaregiverAccess;
}

export interface Address {
  street: string;
  city: string;
  province: string;
  postalCode: string;
  country: string;
}

export interface EmergencyContact {
  name: string;
  relationship: string;
  phone: string;
  email?: string;
}

export interface MedicalInfo {
  primaryDiagnosis: string;
  diagnosisDate: string;
  currentStage?: string;
  allergies: string[];
  primaryOncologist?: string;
  hospitalSystem?: string;
}

export interface UserPreferences {
  theme: 'light' | 'dark' | 'high-contrast';
  fontSize: 'small' | 'medium' | 'large';
  fontFamily: 'default' | 'dyslexia';
  language: string;
  timezone: string;
  notifications: NotificationPreferences;
  privacy: PrivacySettings;
}

export interface NotificationPreferences {
  email: boolean;
  sms: boolean;
  push: boolean;
  medicationReminders: boolean;
  appointmentReminders: boolean;
  caregiverAlerts: boolean;
  dailyCheckins: boolean;
}

export interface PrivacySettings {
  profileVisibility: 'private' | 'caregivers' | 'family';
  dataSharing: boolean;
  analytics: boolean;
}

export interface CaregiverAccess {
  level: 'low' | 'medium' | 'high';
  permissions: {
    viewSymptoms: boolean;
    viewMedications: boolean;
    viewAppointments: boolean;
    viewMood: boolean;
    viewVitals: boolean;
    viewPhotos: boolean;
    receiveAlerts: boolean;
    manageCarePlan: boolean;
  };
}