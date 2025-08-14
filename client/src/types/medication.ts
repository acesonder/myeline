// Medication types
export interface Medication {
  id: string;
  userId: string;
  name: string;
  genericName?: string;
  dosage: string;
  frequency: MedicationFrequency;
  instructions: string;
  prescribedBy: string;
  startDate: string;
  endDate?: string;
  isActive: boolean;
  isPRN: boolean; // "as needed"
  sideEffects?: string[];
  purpose: string;
  reminders: MedicationReminder[];
}

export interface MedicationFrequency {
  type: 'daily' | 'weekly' | 'monthly' | 'as-needed';
  times?: string[]; // Time of day for daily meds
  days?: number[]; // Days of week for weekly (0-6)
  interval?: number; // Every X days/weeks
}

export interface MedicationReminder {
  id: string;
  medicationId: string;
  time: string;
  isActive: boolean;
  soundEnabled: boolean;
  message?: string;
}

export interface MedicationLog {
  id: string;
  medicationId: string;
  userId: string;
  timestamp: string;
  action: 'taken' | 'skipped' | 'delayed';
  notes?: string;
  sideEffects?: string[];
  effectiveness?: number; // 1-10 scale
}

export interface MedicationAdherence {
  medicationId: string;
  period: 'week' | 'month' | 'quarter';
  adherenceRate: number; // 0-100 percentage
  totalDoses: number;
  takenDoses: number;
  skippedDoses: number;
  delayedDoses: number;
}