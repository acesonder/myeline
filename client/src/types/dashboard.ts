// Dashboard and widget types
export interface DashboardLayout {
  userId: string;
  layout: WidgetLayout[];
  theme: string;
  lastModified: string;
}

export interface WidgetLayout {
  id: string;
  type: WidgetType;
  position: {
    x: number;
    y: number;
    w: number;
    h: number;
  };
  isVisible: boolean;
  settings: WidgetSettings;
}

export type WidgetType = 
  | 'care-card'
  | 'symptom-log'
  | 'medication-list'
  | 'mood-tracker'
  | 'pain-map'
  | 'hydration-garden'
  | 'weather'
  | 'photo-frame'
  | 'daily-quote'
  | 'appointments'
  | 'comfort-dashboard'
  | 'vitals-chart'
  | 'goals-tracker'
  | 'messaging'
  | 'emergency-contacts'
  | 'ai-assistant';

export interface WidgetSettings {
  title?: string;
  color?: string;
  refreshInterval?: number;
  showHeader?: boolean;
  compactMode?: boolean;
  [key: string]: any;
}

export interface CareCard {
  id: string;
  userId: string;
  date: string;
  summary: {
    nextMedication?: {
      name: string;
      time: string;
    };
    nextAppointment?: {
      title: string;
      time: string;
    };
    unreadMessages: number;
    weatherAlert?: string;
    moodToday?: MoodLevel;
    painLevel?: number;
  };
  tasks: CareTask[];
}

export interface CareTask {
  id: string;
  title: string;
  type: 'medication' | 'symptom-check' | 'appointment' | 'exercise' | 'meal';
  priority: 'low' | 'medium' | 'high';
  dueTime?: string;
  isCompleted: boolean;
  notes?: string;
}