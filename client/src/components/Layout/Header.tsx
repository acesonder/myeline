import React, { useState } from 'react';
import { Bell, Menu, HelpCircle, Palette, Phone } from 'lucide-react';
import { User } from '@/types';
import { cn } from '@/utils/helpers';

interface HeaderProps {
  onMenuClick: () => void;
  user: User;
}

const Header: React.FC<HeaderProps> = ({ onMenuClick, user }) => {
  const [showNotifications, setShowNotifications] = useState(false);
  const [showThemeSelector, setShowThemeSelector] = useState(false);

  const handleThemeChange = (theme: 'light' | 'dark' | 'high-contrast') => {
    document.documentElement.setAttribute('data-theme', theme);
    // TODO: Update user preferences via API
    setShowThemeSelector(false);
  };

  const handleEmergency = () => {
    // TODO: Implement emergency contact functionality
    window.location.href = '/emergency';
  };

  return (
    <div className="sticky top-0 z-30 bg-white shadow-sm border-b border-gray-200">
      <div className="px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between h-16">
          {/* Left side */}
          <div className="flex items-center">
            {/* Mobile menu button */}
            <button
              type="button"
              className="lg:hidden -ml-2 mr-2 h-10 w-10 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500"
              onClick={onMenuClick}
            >
              <span className="sr-only">Open sidebar</span>
              <Menu className="h-6 w-6" />
            </button>

            {/* Page title or greeting */}
            <div className="hidden lg:block">
              <h1 className="text-lg font-semibold text-gray-900">
                Good {getTimeOfDay()}, {user.firstName}!
              </h1>
              <p className="text-sm text-gray-500">
                {new Date().toLocaleDateString('en-CA', { 
                  weekday: 'long', 
                  year: 'numeric', 
                  month: 'long', 
                  day: 'numeric' 
                })}
              </p>
            </div>
          </div>

          {/* Right side */}
          <div className="flex items-center space-x-2">
            {/* Emergency button - always visible */}
            <button
              onClick={handleEmergency}
              className="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
              title="Emergency Contacts"
            >
              <Phone className="h-5 w-5" />
            </button>

            {/* Theme selector */}
            <div className="relative">
              <button
                onClick={() => setShowThemeSelector(!showThemeSelector)}
                className="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-lg transition-colors"
                title="Change Theme"
              >
                <Palette className="h-5 w-5" />
              </button>

              {showThemeSelector && (
                <div className="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                  <div className="py-1" role="menu">
                    <button
                      onClick={() => handleThemeChange('light')}
                      className="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                      Light Theme
                    </button>
                    <button
                      onClick={() => handleThemeChange('dark')}
                      className="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                      Dark Theme
                    </button>
                    <button
                      onClick={() => handleThemeChange('high-contrast')}
                      className="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                      High Contrast
                    </button>
                  </div>
                </div>
              )}
            </div>

            {/* Help button */}
            <button
              className="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-lg transition-colors"
              title="Help & Assistant"
            >
              <HelpCircle className="h-5 w-5" />
            </button>

            {/* Notifications */}
            <div className="relative">
              <button
                onClick={() => setShowNotifications(!showNotifications)}
                className="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-lg transition-colors relative"
                title="Notifications"
              >
                <Bell className="h-5 w-5" />
                {/* Notification badge */}
                <span className="absolute top-1 right-1 block h-2 w-2 rounded-full bg-red-400"></span>
              </button>

              {showNotifications && (
                <div className="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                  <div className="p-4">
                    <h3 className="text-sm font-medium text-gray-900 mb-3">Notifications</h3>
                    <div className="space-y-3">
                      <div className="p-3 bg-blue-50 rounded-lg">
                        <p className="text-sm text-blue-800">
                          Time for your afternoon medication
                        </p>
                        <p className="text-xs text-blue-600 mt-1">2 minutes ago</p>
                      </div>
                      <div className="p-3 bg-green-50 rounded-lg">
                        <p className="text-sm text-green-800">
                          Great job staying hydrated today!
                        </p>
                        <p className="text-xs text-green-600 mt-1">1 hour ago</p>
                      </div>
                      <div className="p-3 bg-purple-50 rounded-lg">
                        <p className="text-sm text-purple-800">
                          New message from your caregiver
                        </p>
                        <p className="text-xs text-purple-600 mt-1">2 hours ago</p>
                      </div>
                    </div>
                    <div className="mt-4 pt-3 border-t border-gray-200">
                      <button className="text-sm text-primary-600 hover:text-primary-500">
                        View all notifications
                      </button>
                    </div>
                  </div>
                </div>
              )}
            </div>

            {/* User avatar */}
            <div className="flex items-center">
              <div className="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center">
                <span className="text-sm font-medium text-primary-700">
                  {user.firstName?.charAt(0)}{user.lastName?.charAt(0)}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Click outside handlers */}
      {(showNotifications || showThemeSelector) && (
        <div 
          className="fixed inset-0 z-40"
          onClick={() => {
            setShowNotifications(false);
            setShowThemeSelector(false);
          }}
        />
      )}
    </div>
  );
};

function getTimeOfDay(): string {
  const hour = new Date().getHours();
  if (hour < 12) return 'morning';
  if (hour < 17) return 'afternoon';
  return 'evening';
}

export default Header;