import React from 'react';
import { useTranslation } from 'react-i18next';

const Footer = () => {
  const { t } = useTranslation();

  return (
    <footer className="bg-gray-900 text-gray-300 py-12">
      <div className="container mx-auto px-6">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          {/* Brand */}
          <div>
            <h3 className="text-2xl font-bold text-white mb-4">
              <span className="text-blue-500">Verve</span>s
            </h3>
            <p className="text-gray-400">
              {t('common.subtitle')}
            </p>
          </div>

          {/* Quick Links */}
          <div>
            <h4 className="text-white font-semibold mb-4">Quick Links</h4>
            <ul className="space-y-2">
              <li>
                <a href="#home" className="hover:text-white transition-colors">
                  {t('navigation.home')}
                </a>
              </li>
              <li>
                <a href="#features" className="hover:text-white transition-colors">
                  {t('home.features.title')}
                </a>
              </li>
              <li>
                <a href="#programs" className="hover:text-white transition-colors">
                  {t('navigation.programs')}
                </a>
              </li>
            </ul>
          </div>

          {/* Resources */}
          <div>
            <h4 className="text-white font-semibold mb-4">Resources</h4>
            <ul className="space-y-2">
              <li>
                <a href="#forum" className="hover:text-white transition-colors">
                  {t('navigation.forum')}
                </a>
              </li>
              <li>
                <a href="/login" className="hover:text-white transition-colors">
                  {t('auth.login')}
                </a>
              </li>
              <li>
                <a href="/register" className="hover:text-white transition-colors">
                  {t('auth.register')}
                </a>
              </li>
            </ul>
          </div>

          {/* Contact */}
          <div>
            <h4 className="text-white font-semibold mb-4">Contact</h4>
            <p className="text-gray-400 mb-2">support@verve.com</p>
            <p className="text-gray-400">+1 (555) 123-4567</p>
          </div>
        </div>

        <div className="border-t border-gray-800 mt-8 pt-8 text-center text-gray-500">
          <p>&copy; {new Date().getFullYear()} Verve. All rights reserved.</p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;

