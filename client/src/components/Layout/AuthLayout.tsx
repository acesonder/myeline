import React from 'react';
import { Heart } from 'lucide-react';

interface AuthLayoutProps {
  children: React.ReactNode;
}

const AuthLayout: React.FC<AuthLayoutProps> = ({ children }) => {
  return (
    <div className="min-h-screen flex">
      {/* Left side - Form */}
      <div className="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
        <div className="mx-auto w-full max-w-sm lg:w-96">
          <div className="text-center mb-8">
            <div className="flex items-center justify-center mb-4">
              <Heart className="w-10 h-10 text-primary-600 mr-2" />
              <h1 className="text-3xl font-bold text-gray-900">Myeline</h1>
            </div>
            <p className="text-gray-600">
              Your Cancer Care & Comfort Hub
            </p>
          </div>
          
          {children}
        </div>
      </div>

      {/* Right side - Image/Info */}
      <div className="hidden lg:block relative w-0 flex-1">
        <div className="absolute inset-0 bg-gradient-to-br from-primary-600 to-comfort-600 flex items-center justify-center">
          <div className="max-w-md text-center text-white p-8">
            <h2 className="text-3xl font-bold mb-6">
              A Safe Space for Your Health Journey
            </h2>
            <div className="space-y-4 text-lg">
              <p>✓ Track symptoms and medications</p>
              <p>✓ Connect with your caregivers</p>
              <p>✓ Access comfort and wellness tools</p>
              <p>✓ AI-powered health insights</p>
            </div>
            <div className="mt-8 p-4 bg-white/10 rounded-lg backdrop-blur-sm">
              <p className="text-sm italic">
                "Designed with love for stage 4 cancer patients and their families"
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default AuthLayout;