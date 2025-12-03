import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { authAPI } from '../../services/api';
import Header from '../layout/Header';
import Footer from '../layout/Footer';
import Clients from './Clients';
import Sessions from './Sessions';
import ProgramBuilder from './ProgramBuilder';

const Dashboard = () => {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('clients'); // 'clients', 'sessions', 'programs'

  useEffect(() => {
    checkAuth();
  }, []);

  const checkAuth = async () => {
    try {
      const token = localStorage.getItem('auth_token');
      if (!token) {
        navigate('/');
        return;
      }

      const response = await authAPI.getUser();
      const userData = response.data;
      
      if (userData.type !== 'coach') {
        navigate('/');
        return;
      }

      setUser(userData);
    } catch (err) {
      console.error('Auth error:', err);
      localStorage.removeItem('auth_token');
      navigate('/');
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50">
        <Header />
        <div className="container mx-auto px-6 py-20">
          <div className="text-center">
            <p className="text-gray-600">{t('common.loading')}</p>
          </div>
        </div>
        <Footer />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="pt-20 pb-12">
        <div className="container mx-auto px-6">
          {/* Dashboard Header */}
          <div className="bg-white rounded-xl shadow-lg p-6 mb-6 border border-gray-200">
            <div className="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
              <div>
                <h1 className="text-3xl font-bold text-gray-900 mb-2">
                  {t('coach.dashboard.title')}
                </h1>
                <p className="text-gray-600">
                  {t('coach.dashboard.welcome')}, {user?.name}
                </p>
              </div>
              <div className="flex items-center gap-4">
                <div className="text-right">
                  <p className="text-sm text-gray-500">{t('coach.dashboard.specialization')}</p>
                  <p className="font-semibold text-blue-600">{user?.specialization || 'General Fitness'}</p>
                </div>
              </div>
            </div>
          </div>

          {/* Tabs */}
          <div className="bg-white rounded-xl shadow-lg mb-6 border border-gray-200">
            <div className="flex border-b border-gray-200">
              <button
                onClick={() => setActiveTab('clients')}
                className={`
                  px-6 py-4 font-semibold text-sm transition-colors border-b-2
                  ${activeTab === 'clients'
                    ? 'border-blue-600 text-blue-600'
                    : 'border-transparent text-gray-600 hover:text-gray-900'
                  }
                `}
              >
                {t('coach.dashboard.clients')}
              </button>
              <button
                onClick={() => setActiveTab('sessions')}
                className={`
                  px-6 py-4 font-semibold text-sm transition-colors border-b-2
                  ${activeTab === 'sessions'
                    ? 'border-blue-600 text-blue-600'
                    : 'border-transparent text-gray-600 hover:text-gray-900'
                  }
                `}
              >
                {t('coach.dashboard.sessions')}
              </button>
              <button
                onClick={() => setActiveTab('programs')}
                className={`
                  px-6 py-4 font-semibold text-sm transition-colors border-b-2
                  ${activeTab === 'programs'
                    ? 'border-blue-600 text-blue-600'
                    : 'border-transparent text-gray-600 hover:text-gray-900'
                  }
                `}
              >
                {t('coach.dashboard.programs')}
              </button>
            </div>
          </div>

          {/* Tab Content */}
          <div className="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            {activeTab === 'clients' && <Clients />}
            {activeTab === 'sessions' && <Sessions />}
            {activeTab === 'programs' && <ProgramBuilder />}
          </div>
        </div>
      </div>
      <Footer />
    </div>
  );
};

export default Dashboard;

