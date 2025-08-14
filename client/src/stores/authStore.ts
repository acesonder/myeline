import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import { User, AuthResponse } from '@/types';
import { apiClient } from '@/services/api';
import { socketService } from '@/services/socket';

interface AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  error: string | null;
  
  // Actions
  login: (email: string, password: string) => Promise<void>;
  signup: (userData: any) => Promise<void>;
  logout: () => Promise<void>;
  refreshAuth: () => Promise<void>;
  updateProfile: (profileData: Partial<User>) => Promise<void>;
  updatePreferences: (preferences: any) => Promise<void>;
  clearError: () => void;
  setLoading: (loading: boolean) => void;
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      user: null,
      token: null,
      isAuthenticated: false,
      isLoading: false,
      error: null,
      
      login: async (email: string, password: string) => {
        set({ isLoading: true, error: null });
        
        try {
          const authResponse: AuthResponse = await apiClient.login(email, password);
          
          // Store tokens
          localStorage.setItem('auth_token', authResponse.token);
          localStorage.setItem('refresh_token', authResponse.refreshToken);
          localStorage.setItem('user_id', authResponse.user.id);
          
          set({
            user: authResponse.user,
            token: authResponse.token,
            isAuthenticated: true,
            isLoading: false,
            error: null,
          });
          
          // Connect to WebSocket
          socketService.connect(authResponse.user.id);
          
        } catch (error: any) {
          const errorMessage = error.response?.data?.message || 'Login failed';
          set({
            isLoading: false,
            error: errorMessage,
            isAuthenticated: false,
          });
          throw error;
        }
      },
      
      signup: async (userData: any) => {
        set({ isLoading: true, error: null });
        
        try {
          const authResponse: AuthResponse = await apiClient.signup(userData);
          
          // Store tokens
          localStorage.setItem('auth_token', authResponse.token);
          localStorage.setItem('refresh_token', authResponse.refreshToken);
          localStorage.setItem('user_id', authResponse.user.id);
          
          set({
            user: authResponse.user,
            token: authResponse.token,
            isAuthenticated: true,
            isLoading: false,
            error: null,
          });
          
          // Connect to WebSocket
          socketService.connect(authResponse.user.id);
          
        } catch (error: any) {
          const errorMessage = error.response?.data?.message || 'Signup failed';
          set({
            isLoading: false,
            error: errorMessage,
            isAuthenticated: false,
          });
          throw error;
        }
      },
      
      logout: async () => {
        set({ isLoading: true });
        
        try {
          await apiClient.logout();
        } catch (error) {
          console.error('Logout error:', error);
        } finally {
          // Clear local storage
          localStorage.removeItem('auth_token');
          localStorage.removeItem('refresh_token');
          localStorage.removeItem('user_id');
          
          // Disconnect WebSocket
          socketService.disconnect();
          
          set({
            user: null,
            token: null,
            isAuthenticated: false,
            isLoading: false,
            error: null,
          });
        }
      },
      
      refreshAuth: async () => {
        const refreshToken = localStorage.getItem('refresh_token');
        
        if (!refreshToken) {
          set({
            user: null,
            token: null,
            isAuthenticated: false,
          });
          return;
        }
        
        try {
          const authResponse: AuthResponse = await apiClient.refreshToken();
          
          localStorage.setItem('auth_token', authResponse.token);
          localStorage.setItem('refresh_token', authResponse.refreshToken);
          
          set({
            user: authResponse.user,
            token: authResponse.token,
            isAuthenticated: true,
          });
          
          // Reconnect WebSocket if needed
          if (!socketService.connected) {
            socketService.connect(authResponse.user.id);
          }
          
        } catch (error) {
          console.error('Token refresh failed:', error);
          get().logout();
        }
      },
      
      updateProfile: async (profileData: Partial<User>) => {
        set({ isLoading: true, error: null });
        
        try {
          const updatedUser = await apiClient.updateProfile(profileData);
          set({
            user: updatedUser,
            isLoading: false,
          });
        } catch (error: any) {
          const errorMessage = error.response?.data?.message || 'Profile update failed';
          set({
            isLoading: false,
            error: errorMessage,
          });
          throw error;
        }
      },
      
      updatePreferences: async (preferences: any) => {
        set({ isLoading: true, error: null });
        
        try {
          const updatedUser = await apiClient.updatePreferences(preferences);
          set({
            user: updatedUser,
            isLoading: false,
          });
        } catch (error: any) {
          const errorMessage = error.response?.data?.message || 'Preferences update failed';
          set({
            isLoading: false,
            error: errorMessage,
          });
          throw error;
        }
      },
      
      clearError: () => set({ error: null }),
      setLoading: (loading: boolean) => set({ isLoading: loading }),
    }),
    {
      name: 'myeline-auth',
      partialize: (state) => ({
        user: state.user,
        token: state.token,
        isAuthenticated: state.isAuthenticated,
      }),
    }
  )
);