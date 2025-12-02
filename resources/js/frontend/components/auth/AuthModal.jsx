import React, { useState, useEffect } from 'react';
import { createPortal } from 'react-dom';
import { useTranslation } from 'react-i18next';
import { authAPI, setAuthToken } from '../../services/api';

const AuthModal = ({ isOpen, onClose, initialMode = 'login' }) => {
  const { t } = useTranslation();
  const [mode, setMode] = useState(initialMode); // 'login', 'signup', 'signup-coach'
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
  });

  // Prevent body scroll when modal is open
  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = 'unset';
    }
    return () => {
      document.body.style.overflow = 'unset';
    };
  }, [isOpen]);

  if (!isOpen) return null;

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value,
    }));
    setError(''); // Clear error on input change
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');

    try {
      if (mode === 'login') {
        const response = await authAPI.login({
          email: formData.email,
          password: formData.password,
        });
        
        if (response.data.token) {
          setAuthToken(response.data.token);
          localStorage.setItem('user', JSON.stringify(response.data.user));
          onClose();
          window.location.reload(); // Reload to update auth state
        }
      } else {
        // Signup (client or coach)
        const response = await authAPI.register({
          name: formData.name,
          email: formData.email,
          phone: formData.phone,
          password: formData.password,
          password_confirmation: formData.password_confirmation,
          type: mode === 'signup-coach' ? 'coach' : 'client',
        });
        
        if (response.data.token) {
          setAuthToken(response.data.token);
          localStorage.setItem('user', JSON.stringify(response.data.user));
          onClose();
          window.location.reload(); // Reload to update auth state
        }
      }
    } catch (err) {
      const errorMessage = err.response?.data?.message || 
                          err.response?.data?.errors?.email?.[0] ||
                          err.response?.data?.errors?.password?.[0] ||
                          t('auth.error');
      setError(errorMessage);
    } finally {
      setLoading(false);
    }
  };

  const switchMode = (newMode) => {
    setMode(newMode);
    setError('');
    setFormData({
      name: '',
      email: '',
      phone: '',
      password: '',
      password_confirmation: '',
    });
  };

  const modalContent = (
    <div 
      className="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-md p-4" 
      onClick={onClose}
    >
      <div 
        className="bg-white rounded-3xl shadow-2xl w-full max-w-md max-h-[90vh] flex flex-col overflow-hidden border border-gray-100" 
        onClick={(e) => e.stopPropagation()}
      >
        {/* Header */}
        <div className="relative px-8 pt-8 pb-6">
          <button
            onClick={onClose}
            className="absolute top-6 right-6 text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-full"
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
          
          <div className="pr-8">
            <div className="flex items-center gap-3 mb-2">
              <div className="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-xl flex items-center justify-center shadow-lg">
                <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <div>
                <h2 className="text-2xl font-bold text-gray-900">
                  {mode === 'login' && t('auth.login')}
                  {mode === 'signup' && t('auth.signUp')}
                  {mode === 'signup-coach' && t('auth.signUpAsCoach')}
                </h2>
                <p className="text-sm text-gray-500 mt-0.5">
                  {mode === 'login' && 'Welcome back! Please sign in to continue'}
                  {mode === 'signup' && 'Create your account to get started'}
                  {mode === 'signup-coach' && 'Join as a coach and help others achieve their goals'}
                </p>
              </div>
            </div>
          </div>
        </div>

        {/* Content */}
        <div className="px-8 pb-8 overflow-y-auto flex-1">
          {/* Error Message */}
          {error && (
            <div className="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg text-sm flex items-start gap-3">
              <svg className="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>{error}</span>
            </div>
          )}

          {/* Form */}
          <form onSubmit={handleSubmit} className="space-y-5">
            {/* Name field for signup */}
            {(mode === 'signup' || mode === 'signup-coach') && (
              <div>
                <label htmlFor="name" className="block text-sm font-semibold text-gray-900 mb-2">
                  {t('auth.name')}
                </label>
                <div className="relative">
                  <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg className="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                  </div>
                  <input
                    type="text"
                    id="name"
                    name="name"
                    value={formData.name}
                    onChange={handleInputChange}
                    required
                    className="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-gray-50 focus:bg-white text-gray-900 placeholder-gray-400"
                    placeholder={t('auth.namePlaceholder')}
                  />
                </div>
              </div>
            )}

            {/* Email */}
            <div>
              <label htmlFor="email" className="block text-sm font-semibold text-gray-900 mb-2">
                {t('auth.email')}
              </label>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg className="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <input
                  type="email"
                  id="email"
                  name="email"
                  value={formData.email}
                  onChange={handleInputChange}
                  required
                  className="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-gray-50 focus:bg-white text-gray-900 placeholder-gray-400"
                  placeholder={t('auth.emailPlaceholder')}
                />
              </div>
            </div>

            {/* Phone field for signup */}
            {(mode === 'signup' || mode === 'signup-coach') && (
              <div>
                <label htmlFor="phone" className="block text-sm font-semibold text-gray-900 mb-2">
                  {t('auth.phone')}
                </label>
                <div className="relative">
                  <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg className="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                  </div>
                  <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value={formData.phone}
                    onChange={handleInputChange}
                    required
                    className="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-gray-50 focus:bg-white text-gray-900 placeholder-gray-400"
                    placeholder={t('auth.phonePlaceholder')}
                  />
                </div>
              </div>
            )}

            {/* Password */}
            <div>
              <label htmlFor="password" className="block text-sm font-semibold text-gray-900 mb-2">
                {t('auth.password')}
              </label>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg className="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </div>
                <input
                  type="password"
                  id="password"
                  name="password"
                  value={formData.password}
                  onChange={handleInputChange}
                  required
                  minLength={8}
                  className="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-gray-50 focus:bg-white text-gray-900 placeholder-gray-400"
                  placeholder={t('auth.passwordPlaceholder')}
                />
              </div>
            </div>

            {/* Confirm Password for signup */}
            {(mode === 'signup' || mode === 'signup-coach') && (
              <div>
                <label htmlFor="password_confirmation" className="block text-sm font-semibold text-gray-900 mb-2">
                  {t('auth.confirmPassword')}
                </label>
                <div className="relative">
                  <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg className="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                  </div>
                  <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    value={formData.password_confirmation}
                    onChange={handleInputChange}
                    required
                    minLength={8}
                    className="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-gray-50 focus:bg-white text-gray-900 placeholder-gray-400"
                    placeholder={t('auth.confirmPasswordPlaceholder')}
                  />
                </div>
              </div>
            )}

            {/* Submit Button */}
            <button
              type="submit"
              disabled={loading}
              className="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3.5 rounded-xl font-semibold hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none shadow-lg shadow-blue-500/30 hover:shadow-blue-500/40 flex items-center justify-center gap-2"
            >
              {loading ? (
                <>
                  <svg className="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span>{t('common.loading')}</span>
                </>
              ) : (
                <>
                  <span>{mode === 'login' ? t('auth.signIn') : t('auth.signUp')}</span>
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 7l5 5m0 0l-5 5m5-5H6" />
                  </svg>
                </>
              )}
            </button>
          </form>

          {/* Mode Switcher */}
          <div className="mt-8 pt-6 border-t border-gray-200">
            {mode === 'login' ? (
              <div className="text-center space-y-4">
                <p className="text-sm text-gray-500">
                  {t('auth.dontHaveAccount')}
                </p>
                <div className="flex gap-3 justify-center">
                  <button
                    onClick={() => switchMode('signup')}
                    className="px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors"
                  >
                    {t('auth.signUpAsClient')}
                  </button>
                  <span className="text-gray-300">|</span>
                  <button
                    onClick={() => switchMode('signup-coach')}
                    className="px-4 py-2 text-sm font-medium text-purple-600 hover:text-purple-700 hover:bg-purple-50 rounded-lg transition-colors"
                  >
                    {t('auth.signUpAsCoach')}
                  </button>
                </div>
              </div>
            ) : (
              <div className="text-center">
                <p className="text-sm text-gray-500">
                  {t('auth.alreadyHaveAccount')}{' '}
                  <button
                    onClick={() => switchMode('login')}
                    className="text-blue-600 hover:text-blue-700 font-semibold hover:underline transition-colors"
                  >
                    {t('auth.login')}
                  </button>
                </p>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );

  // Render modal using portal to document.body
  return createPortal(modalContent, document.body);
};

export default AuthModal;

