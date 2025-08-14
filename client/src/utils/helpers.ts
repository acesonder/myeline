import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

/**
 * Utility function to merge Tailwind CSS classes
 */
export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

/**
 * Format date for display
 */
export function formatDate(date: string | Date, format: 'short' | 'long' | 'time' | 'datetime' = 'short'): string {
  const d = new Date(date);
  
  switch (format) {
    case 'short':
      return d.toLocaleDateString('en-CA', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
      });
    case 'long':
      return d.toLocaleDateString('en-CA', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        weekday: 'long'
      });
    case 'time':
      return d.toLocaleTimeString('en-CA', { 
        hour: '2-digit', 
        minute: '2-digit' 
      });
    case 'datetime':
      return d.toLocaleString('en-CA', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit', 
        minute: '2-digit' 
      });
    default:
      return d.toISOString();
  }
}

/**
 * Format time for medication reminders
 */
export function formatTime(timeString: string): string {
  const [hours, minutes] = timeString.split(':');
  const hour24 = parseInt(hours);
  const hour12 = hour24 === 0 ? 12 : hour24 > 12 ? hour24 - 12 : hour24;
  const ampm = hour24 >= 12 ? 'PM' : 'AM';
  return `${hour12}:${minutes} ${ampm}`;
}

/**
 * Calculate time until next medication
 */
export function getTimeUntilNext(timeString: string): string {
  const now = new Date();
  const [hours, minutes] = timeString.split(':').map(Number);
  
  const medicationTime = new Date();
  medicationTime.setHours(hours, minutes, 0, 0);
  
  // If medication time has passed today, set it for tomorrow
  if (medicationTime <= now) {
    medicationTime.setDate(medicationTime.getDate() + 1);
  }
  
  const timeDiff = medicationTime.getTime() - now.getTime();
  const hoursDiff = Math.floor(timeDiff / (1000 * 60 * 60));
  const minutesDiff = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
  
  if (hoursDiff === 0) {
    return `${minutesDiff}m`;
  } else if (hoursDiff < 24) {
    return `${hoursDiff}h ${minutesDiff}m`;
  } else {
    const days = Math.floor(hoursDiff / 24);
    const remainingHours = hoursDiff % 24;
    return `${days}d ${remainingHours}h`;
  }
}

/**
 * Validate email format
 */
export function isValidEmail(email: string): boolean {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

/**
 * Validate phone number (Canadian format)
 */
export function isValidPhone(phone: string): boolean {
  const phoneRegex = /^(\+1\s?)?(\(?[0-9]{3}\)?[\s.-]?)?[0-9]{3}[\s.-]?[0-9]{4}$/;
  return phoneRegex.test(phone);
}

/**
 * Generate random ID
 */
export function generateId(): string {
  return Math.random().toString(36).substring(2) + Date.now().toString(36);
}

/**
 * Debounce function calls
 */
export function debounce<T extends (...args: any[]) => any>(
  func: T,
  wait: number
): (...args: Parameters<T>) => void {
  let timeout: NodeJS.Timeout;
  return (...args: Parameters<T>) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => func(...args), wait);
  };
}

/**
 * Format file size
 */
export function formatFileSize(bytes: number): string {
  if (bytes === 0) return '0 Bytes';
  
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Get pain level color class
 */
export function getPainLevelClass(level: number): string {
  return `pain-level-${Math.floor(level)}`;
}

/**
 * Get mood color class
 */
export function getMoodClass(mood: string): string {
  return `mood-${mood}`;
}

/**
 * Calculate medication adherence percentage
 */
export function calculateAdherence(taken: number, total: number): number {
  if (total === 0) return 0;
  return Math.round((taken / total) * 100);
}

/**
 * Get adherence color based on percentage
 */
export function getAdherenceColor(percentage: number): string {
  if (percentage >= 90) return 'text-green-600';
  if (percentage >= 70) return 'text-yellow-600';
  return 'text-red-600';
}

/**
 * Format hydration progress
 */
export function formatHydration(current: number, goal: number): string {
  const percentage = goal > 0 ? Math.round((current / goal) * 100) : 0;
  return `${current}mL / ${goal}mL (${percentage}%)`;
}

/**
 * Get relative time string
 */
export function getRelativeTime(date: string | Date): string {
  const now = new Date();
  const past = new Date(date);
  const diffInMinutes = Math.floor((now.getTime() - past.getTime()) / (1000 * 60));
  
  if (diffInMinutes < 1) return 'Just now';
  if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
  
  const diffInHours = Math.floor(diffInMinutes / 60);
  if (diffInHours < 24) return `${diffInHours}h ago`;
  
  const diffInDays = Math.floor(diffInHours / 24);
  if (diffInDays < 7) return `${diffInDays}d ago`;
  
  return formatDate(past, 'short');
}

/**
 * Sanitize HTML content
 */
export function sanitizeHTML(html: string): string {
  const div = document.createElement('div');
  div.textContent = html;
  return div.innerHTML;
}

/**
 * Copy text to clipboard
 */
export async function copyToClipboard(text: string): Promise<boolean> {
  try {
    await navigator.clipboard.writeText(text);
    return true;
  } catch (err) {
    // Fallback for older browsers
    const textArea = document.createElement('textarea');
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    const successful = document.execCommand('copy');
    document.body.removeChild(textArea);
    return successful;
  }
}

/**
 * Deep clone object
 */
export function deepClone<T>(obj: T): T {
  return JSON.parse(JSON.stringify(obj));
}

/**
 * Check if user is online
 */
export function isOnline(): boolean {
  return navigator.onLine;
}

/**
 * Local storage helpers with error handling
 */
export const storage = {
  get: <T>(key: string): T | null => {
    try {
      const item = localStorage.getItem(key);
      return item ? JSON.parse(item) : null;
    } catch {
      return null;
    }
  },
  
  set: <T>(key: string, value: T): boolean => {
    try {
      localStorage.setItem(key, JSON.stringify(value));
      return true;
    } catch {
      return false;
    }
  },
  
  remove: (key: string): boolean => {
    try {
      localStorage.removeItem(key);
      return true;
    } catch {
      return false;
    }
  },
  
  clear: (): boolean => {
    try {
      localStorage.clear();
      return true;
    } catch {
      return false;
    }
  }
};