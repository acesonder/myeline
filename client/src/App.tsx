import { useEffect } from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import { useAuthStore } from '@/stores/authStore';
import { socketService } from '@/services/socket';

// Layout components
import Layout from '@/components/Layout/Layout';
import AuthLayout from '@/components/Layout/AuthLayout';

// Page components
import Dashboard from '@/pages/Dashboard';
import Login from '@/pages/auth/Login';
import Signup from '@/pages/auth/Signup';
import Profile from '@/pages/Profile';
import Medications from '@/pages/Medications';
import Health from '@/pages/Health';
import Appointments from '@/pages/Appointments';
import Messages from '@/pages/Messages';
import Comfort from '@/pages/Comfort';
import Emergency from '@/pages/Emergency';
import CaregiverDashboard from '@/pages/CaregiverDashboard';

// Components
import PrivateRoute from '@/components/auth/PrivateRoute';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import ErrorBoundary from '@/components/ui/ErrorBoundary';

function App() {
  const { isAuthenticated, user, refreshAuth, isLoading } = useAuthStore();

  useEffect(() => {
    // Initialize authentication on app load
    const token = localStorage.getItem('auth_token');
    if (token && !isAuthenticated) {
      refreshAuth();
    }
  }, [refreshAuth, isAuthenticated]);

  useEffect(() => {
    // Connect to WebSocket when authenticated
    if (isAuthenticated && user) {
      socketService.connect(user.id);
    }

    // Cleanup on unmount
    return () => {
      socketService.disconnect();
    };
  }, [isAuthenticated, user]);

  useEffect(() => {
    // Apply user preferences to document
    if (user?.profile?.preferences) {
      const { theme, fontFamily } = user.profile.preferences;
      
      document.documentElement.setAttribute('data-theme', theme);
      document.documentElement.setAttribute('data-font', fontFamily);
    }
  }, [user?.profile?.preferences]);

  if (isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <LoadingSpinner size="lg" />
      </div>
    );
  }

  return (
    <ErrorBoundary>
      <div className="min-h-screen bg-gray-50">
        <Routes>
          {/* Public routes */}
          <Route
            path="/login"
            element={
              isAuthenticated ? (
                <Navigate to="/" replace />
              ) : (
                <AuthLayout>
                  <Login />
                </AuthLayout>
              )
            }
          />
          <Route
            path="/signup"
            element={
              isAuthenticated ? (
                <Navigate to="/" replace />
              ) : (
                <AuthLayout>
                  <Signup />
                </AuthLayout>
              )
            }
          />

          {/* Private routes */}
          <Route
            path="/"
            element={
              <PrivateRoute>
                <Layout>
                  {user?.role === 'caregiver' ? <CaregiverDashboard /> : <Dashboard />}
                </Layout>
              </PrivateRoute>
            }
          />
          
          <Route
            path="/profile"
            element={
              <PrivateRoute>
                <Layout>
                  <Profile />
                </Layout>
              </PrivateRoute>
            }
          />
          
          <Route
            path="/medications"
            element={
              <PrivateRoute>
                <Layout>
                  <Medications />
                </Layout>
              </PrivateRoute>
            }
          />
          
          <Route
            path="/health"
            element={
              <PrivateRoute>
                <Layout>
                  <Health />
                </Layout>
              </PrivateRoute>
            }
          />
          
          <Route
            path="/appointments"
            element={
              <PrivateRoute>
                <Layout>
                  <Appointments />
                </Layout>
              </PrivateRoute>
            }
          />
          
          <Route
            path="/messages"
            element={
              <PrivateRoute>
                <Layout>
                  <Messages />
                </Layout>
              </PrivateRoute>
            }
          />
          
          <Route
            path="/comfort"
            element={
              <PrivateRoute>
                <Layout>
                  <Comfort />
                </Layout>
              </PrivateRoute>
            }
          />
          
          <Route
            path="/emergency"
            element={
              <PrivateRoute>
                <Layout>
                  <Emergency />
                </Layout>
              </PrivateRoute>
            }
          />

          {/* Catch all route */}
          <Route
            path="*"
            element={
              <Navigate to={isAuthenticated ? "/" : "/login"} replace />
            }
          />
        </Routes>
      </div>
    </ErrorBoundary>
  );
}

export default App;