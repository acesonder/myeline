// Health tracking types
export interface SymptomLog {
  id: string;
  userId: string;
  timestamp: string;
  type: SymptomType;
  severity: number; // 1-10 scale
  location?: string;
  description?: string;
  triggers?: string[];
  tags?: string[];
  mood?: MoodLevel;
}

export type SymptomType = 
  | 'pain'
  | 'nausea' 
  | 'fatigue'
  | 'anxiety'
  | 'neuropathy'
  | 'appetite-loss'
  | 'sleep-issues'
  | 'breathing'
  | 'digestive'
  | 'skin'
  | 'other';

export type MoodLevel = 
  | 'great'
  | 'good' 
  | 'okay'
  | 'struggling'
  | 'difficult';

export interface MoodLog {
  id: string;
  userId: string;
  timestamp: string;
  mood: MoodLevel;
  energyLevel: number; // 1-10
  stressLevel: number; // 1-10
  notes?: string;
  activities?: string[];
}

export interface VitalSigns {
  id: string;
  userId: string;
  timestamp: string;
  temperature?: number;
  bloodPressure?: {
    systolic: number;
    diastolic: number;
  };
  heartRate?: number;
  oxygenSaturation?: number;
  weight?: number;
  bloodSugar?: number;
}

export interface HydrationLog {
  id: string;
  userId: string;
  timestamp: string;
  amount: number; // in mL
  type: 'water' | 'juice' | 'tea' | 'coffee' | 'other';
  goal: number; // daily goal in mL
}

export interface PainMap {
  id: string;
  userId: string;
  timestamp: string;
  bodyParts: PainPoint[];
}

export interface PainPoint {
  location: {
    x: number; // percentage
    y: number; // percentage
  };
  intensity: number; // 1-10
  type: 'sharp' | 'dull' | 'burning' | 'tingling' | 'cramping';
  bodyPart: string;
}