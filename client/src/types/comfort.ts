// Comfort and wellness types
export interface ComfortActivity {
  id: string;
  name: string;
  type: ComfortActivityType;
  description: string;
  duration: number; // in minutes
  instructions?: string[];
  media?: {
    audio?: string;
    video?: string;
    images?: string[];
  };
  tags: string[];
  difficulty: 'easy' | 'medium' | 'challenging';
  isPersonalized: boolean;
}

export type ComfortActivityType = 
  | 'breathing'
  | 'meditation'
  | 'music'
  | 'puzzle'
  | 'reading'
  | 'art'
  | 'nature-sounds'
  | 'guided-imagery'
  | 'light-exercise'
  | 'journaling';

export interface MusicPlaylist {
  id: string;
  name: string;
  userId?: string; // null for default playlists
  tracks: MusicTrack[];
  genre: string;
  mood: string[];
  isPersonalized: boolean;
  createdAt: string;
}

export interface MusicTrack {
  id: string;
  title: string;
  artist: string;
  duration: number;
  url: string;
  genre: string;
  bpm?: number;
}

export interface PhotoFrame {
  id: string;
  userId: string;
  photos: Photo[];
  currentIndex: number;
  rotationInterval: number; // in seconds
  isActive: boolean;
}

export interface Photo {
  id: string;
  url: string;
  caption?: string;
  uploadedBy: string;
  uploadedAt: string;
  tags?: string[];
}

export interface DailyQuote {
  id: string;
  text: string;
  author?: string;
  category: 'inspiration' | 'humor' | 'wisdom' | 'comfort';
  date: string;
}

export interface WeatherInfo {
  location: string;
  current: {
    temperature: number;
    condition: string;
    humidity: number;
    windSpeed: number;
    icon: string;
  };
  forecast: WeatherForecast[];
  alerts?: WeatherAlert[];
  safetyTips?: string[];
}

export interface WeatherForecast {
  date: string;
  high: number;
  low: number;
  condition: string;
  icon: string;
  precipitation: number;
}

export interface WeatherAlert {
  id: string;
  type: 'severe' | 'moderate' | 'info';
  title: string;
  description: string;
  startTime: string;
  endTime: string;
}