import { create } from 'zustand';
import { Medication, MedicationLog } from '@/types';
import { apiClient } from '@/services/api';

interface MedicationState {
  medications: Medication[];
  logs: MedicationLog[];
  isLoading: boolean;
  error: string | null;
  
  // Actions
  loadMedications: () => Promise<void>;
  addMedication: (medicationData: Partial<Medication>) => Promise<void>;
  updateMedication: (id: string, medicationData: Partial<Medication>) => Promise<void>;
  deleteMedication: (id: string) => Promise<void>;
  logMedicationTaken: (medicationId: string, action: 'taken' | 'skipped' | 'delayed', notes?: string) => Promise<void>;
  loadMedicationLogs: (params?: any) => Promise<void>;
  clearError: () => void;
}

export const useMedicationStore = create<MedicationState>((set, get) => ({
  medications: [],
  logs: [],
  isLoading: false,
  error: null,
  
  loadMedications: async () => {
    set({ isLoading: true, error: null });
    
    try {
      const medications = await apiClient.getMedications();
      set({ medications, isLoading: false });
    } catch (error: any) {
      console.error('Failed to load medications:', error);
      set({
        isLoading: false,
        error: 'Failed to load medications',
      });
    }
  },
  
  addMedication: async (medicationData: Partial<Medication>) => {
    set({ isLoading: true, error: null });
    
    try {
      const newMedication = await apiClient.addMedication(medicationData);
      const { medications } = get();
      
      set({
        medications: [...medications, newMedication],
        isLoading: false,
      });
    } catch (error: any) {
      console.error('Failed to add medication:', error);
      set({
        isLoading: false,
        error: 'Failed to add medication',
      });
      throw error;
    }
  },
  
  updateMedication: async (id: string, medicationData: Partial<Medication>) => {
    set({ isLoading: true, error: null });
    
    try {
      const updatedMedication = await apiClient.updateMedication(id, medicationData);
      const { medications } = get();
      
      set({
        medications: medications.map(med => 
          med.id === id ? updatedMedication : med
        ),
        isLoading: false,
      });
    } catch (error: any) {
      console.error('Failed to update medication:', error);
      set({
        isLoading: false,
        error: 'Failed to update medication',
      });
      throw error;
    }
  },
  
  deleteMedication: async (id: string) => {
    set({ isLoading: true, error: null });
    
    try {
      await apiClient.deleteMedication(id);
      const { medications } = get();
      
      set({
        medications: medications.filter(med => med.id !== id),
        isLoading: false,
      });
    } catch (error: any) {
      console.error('Failed to delete medication:', error);
      set({
        isLoading: false,
        error: 'Failed to delete medication',
      });
      throw error;
    }
  },
  
  logMedicationTaken: async (medicationId: string, action: 'taken' | 'skipped' | 'delayed', notes?: string) => {
    try {
      const logData = {
        medicationId,
        action,
        notes,
        timestamp: new Date().toISOString(),
      };
      
      const newLog = await apiClient.logMedicationTaken(logData);
      const { logs } = get();
      
      set({
        logs: [newLog, ...logs],
      });
      
      // Emit WebSocket event for real-time updates
      // socketService.logMedication(newLog);
      
    } catch (error: any) {
      console.error('Failed to log medication:', error);
      set({ error: 'Failed to log medication' });
      throw error;
    }
  },
  
  loadMedicationLogs: async (params?: any) => {
    set({ isLoading: true, error: null });
    
    try {
      const logs = await apiClient.getMedicationLogs(params);
      set({ logs, isLoading: false });
    } catch (error: any) {
      console.error('Failed to load medication logs:', error);
      set({
        isLoading: false,
        error: 'Failed to load medication logs',
      });
    }
  },
  
  clearError: () => set({ error: null }),
}));