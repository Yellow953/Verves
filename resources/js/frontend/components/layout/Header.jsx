import React, { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import LanguageSwitcher from '../LanguageSwitcher';
import AuthModal from '../auth/AuthModal';
import { removeAuthToken, authAPI } from '../../services/api';

const Header = () => {
  const { t } = useTranslation();
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [isAuthModalOpen, setIsAuthModalOpen] = useState(false);
  const [authMode, setAuthMode] = useState('login');
  const [user, setUser] = useState(null);

  useEffect(() => {
    // Check if user is logged in
    const userData = localStorage.getItem('user');
    if (userData) {
      try {
        setUser(JSON.parse(userData));
      } catch (e) {
        localStorage.removeItem('user');
      }
    }
  }, []);

  const handleLogout = async () => {
    try {
      // Try to call logout API if token exists
      const token = localStorage.getItem('auth_token');
      if (token) {
        await authAPI.logout();
      }
    } catch (error) {
      // Even if logout fails, clear local storage
      console.error('Logout error:', error);
    } finally {
      removeAuthToken();
      localStorage.removeItem('user');
      setUser(null);
      window.location.reload();
    }
  };

  const openAuthModal = (mode = 'login') => {
    setAuthMode(mode);
    setIsAuthModalOpen(true);
  };

  return (
    <header className="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm shadow-sm">
      <nav className="container mx-auto px-6 py-4">
        <div className="flex items-center justify-between">
          {/* Logo */}
          <div className="flex items-center">
            <a href="/" className="text-2xl font-bold text-gray-900">
              <span className="text-blue-600">Verve</span>s
            </a>
          </div>

          {/* Desktop Navigation */}
          <div className="hidden md:flex items-center space-x-8">
            <a href="#home" className="text-gray-700 hover:text-blue-600 transition-colors font-medium">
              {t('navigation.home')}
            </a>
            <a href="/services" className="text-gray-700 hover:text-blue-600 transition-colors font-medium">
              {t('navigation.services')}
            </a>
            <a href="#courses" className="text-gray-700 hover:text-blue-600 transition-colors font-medium">
              Courses
            </a>
            <a href="/coaches" className="text-gray-700 hover:text-blue-600 transition-colors font-medium">
              {t('coach.title')}
            </a>
            <a href="/forum" className="text-gray-700 hover:text-blue-600 transition-colors font-medium">
              {t('navigation.forum')}
            </a>
            <LanguageSwitcher />
            {user ? (
              <>
                {user.type === 'coach' && (
                  <a
                    href="/coach/dashboard"
                    className="text-gray-700 hover:text-blue-600 transition-colors font-medium"
                  >
                    {t('navigation.dashboard')}
                  </a>
                )}
                <button
                  onClick={handleLogout}
                  className="text-gray-700 hover:text-blue-600 transition-colors font-medium"
                >
                  {t('auth.logout')}
                </button>
              </>
            ) : (
              <>
                <button
                  onClick={() => openAuthModal('login')}
                  className="text-gray-700 hover:text-blue-600 transition-colors font-medium"
                >
                  {t('auth.login')}
                </button>
                <button
                  onClick={() => openAuthModal('signup')}
                  className="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-6 py-2.5 rounded-lg hover:from-blue-700 hover:to-blue-900 transition-all font-semibold shadow-md"
                >
                  {t('auth.signUp')}
                </button>
              </>
            )}
          </div>

          {/* Mobile Menu Button */}
          <button
            className="md:hidden text-gray-700 hover:text-blue-600 transition-colors"
            onClick={() => setIsMenuOpen(!isMenuOpen)}
          >
            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              {isMenuOpen ? (
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
              ) : (
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
              )}
            </svg>
          </button>
        </div>

        {/* Mobile Menu */}
        {isMenuOpen && (
          <div className="md:hidden mt-4 pb-4 space-y-4 bg-white rounded-lg p-4 shadow-lg border border-gray-200">
            <a href="#home" className="block text-gray-700 hover:text-blue-600 transition-colors">
              {t('navigation.home')}
            </a>
            <a href="/services" className="block text-gray-700 hover:text-blue-600 transition-colors">
              {t('navigation.services')}
            </a>
            <a href="#courses" className="block text-gray-700 hover:text-blue-600 transition-colors">
              Courses
            </a>
            <a href="/coaches" className="block text-gray-700 hover:text-blue-600 transition-colors">
              {t('coach.title')}
            </a>
            <a href="/forum" className="block text-gray-700 hover:text-blue-600 transition-colors">
              {t('navigation.forum')}
            </a>
            <div className="flex items-center justify-between pt-4 border-t border-gray-200">
              <LanguageSwitcher />
              <div className="space-x-4">
                {user ? (
                  <>
                    {user.type === 'coach' && (
                      <a href="/coach/dashboard" className="text-gray-700 hover:text-blue-600 transition-colors">
                        {t('navigation.dashboard')}
                      </a>
                    )}
                    <button
                      onClick={handleLogout}
                      className="text-gray-700 hover:text-blue-600 transition-colors"
                    >
                      {t('auth.logout')}
                    </button>
                  </>
                ) : (
                  <>
                    <button
                      onClick={() => openAuthModal('login')}
                      className="text-gray-700 hover:text-blue-600 transition-colors"
                    >
                      {t('auth.login')}
                    </button>
                    <button
                      onClick={() => openAuthModal('signup')}
                      className="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-4 py-2 rounded-lg hover:from-blue-700 hover:to-blue-900 transition-all"
                    >
                      {t('auth.signUp')}
                    </button>
                  </>
                )}
              </div>
            </div>
          </div>
        )}
      </nav>
      
      {/* Auth Modal */}
      <AuthModal
        isOpen={isAuthModalOpen}
        onClose={() => setIsAuthModalOpen(false)}
        initialMode={authMode}
      />
    </header>
  );
};

export default Header;

