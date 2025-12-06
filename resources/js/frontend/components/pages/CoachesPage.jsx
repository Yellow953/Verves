import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { coachesAPI } from '../../services/api';

const CoachesPage = () => {
  const { t } = useTranslation();
  const [coaches, setCoaches] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [specializationFilter, setSpecializationFilter] = useState('');
  const [specializations, setSpecializations] = useState([]);
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, per_page: 12 });

  useEffect(() => {
    loadCoaches();
    loadSpecializations();
  }, []);

  useEffect(() => {
    loadCoaches(1);
  }, [searchTerm, specializationFilter]);

  const loadCoaches = async (page = 1) => {
    try {
      setLoading(true);
      const params = {
        per_page: pagination.per_page,
        page,
      };
      if (searchTerm) {
        params.search = searchTerm;
      }
      if (specializationFilter) {
        params.specialization = specializationFilter;
      }
      const response = await coachesAPI.list(params);
      if (response.data.success && response.data.data) {
        const paginatedData = response.data.data;
        setCoaches(paginatedData.data || []);
        setPagination({
          current_page: paginatedData.current_page || 1,
          last_page: paginatedData.last_page || 1,
          per_page: paginatedData.per_page || 12,
        });
      } else if (response.data.data) {
        // Fallback for non-standard response
        const paginatedData = response.data.data;
        setCoaches(paginatedData.data || paginatedData || []);
        setPagination({
          current_page: paginatedData.current_page || 1,
          last_page: paginatedData.last_page || 1,
          per_page: paginatedData.per_page || 12,
        });
      }
    } catch (err) {
      console.error('Failed to load coaches:', err);
      setCoaches([]);
    } finally {
      setLoading(false);
    }
  };

  const loadSpecializations = async () => {
    try {
      const response = await coachesAPI.list({ per_page: 100 });
      if (response.data.success && response.data.data) {
        const paginatedData = response.data.data;
        const allCoaches = paginatedData.data || [];
        const uniqueSpecializations = [...new Set(allCoaches.map(c => c.specialization).filter(Boolean))];
        setSpecializations(uniqueSpecializations.sort());
      }
    } catch (err) {
      console.error('Failed to load specializations:', err);
    }
  };

  const handleSearch = (e) => {
    e.preventDefault();
    loadCoaches(1);
  };

  const handleFilterChange = (value) => {
    setSpecializationFilter(value);
  };

  return (
    <div className="min-h-screen bg-gray-50 pt-24 pb-16">
      <div className="container mx-auto px-6">
        {/* Header */}
        <div className="text-center mb-12">
          <h1 className="text-5xl font-bold text-gray-900 mb-4">
            {t('coachesPage.title')}
          </h1>
          <p className="text-xl text-gray-600 max-w-2xl mx-auto">
            {t('coachesPage.subtitle')}
          </p>
        </div>

        {/* Filters and Search */}
        <div className="bg-white rounded-xl shadow-md p-6 mb-8">
          <form onSubmit={handleSearch} className="flex flex-col md:flex-row gap-4">
            <div className="flex-1">
              <input
                type="text"
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                placeholder={t('coachesPage.searchPlaceholder')}
                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div className="md:w-64">
              <select
                value={specializationFilter}
                onChange={(e) => handleFilterChange(e.target.value)}
                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">{t('coachesPage.allSpecializations')}</option>
                {specializations.map((spec) => (
                  <option key={spec} value={spec}>
                    {spec}
                  </option>
                ))}
              </select>
            </div>
            <button
              type="submit"
              className="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-8 py-3 rounded-lg hover:from-blue-700 hover:to-blue-900 transition-all font-semibold shadow-md"
            >
              {t('common.search')}
            </button>
            {(searchTerm || specializationFilter) && (
              <button
                type="button"
                onClick={() => {
                  setSearchTerm('');
                  setSpecializationFilter('');
                  loadCoaches(1);
                }}
                className="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
              >
                {t('common.clear')}
              </button>
            )}
          </form>
        </div>

        {/* Coaches Grid */}
        {loading ? (
          <div className="text-center py-12">
            <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <p className="mt-4 text-gray-600">{t('common.loading')}</p>
          </div>
        ) : coaches.length > 0 ? (
          <>
            <div className="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
              {coaches.map((coach) => (
                <div
                  key={coach.id}
                  className="bg-white rounded-xl p-6 border-2 border-gray-200 hover:border-blue-500 hover:shadow-xl transition-all transform hover:-translate-y-2"
                >
                  <div className="text-center mb-4">
                    <div className="w-24 h-24 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl text-white font-bold shadow-lg">
                      {coach.name?.charAt(0)?.toUpperCase() || '👨‍💼'}
                    </div>
                    <h3 className="text-xl font-bold text-gray-900 mb-2">
                      {coach.name}
                    </h3>
                    {coach.specialization && (
                      <p className="text-blue-600 font-semibold mb-2">
                        {coach.specialization}
                      </p>
                    )}
                    {coach.bio && (
                      <p className="text-gray-500 text-sm mb-4 line-clamp-3">
                        {coach.bio}
                      </p>
                    )}
                  </div>
                  <div className="flex flex-col gap-2">
                    <Link
                      to={`/coaches/${coach.id}/book`}
                      className="text-center bg-gradient-to-r from-blue-600 to-blue-800 text-white px-6 py-2.5 rounded-lg font-semibold text-sm hover:from-blue-700 hover:to-blue-900 transition-all shadow-md"
                    >
                      {t('coach.bookSession')}
                    </Link>
                    {coach.email && (
                      <a
                        href={`mailto:${coach.email}`}
                        className="text-center text-blue-600 hover:text-blue-700 text-sm font-medium"
                      >
                        {t('coachesPage.contact')}
                      </a>
                    )}
                  </div>
                </div>
              ))}
            </div>

            {/* Pagination */}
            {pagination.last_page > 1 && (
              <div className="flex justify-center gap-2">
                {Array.from({ length: pagination.last_page }, (_, i) => i + 1).map((page) => (
                  <button
                    key={page}
                    onClick={() => loadCoaches(page)}
                    className={`px-4 py-2 rounded-lg ${
                      page === pagination.current_page
                        ? 'bg-gradient-to-r from-blue-600 to-blue-800 text-white'
                        : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                    }`}
                  >
                    {page}
                  </button>
                ))}
              </div>
            )}
          </>
        ) : (
          <div className="text-center py-12 bg-white rounded-xl shadow-md">
            <p className="text-gray-600 text-lg">{t('coachesPage.noCoaches')}</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default CoachesPage;

