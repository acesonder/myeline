import { create } from 'zustand';
import { SymptomLog, MoodLog, VitalSigns, HydrationLog } from '@/types';
import { apiClient } from '@/services/api';

interface HealthState {
  symptoms: SymptomLog[];
  moods: MoodLog[];
  vitals: VitalSigns[];
  hydration: HydrationLog[];
  isLoading: boolean;
  error: string | null;
  
  // Actions
  loadSymptoms: (params?: any) => Promise<void>;
  logSymptom: (symptomData: Partial<SymptomLog>) => Promise<void>;
  loadMoods: (params?: any) => Promise<void>;
  logMood: (moodData: Partial<MoodLog>) => Promise<void>;
  loadVitals: (params?: any) => Promise<void>;
  logVitals: (vitalsData: Partial<VitalSigns>) => Promise<void>;
  loadHydration: (params?: any) => Promise<void>;
  logHydration: (hydrationData: Partial<HydrationLog>) => Promise<void>;
  clearError: () => void;
}

export const useHealthStore = create<HealthState>((set, get) => ({
  symptoms: [],
  moods: [],
  vitals: [],
  hydration: [],
  isLoading: false,
  error: null,
  
  loadSymptoms: async (params?: any) => {
    set({ isLoading: true, error: null });
    
    try {
      const symptoms = await apiClient.getSymptomLogs(params);
      set({ symptoms, isLoading: false });
    } catch (error: any) {
      console.error('Failed to load symptoms:', error);
      set({
        isLoading: false,
        error: 'Failed to load symptoms',
      });
    }
  },
  
  logSymptom: async (symptomData: Partial<SymptomLog>) => {
    try {
      const newSymptom = await apiClient.logSymptom({
        ...symptomData,
        timestamp: new Date().toISOString(),
      });
      
      const { symptoms } = get();
      set({
        symptoms: [newSymptom, ...symptoms],
      });
      
      // Emit WebSocket event for real-time updates
      // socketService.logSymptom(newSymptom);
      
    } catch (error: any) {
      console.error('Failed to log symptom:', error);
      set({ error: 'Failed to log symptom' });
      throw error;
    }
  },
  
  loadMoods: async (params?: any) => {
    set({ isLoading: true, error: null });
    
    try {
      const moods = await apiClient.getMoodLogs(params);
      set({ moods, isLoading: false });
    } catch (error: any) {
      console.error('Failed to load moods:', error);
      set({
        isLoading: false,
        error: 'Failed to load moods',
      });
    }
  },
  
  logMood: async (moodData: Partial<MoodLog>) => {
    try {
      const newMood = await apiClient.logMood({
        ...moodData,
        timestamp: new Date().toISOString(),
      });
      
      const { moods } = get();
      set({
        moods: [newMood, ...moods],
      });
      
      // Emit WebSocket event for real-time updates
      // socketService.logMood(newMood);
      
    } catch (error: any) {
      console.error('Failed to log mood:', error);
      set({ error: 'Failed to log mood' });
      throw error;
    }
  },
  
  loadVitals: async (params?: any) => {
    set({ isLoading: true, error: null });
    
    try {
      const vitals = await apiClient.getVitalSigns(params);
      set({ vitals, isLoading: false });
    } catch (error: any) {
      console.error('Failed to load vitals:', error);
      set({
        isLoading: false,
        error: 'Failed to load vitals',
      });
    }
  },
  
  logVitals: async (vitalsData: Partial<VitalSigns>) => {
    try {
      const newVitals = await apiClient.logVitals({
        ...vitalsData,
        timestamp: new Date().toISOString(),
      });
      
      const { vitals } = get();
      set({
        vitals: [newVitals, ...vitals],
      });
      
      // Emit WebSocket event for real-time updates
      // socketService.logVitals(newVitals);
      
    } catch (error: any) {
      console.error('Failed to log vitals:', error);
      set({ error: 'Failed to log vitals' });
      throw error;
    }
  },
  
  loadHydration: async (params?: any) => {
    set({ isLoading: true, error: null });
    
    try {
      const hydration = await apiClient.getHydrationLogs(params);
      set({ hydration, isLoading: false });
    } catch (error: any) {
      console.error('Failed to load hydration:', error);
      set({
        isLoading: false,
        error: 'Failed to load hydration',
      });
    }
  },
  
  logHydration: async (hydrationData: Partial<HydrationLog>) => {
    try {
      const newHydration = await apiClient.logHydration({
        ...hydrationData,
        timestamp: new Date().toISOString(),
      });
      
      const { hydration } = get();
      set({
        hydration: [newHydration, ...hydration],
      });
      
    } catch (error: any) {
      console.error('Failed to log hydration:', error);
      set({ error: 'Failed to log hydration' });
      throw error;
    }
  },
  
  clearError: () => set({ error: null }),
}));