import React from 'react';
import { NavLink, useNavigate } from 'react-router-dom';
import { useAuthStore } from '@/stores/authStore';
import { cn } from '@/utils/helpers';
import {
  Home,
  Heart,
  Pill,
  Activity,
  Calendar,
  MessageCircle,
  Music,
  Phone,
  User,
  Settings,
  LogOut,
  X,
  Shield
} from 'lucide-react';

interface SidebarProps {
  isOpen: boolean;
  onClose: () => void;
}

const Sidebar: React.FC<SidebarProps> = ({ isOpen, onClose }) => {
  const { user, logout } = useAuthStore();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  const navigationItems = [
    { name: 'Dashboard', href: '/', icon: Home },
    { name: 'Health Tracking', href: '/health', icon: Activity },
    { name: 'Medications', href: '/medications', icon: Pill },
    { name: 'Appointments', href: '/appointments', icon: Calendar },
    { name: 'Messages', href: '/messages', icon: MessageCircle },
    { name: 'Comfort Zone', href: '/comfort', icon: Music },
    { name: 'Emergency', href: '/emergency', icon: Phone },
  ];

  const secondaryItems = [
    { name: 'Profile', href: '/profile', icon: User },
    { name: 'Settings', href: '/settings', icon: Settings },
  ];

  const isCaregiver = user?.role === 'caregiver';

  return (
    <>
      {/* Desktop Sidebar */}
      <div className="hidden lg:flex lg:flex-shrink-0">
        <div className="flex flex-col w-64">
          <div className="flex flex-col flex-grow bg-white border-r border-gray-200 pt-5 pb-4 overflow-y-auto">
            {/* Logo */}
            <div className="flex items-center flex-shrink-0 px-4 mb-8">
              <Heart className="w-8 h-8 text-primary-600 mr-3" />
              <span className="text-xl font-bold text-gray-900">Myeline</span>
              {isCaregiver && (
                <Shield className="w-5 h-5 text-blue-600 ml-2" title="Caregiver View" />
              )}
            </div>

            {/* Navigation */}
            <nav className="mt-5 flex-1 px-2 space-y-1">
              {navigationItems.map((item) => (
                <NavLink
                  key={item.name}
                  to={item.href}
                  className={({ isActive }) =>
                    cn(
                      'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors',
                      isActive
                        ? 'bg-primary-100 text-primary-900'
                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                    )
                  }
                >
                  {({ isActive }) => (
                    <>
                      <item.icon
                        className={cn(
                          'mr-3 h-5 w-5 flex-shrink-0',
                          isActive ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500'
                        )}
                      />
                      {item.name}
                    </>
                  )}
                </NavLink>
              ))}
            </nav>

            {/* Secondary Navigation */}
            <div className="mt-6 pt-6 border-t border-gray-200">
              <nav className="px-2 space-y-1">
                {secondaryItems.map((item) => (
                  <NavLink
                    key={item.name}
                    to={item.href}
                    className={({ isActive }) =>
                      cn(
                        'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors',
                        isActive
                          ? 'bg-gray-100 text-gray-900'
                          : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                      )
                    }
                  >
                    {({ isActive }) => (
                      <>
                        <item.icon
                          className={cn(
                            'mr-3 h-5 w-5 flex-shrink-0',
                            isActive ? 'text-gray-500' : 'text-gray-400 group-hover:text-gray-500'
                          )}
                        />
                        {item.name}
                      </>
                    )}
                  </NavLink>
                ))}
                
                <button
                  onClick={handleLogout}
                  className="group flex items-center w-full px-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900 transition-colors"
                >
                  <LogOut className="mr-3 h-5 w-5 flex-shrink-0 text-gray-400 group-hover:text-gray-500" />
                  Sign Out
                </button>
              </nav>
            </div>

            {/* User Info */}
            <div className="flex-shrink-0 flex border-t border-gray-200 p-4">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <div className="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center">
                    <span className="text-sm font-medium text-primary-700">
                      {user?.firstName?.charAt(0)}{user?.lastName?.charAt(0)}
                    </span>
                  </div>
                </div>
                <div className="ml-3 min-w-0">
                  <p className="text-sm font-medium text-gray-700 truncate">
                    {user?.firstName} {user?.lastName}
                  </p>
                  <p className="text-xs text-gray-500 truncate">
                    {isCaregiver ? 'Caregiver' : 'Patient'}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Mobile Sidebar */}
      <div className={cn(
        'lg:hidden fixed inset-0 z-50 flex',
        isOpen ? 'block' : 'hidden'
      )}>
        <div className="flex flex-col w-64 bg-white border-r border-gray-200">
          <div className="flex items-center justify-between flex-shrink-0 px-4 py-4 border-b border-gray-200">
            <div className="flex items-center">
              <Heart className="w-8 h-8 text-primary-600 mr-3" />
              <span className="text-xl font-bold text-gray-900">Myeline</span>
            </div>
            <button
              onClick={onClose}
              className="text-gray-400 hover:text-gray-600"
            >
              <X className="w-6 h-6" />
            </button>
          </div>

          {/* Mobile Navigation - Same as desktop */}
          <nav className="mt-5 flex-1 px-2 space-y-1 overflow-y-auto">
            {navigationItems.map((item) => (
              <NavLink
                key={item.name}
                to={item.href}
                onClick={onClose}
                className={({ isActive }) =>
                  cn(
                    'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors',
                    isActive
                      ? 'bg-primary-100 text-primary-900'
                      : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                  )
                }
              >
                {({ isActive }) => (
                  <>
                    <item.icon
                      className={cn(
                        'mr-3 h-5 w-5 flex-shrink-0',
                        isActive ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500'
                      )}
                    />
                    {item.name}
                  </>
                )}
              </NavLink>
            ))}
          </nav>

          {/* Mobile Secondary Navigation */}
          <div className="border-t border-gray-200 p-4">
            <div className="space-y-1">
              {secondaryItems.map((item) => (
                <NavLink
                  key={item.name}
                  to={item.href}
                  onClick={onClose}
                  className="group flex items-center px-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900 transition-colors"
                >
                  <item.icon className="mr-3 h-5 w-5 flex-shrink-0 text-gray-400 group-hover:text-gray-500" />
                  {item.name}
                </NavLink>
              ))}
              
              <button
                onClick={handleLogout}
                className="group flex items-center w-full px-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900 transition-colors"
              >
                <LogOut className="mr-3 h-5 w-5 flex-shrink-0 text-gray-400 group-hover:text-gray-500" />
                Sign Out
              </button>
            </div>
          </div>
        </div>
      </div>
    </>
  );
};

export default Sidebar;