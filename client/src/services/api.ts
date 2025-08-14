import axios, { AxiosInstance, AxiosResponse, AxiosError } from 'axios';
import { ApiResponse, AuthResponse, User } from '@/types';

class ApiClient {
  private client: AxiosInstance;
  private baseURL: string;
  
  constructor() {
    this.baseURL = import.meta.env.VITE_API_URL || 'http://localhost:3001';
    
    this.client = axios.create({
      baseURL: `${this.baseURL}/api`,
      timeout: 30000,
      headers: {
        'Content-Type': 'application/json',
      },
    });
    
    this.setupInterceptors();
  }
  
  private setupInterceptors() {
    // Request interceptor to add auth token
    this.client.interceptors.request.use(
      (config) => {
        const token = localStorage.getItem('auth_token');
        if (token) {
          config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
      },
      (error) => Promise.reject(error)
    );
    
    // Response interceptor to handle errors
    this.client.interceptors.response.use(
      (response: AxiosResponse) => response,
      (error: AxiosError) => {
        if (error.response?.status === 401) {
          localStorage.removeItem('auth_token');
          localStorage.removeItem('refresh_token');
          window.location.href = '/login';
        }
        return Promise.reject(error);
      }
    );
  }
  
  // Authentication endpoints
  async login(email: string, password: string): Promise<AuthResponse> {
    const response = await this.client.post<ApiResponse<AuthResponse>>('/auth/login', {
      email,
      password,
    });
    return response.data.data!;
  }
  
  async signup(userData: any): Promise<AuthResponse> {
    const response = await this.client.post<ApiResponse<AuthResponse>>('/auth/signup', userData);
    return response.data.data!;
  }
  
  async logout(): Promise<void> {
    await this.client.post('/auth/logout');
  }
  
  async refreshToken(): Promise<AuthResponse> {
    const refreshToken = localStorage.getItem('refresh_token');
    const response = await this.client.post<ApiResponse<AuthResponse>>('/auth/refresh', {
      refreshToken,
    });
    return response.data.data!;
  }
  
  async getCurrentUser(): Promise<User> {
    const response = await this.client.get<ApiResponse<User>>('/auth/me');
    return response.data.data!;
  }
  
  // User profile endpoints
  async updateProfile(profileData: Partial<User>): Promise<User> {
    const response = await this.client.put<ApiResponse<User>>('/users/profile', profileData);
    return response.data.data!;
  }
  
  async updatePreferences(preferences: any): Promise<User> {
    const response = await this.client.put<ApiResponse<User>>('/users/preferences', preferences);
    return response.data.data!;
  }
  
  // Health tracking endpoints
  async getSymptomLogs(params?: any): Promise<any[]> {
    const response = await this.client.get<ApiResponse<any[]>>('/health/symptoms', { params });
    return response.data.data!;
  }
  
  async logSymptom(symptomData: any): Promise<any> {
    const response = await this.client.post<ApiResponse<any>>('/health/symptoms', symptomData);
    return response.data.data!;
  }
  
  async getMoodLogs(params?: any): Promise<any[]> {
    const response = await this.client.get<ApiResponse<any[]>>('/health/mood', { params });
    return response.data.data!;
  }
  
  async logMood(moodData: any): Promise<any> {
    const response = await this.client.post<ApiResponse<any>>('/health/mood', moodData);
    return response.data.data!;
  }
  
  async getVitalSigns(params?: any): Promise<any[]> {
    const response = await this.client.get<ApiResponse<any[]>>('/health/vitals', { params });
    return response.data.data!;
  }
  
  async logVitals(vitalsData: any): Promise<any> {
    const response = await this.client.post<ApiResponse<any>>('/health/vitals', vitalsData);
    return response.data.data!;
  }
  
  async getHydrationLogs(params?: any): Promise<any[]> {
    const response = await this.client.get<ApiResponse<any[]>>('/health/hydration', { params });
    return response.data.data!;
  }
  
  async logHydration(hydrationData: any): Promise<any> {
    const response = await this.client.post<ApiResponse<any>>('/health/hydration', hydrationData);
    return response.data.data!;
  }
  
  // Medication endpoints
  async getMedications(): Promise<any[]> {
    const response = await this.client.get<ApiResponse<any[]>>('/medications');
    return response.data.data!;
  }
  
  async addMedication(medicationData: any): Promise<any> {
    const response = await this.client.post<ApiResponse<any>>('/medications', medicationData);
    return response.data.data!;
  }
  
  async updateMedication(id: string, medicationData: any): Promise<any> {
    const response = await this.client.put<ApiResponse<any>>(`/medications/${id}`, medicationData);
    return response.data.data!;
  }
  
  async deleteMedication(id: string): Promise<void> {
    await this.client.delete(`/medications/${id}`);
  }
  
  async logMedicationTaken(logData: any): Promise<any> {
    const response = await this.client.post<ApiResponse<any>>('/medications/log', logData);
    return response.data.data!;
  }
  
  async getMedicationLogs(params?: any): Promise<any[]> {
    const response = await this.client.get<ApiResponse<any[]>>('/medications/logs', { params });
    return response.data.data!;
  }
  
  // Appointments endpoints
  async getAppointments(params?: any): Promise<any[]> {
    const response = await this.client.get<ApiResponse<any[]>>('/appointments', { params });
    return response.data.data!;
  }
  
  async createAppointment(appointmentData: any): Promise<any> {
    const response = await this.client.post<ApiResponse<any>>('/appointments', appointmentData);
    return response.data.data!;
  }
  
  async updateAppointment(id: string, appointmentData: any): Promise<any> {
    const response = await this.client.put<ApiResponse<any>>(`/appointments/${id}`, appointmentData);
    return response.data.data!;
  }
  
  async deleteAppointment(id: string): Promise<void> {
    await this.client.delete(`/appointments/${id}`);
  }
  
  // Messaging endpoints
  async getConversations(): Promise<any[]> {
    const response = await this.client.get<ApiResponse<any[]>>('/messages/conversations');
    return response.data.data!;
  }
  
  async getMessages(conversationId: string, params?: any): Promise<any[]> {
    const response = await this.client.get<ApiResponse<any[]>>(`/messages/conversations/${conversationId}`, { params });
    return response.data.data!;
  }
  
  async sendMessage(messageData: any): Promise<any> {
    const response = await this.client.post<ApiResponse<any>>('/messages', messageData);
    return response.data.data!;
  }
  
  async markMessageAsRead(messageId: string): Promise<void> {
    await this.client.put(`/messages/${messageId}/read`);
  }
  
  // Dashboard endpoints
  async getDashboardLayout(): Promise<any> {
    const response = await this.client.get<ApiResponse<any>>('/dashboard/layout');
    return response.data.data!;
  }
  
  async updateDashboardLayout(layoutData: any): Promise<any> {
    const response = await this.client.put<ApiResponse<any>>('/dashboard/layout', layoutData);
    return response.data.data!;
  }
  
  async getCareCard(): Promise<any> {
    const response = await this.client.get<ApiResponse<any>>('/dashboard/care-card');
    return response.data.data!;
  }
  
  // Weather endpoints
  async getWeather(location?: string): Promise<any> {
    const response = await this.client.get<ApiResponse<any>>('/weather', {
      params: { location }
    });
    return response.data.data!;
  }
  
  // Comfort features endpoints
  async getDailyQuote(): Promise<any> {
    const response = await this.client.get<ApiResponse<any>>('/comfort/quote');
    return response.data.data!;
  }
  
  async getPlaylists(): Promise<any[]> {
    const response = await this.client.get<ApiResponse<any[]>>('/comfort/playlists');
    return response.data.data!;
  }
  
  async getComfortActivities(): Promise<any[]> {
    const response = await this.client.get<ApiResponse<any[]>>('/comfort/activities');
    return response.data.data!;
  }
  
  async getPhotos(): Promise<any[]> {
    const response = await this.client.get<ApiResponse<any[]>>('/photos');
    return response.data.data!;
  }
  
  async uploadPhoto(formData: FormData): Promise<any> {
    const response = await this.client.post<ApiResponse<any>>('/photos/upload', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data.data!;
  }
  
  // Emergency endpoints
  async triggerEmergency(emergencyData: any): Promise<void> {
    await this.client.post('/emergency/trigger', emergencyData);
  }
  
  async getEmergencyContacts(): Promise<any[]> {
    const response = await this.client.get<ApiResponse<any[]>>('/emergency/contacts');
    return response.data.data!;
  }
  
  // Notifications endpoints
  async getNotifications(params?: any): Promise<any[]> {
    const response = await this.client.get<ApiResponse<any[]>>('/notifications', { params });
    return response.data.data!;
  }
  
  async markNotificationAsRead(id: string): Promise<void> {
    await this.client.put(`/notifications/${id}/read`);
  }
  
  async markAllNotificationsAsRead(): Promise<void> {
    await this.client.put('/notifications/read-all');
  }
}

export const apiClient = new ApiClient();