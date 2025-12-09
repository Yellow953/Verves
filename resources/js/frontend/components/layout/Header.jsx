import React, { useState, useEffect } from 'react';
import { Link, useNavigate, useLocation } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import LanguageSwitcher from '../LanguageSwitcher';
import AuthModal from '../auth/AuthModal';
import { removeAuthToken, authAPI } from '../../services/api';

const Header = () => {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const location = useLocation();
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

  // Close mobile menu when route changes
  useEffect(() => {
    setIsMenuOpen(false);
  }, [location.pathname]);

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
    setIsMenuOpen(false); // Close mobile menu when opening auth modal
  };

  const handleNavClick = (e, path) => {
    e.preventDefault();
    setIsMenuOpen(false); // Close mobile menu
    
    if (path.startsWith('#')) {
      // Handle anchor links (scroll to section)
      if (location.pathname === '/') {
        const element = document.querySelector(path);
        if (element) {
          element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      } else {
        // If not on home page, navigate to home first, then scroll
        navigate('/');
        setTimeout(() => {
          const element = document.querySelector(path);
          if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }, 100);
      }
    } else {
      // Regular route navigation
      navigate(path);
    }
  };

  return (
    <header className="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm shadow-sm">
      <nav className="container mx-auto px-4 sm:px-6 py-3 sm:py-4">
        <div className="flex items-center justify-between">
          {/* Logo */}
          <div className="flex items-center">
            <Link to="/" className="text-2xl font-bold text-gray-900 hover:opacity-80 transition-opacity">
              <span className="text-blue-600">Verve</span>s
            </Link>
          </div>

          {/* Desktop Navigation */}
          <div className="hidden md:flex items-center space-x-6 lg:space-x-8">
            <button
              onClick={(e) => handleNavClick(e, '#home')}
              className="text-gray-700 hover:text-blue-600 transition-colors font-medium"
            >
              {t('navigation.home')}
            </button>
            <Link
              to="/services"
              className="text-gray-700 hover:text-blue-600 transition-colors font-medium"
            >
              {t('navigation.services')}
            </Link>
            <button
              onClick={(e) => handleNavClick(e, '#courses')}
              className="text-gray-700 hover:text-blue-600 transition-colors font-medium"
            >
              Courses
            </button>
            <Link
              to="/coaches"
              className="text-gray-700 hover:text-blue-600 transition-colors font-medium"
            >
              {t('coach.title')}
            </Link>
            <Link
              to="/forum"
              className="text-gray-700 hover:text-blue-600 transition-colors font-medium"
            >
              {t('navigation.forum')}
            </Link>
            <LanguageSwitcher />
            {user ? (
              <>
                {user.type === 'coach' && (
                  <Link
                    to="/coach/dashboard"
                    className="text-gray-700 hover:text-blue-600 transition-colors font-medium"
                  >
                    {t('navigation.dashboard')}
                  </Link>
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
                  className="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-4 lg:px-6 py-2.5 rounded-lg hover:from-blue-700 hover:to-blue-900 transition-all font-semibold shadow-md whitespace-nowrap"
                >
                  {t('auth.signUp')}
                </button>
              </>
            )}
          </div>

          {/* Mobile Menu Button */}
          <button
            className="md:hidden text-gray-700 hover:text-blue-600 transition-colors p-2 -mr-2"
            onClick={() => setIsMenuOpen(!isMenuOpen)}
            aria-label="Toggle menu"
            aria-expanded={isMenuOpen}
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
          <div className="md:hidden mt-4 pb-4 bg-white rounded-xl shadow-xl border-2 border-gray-200 overflow-hidden animate-in slide-in-from-top duration-200">
            <div className="px-4 py-3 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-gray-200">
              <h3 className="font-semibold text-gray-900">Menu</h3>
            </div>
            <div className="px-4 py-3 space-y-1">
              <button
                onClick={(e) => handleNavClick(e, '#home')}
                className="w-full text-left px-4 py-3 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors font-medium"
              >
                {t('navigation.home')}
              </button>
              <Link
                to="/services"
                onClick={() => setIsMenuOpen(false)}
                className="block px-4 py-3 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors font-medium"
              >
                {t('navigation.services')}
              </Link>
              <button
                onClick={(e) => handleNavClick(e, '#courses')}
                className="w-full text-left px-4 py-3 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors font-medium"
              >
                Courses
              </button>
              <Link
                to="/coaches"
                onClick={() => setIsMenuOpen(false)}
                className="block px-4 py-3 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors font-medium"
              >
                {t('coach.title')}
              </Link>
              <Link
                to="/forum"
                onClick={() => setIsMenuOpen(false)}
                className="block px-4 py-3 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors font-medium"
              >
                {t('navigation.forum')}
              </Link>
            </div>
            
            <div className="px-4 py-3 border-t border-gray-200 space-y-3">
              <div className="flex items-center justify-between">
                <span className="text-sm text-gray-600 font-medium">Language</span>
                <LanguageSwitcher />
              </div>
              
              {user ? (
                <div className="space-y-2">
                  {user.type === 'coach' && (
                    <Link
                      to="/coach/dashboard"
                      onClick={() => setIsMenuOpen(false)}
                      className="block w-full text-center px-4 py-2.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg transition-colors font-medium"
                    >
                      {t('navigation.dashboard')}
                    </Link>
                  )}
                  <button
                    onClick={handleLogout}
                    className="w-full px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors font-medium border border-gray-300"
                  >
                    {t('auth.logout')}
                  </button>
                </div>
              ) : (
                <div className="space-y-2">
                  <button
                    onClick={() => openAuthModal('login')}
                    className="w-full px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors font-medium border border-gray-300"
                  >
                    {t('auth.login')}
                  </button>
                  <button
                    onClick={() => openAuthModal('signup')}
                    className="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white px-4 py-2.5 rounded-lg hover:from-blue-700 hover:to-blue-900 transition-all font-semibold shadow-md"
                  >
                    {t('auth.signUp')}
                  </button>
                </div>
              )}
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

