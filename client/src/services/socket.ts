import { io, Socket } from 'socket.io-client';
import { SocketEvents } from '@/types';

class SocketService {
  private socket: Socket | null = null;
  private isConnected = false;
  private reconnectAttempts = 0;
  private maxReconnectAttempts = 5;
  private reconnectDelay = 1000;
  
  connect(userId: string) {
    if (this.socket?.connected) {
      return;
    }
    
    const serverUrl = import.meta.env.VITE_WS_URL || 'http://localhost:3001';
    const token = localStorage.getItem('auth_token');
    
    this.socket = io(serverUrl, {
      auth: {
        token,
        userId,
      },
      transports: ['websocket', 'polling'],
      timeout: 20000,
    });
    
    this.setupEventListeners();
  }
  
  private setupEventListeners() {
    if (!this.socket) return;
    
    this.socket.on('connect', () => {
      console.log('WebSocket connected');
      this.isConnected = true;
      this.reconnectAttempts = 0;
      
      // Join user-specific room
      const userId = localStorage.getItem('user_id');
      if (userId) {
        this.emit('join:user-room', userId);
      }
    });
    
    this.socket.on('disconnect', (reason) => {
      console.log('WebSocket disconnected:', reason);
      this.isConnected = false;
      
      // Auto-reconnect if not intentional
      if (reason === 'io server disconnect') {
        this.socket?.connect();
      }
    });
    
    this.socket.on('connect_error', (error) => {
      console.error('WebSocket connection error:', error);
      this.handleReconnect();
    });
    
    this.socket.on('error', (error) => {
      console.error('WebSocket error:', error);
    });
  }
  
  private handleReconnect() {
    if (this.reconnectAttempts >= this.maxReconnectAttempts) {
      console.error('Max reconnection attempts reached');
      return;
    }
    
    this.reconnectAttempts++;
    const delay = this.reconnectDelay * Math.pow(2, this.reconnectAttempts - 1);
    
    setTimeout(() => {
      console.log(`Attempting to reconnect... (${this.reconnectAttempts}/${this.maxReconnectAttempts})`);
      this.socket?.connect();
    }, delay);
  }
  
  disconnect() {
    if (this.socket) {
      this.socket.disconnect();
      this.socket = null;
      this.isConnected = false;
    }
  }
  
  // Event emission
  emit<K extends keyof SocketEvents>(event: K, data: Parameters<SocketEvents[K]>[0]) {
    if (this.socket?.connected) {
      this.socket.emit(event, data);
    } else {
      console.warn('Socket not connected, cannot emit event:', event);
    }
  }
  
  // Event listening
  on<K extends keyof SocketEvents>(event: K, callback: SocketEvents[K]) {
    if (this.socket) {
      this.socket.on(event as string, callback);
    }
  }
  
  off<K extends keyof SocketEvents>(event: K, callback?: SocketEvents[K]) {
    if (this.socket) {
      this.socket.off(event as string, callback);
    }
  }
  
  // Health tracking events
  onSymptomLogged(callback: (symptom: any) => void) {
    this.on('symptom:logged', callback);
  }
  
  onMoodUpdated(callback: (mood: any) => void) {
    this.on('mood:updated', callback);
  }
  
  onVitalsUpdated(callback: (vitals: any) => void) {
    this.on('vitals:updated', callback);
  }
  
  onMedicationTaken(callback: (log: any) => void) {
    this.on('medication:taken', callback);
  }
  
  // Communication events
  onNewMessage(callback: (message: any) => void) {
    this.on('message:new', callback);
  }
  
  onAppointmentReminder(callback: (appointment: any) => void) {
    this.on('appointment:reminder', callback);
  }
  
  // Care card updates
  onCareCardUpdated(callback: (careCard: any) => void) {
    this.on('care-card:updated', callback);
  }
  
  // Weather updates
  onWeatherUpdated(callback: (weather: any) => void) {
    this.on('weather:updated', callback);
  }
  
  // Emergency events
  onEmergencyActivated(callback: (data: any) => void) {
    this.on('emergency:activated', callback);
  }
  
  // User presence events
  onUserConnect(callback: (data: { userId: string }) => void) {
    this.on('user:connect', callback);
  }
  
  onUserDisconnect(callback: (data: { userId: string }) => void) {
    this.on('user:disconnect', callback);
  }
  
  // Helper methods for emitting common events
  logSymptom(symptomData: any) {
    this.emit('symptom:log', symptomData);
  }
  
  logMood(moodData: any) {
    this.emit('mood:log', moodData);
  }
  
  logVitals(vitalsData: any) {
    this.emit('vitals:log', vitalsData);
  }
  
  logMedication(medicationData: any) {
    this.emit('medication:log', medicationData);
  }
  
  sendMessage(messageData: any) {
    this.emit('message:send', messageData);
  }
  
  triggerEmergency(emergencyData: any) {
    this.emit('emergency:trigger', emergencyData);
  }
  
  // Getters
  get connected() {
    return this.isConnected && this.socket?.connected;
  }
  
  get connecting() {
    return this.socket?.connecting || false;
  }
}

export const socketService = new SocketService();