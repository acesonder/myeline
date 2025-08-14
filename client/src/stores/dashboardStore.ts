import { create } from 'zustand';
import { DashboardLayout, WidgetLayout, CareCard, WidgetType } from '@/types';
import { apiClient } from '@/services/api';

interface DashboardState {
  layout: WidgetLayout[];
  careCard: CareCard | null;
  isLoading: boolean;
  error: string | null;
  
  // Actions
  loadDashboard: () => Promise<void>;
  updateLayout: (layout: WidgetLayout[]) => Promise<void>;
  updateWidget: (widgetId: string, settings: any) => void;
  toggleWidget: (widgetId: string, isVisible: boolean) => void;
  addWidget: (type: WidgetType, position?: any) => void;
  removeWidget: (widgetId: string) => void;
  loadCareCard: () => Promise<void>;
  clearError: () => void;
}

const defaultWidgets: WidgetLayout[] = [
  {
    id: 'care-card',
    type: 'care-card',
    position: { x: 0, y: 0, w: 12, h: 6 },
    isVisible: true,
    settings: { title: "Today's Care Card" },
  },
  {
    id: 'symptom-log',
    type: 'symptom-log',
    position: { x: 0, y: 6, w: 6, h: 8 },
    isVisible: true,
    settings: { title: 'Log Something', showQuickButtons: true },
  },
  {
    id: 'medication-list',
    type: 'medication-list',
    position: { x: 6, y: 6, w: 6, h: 8 },
    isVisible: true,
    settings: { title: 'Medications', showAdherence: true },
  },
  {
    id: 'weather',
    type: 'weather',
    position: { x: 0, y: 14, w: 4, h: 6 },
    isVisible: true,
    settings: { title: 'Weather', location: 'Melfort, SK' },
  },
  {
    id: 'photo-frame',
    type: 'photo-frame',
    position: { x: 4, y: 14, w: 4, h: 6 },
    isVisible: true,
    settings: { title: 'Family Photos', rotationInterval: 10 },
  },
  {
    id: 'daily-quote',
    type: 'daily-quote',
    position: { x: 8, y: 14, w: 4, h: 6 },
    isVisible: true,
    settings: { title: 'Daily Inspiration' },
  },
  {
    id: 'hydration-garden',
    type: 'hydration-garden',
    position: { x: 0, y: 20, w: 6, h: 8 },
    isVisible: true,
    settings: { title: 'Hydration Garden', goal: 2000 },
  },
  {
    id: 'comfort-dashboard',
    type: 'comfort-dashboard',
    position: { x: 6, y: 20, w: 6, h: 8 },
    isVisible: true,
    settings: { title: 'Comfort Zone' },
  },
];

export const useDashboardStore = create<DashboardState>((set, get) => ({
  layout: defaultWidgets,
  careCard: null,
  isLoading: false,
  error: null,
  
  loadDashboard: async () => {
    set({ isLoading: true, error: null });
    
    try {
      const dashboardData = await apiClient.getDashboardLayout();
      
      set({
        layout: dashboardData.layout || defaultWidgets,
        isLoading: false,
      });
    } catch (error: any) {
      console.error('Failed to load dashboard:', error);
      // Use default layout if loading fails
      set({
        layout: defaultWidgets,
        isLoading: false,
        error: 'Failed to load dashboard layout',
      });
    }
  },
  
  updateLayout: async (layout: WidgetLayout[]) => {
    set({ isLoading: true });
    
    try {
      await apiClient.updateDashboardLayout({ layout });
      set({ layout, isLoading: false });
    } catch (error: any) {
      console.error('Failed to update layout:', error);
      set({
        isLoading: false,
        error: 'Failed to save layout changes',
      });
    }
  },
  
  updateWidget: (widgetId: string, settings: any) => {
    const { layout } = get();
    const updatedLayout = layout.map(widget =>
      widget.id === widgetId
        ? { ...widget, settings: { ...widget.settings, ...settings } }
        : widget
    );
    
    set({ layout: updatedLayout });
    
    // Save to backend
    get().updateLayout(updatedLayout);
  },
  
  toggleWidget: (widgetId: string, isVisible: boolean) => {
    const { layout } = get();
    const updatedLayout = layout.map(widget =>
      widget.id === widgetId
        ? { ...widget, isVisible }
        : widget
    );
    
    set({ layout: updatedLayout });
    get().updateLayout(updatedLayout);
  },
  
  addWidget: (type: WidgetType, position = { x: 0, y: 0, w: 6, h: 6 }) => {
    const { layout } = get();
    const newWidget: WidgetLayout = {
      id: `${type}-${Date.now()}`,
      type,
      position,
      isVisible: true,
      settings: { title: type.charAt(0).toUpperCase() + type.slice(1).replace('-', ' ') },
    };
    
    const updatedLayout = [...layout, newWidget];
    set({ layout: updatedLayout });
    get().updateLayout(updatedLayout);
  },
  
  removeWidget: (widgetId: string) => {
    const { layout } = get();
    const updatedLayout = layout.filter(widget => widget.id !== widgetId);
    
    set({ layout: updatedLayout });
    get().updateLayout(updatedLayout);
  },
  
  loadCareCard: async () => {
    try {
      const careCard = await apiClient.getCareCard();
      set({ careCard });
    } catch (error: any) {
      console.error('Failed to load care card:', error);
      set({ error: 'Failed to load care card' });
    }
  },
  
  clearError: () => set({ error: null }),
}));