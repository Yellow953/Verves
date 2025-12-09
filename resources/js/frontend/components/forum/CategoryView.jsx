import React, { useState, useEffect } from 'react';
import { Link, useParams, useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { forumAPI } from '../../services/api';
import CreateTopicModal from './CreateTopicModal';

const CategoryView = () => {
  const { t } = useTranslation();
  const { id } = useParams();
  const navigate = useNavigate();
  const [category, setCategory] = useState(null);
  const [topics, setTopics] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [searchTerm, setSearchTerm] = useState('');
  const [isCreateModalOpen, setIsCreateModalOpen] = useState(false);
  const [user, setUser] = useState(null);
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1 });

  useEffect(() => {
    const userData = localStorage.getItem('user');
    if (userData) {
      try {
        setUser(JSON.parse(userData));
      } catch (e) {
        localStorage.removeItem('user');
      }
    }
    fetchCategory();
    fetchTopics();
  }, [id]);

  const fetchCategory = async () => {
    try {
      const response = await forumAPI.getCategory(id);
      if (response.data.success) {
        setCategory(response.data.data);
      }
    } catch (err) {
      setError(err.response?.data?.message || t('forum.error.loading'));
    }
  };

  const fetchTopics = async (page = 1) => {
    try {
      setLoading(true);
      const params = {
        category_id: id,
        per_page: 15,
        page,
      };
      if (searchTerm) {
        params.search = searchTerm;
      }
      const response = await forumAPI.getTopics(params);
      if (response.data.success) {
        setTopics(response.data.data.data || []);
        setPagination({
          current_page: response.data.data.current_page,
          last_page: response.data.data.last_page,
        });
      }
    } catch (err) {
      setError(err.response?.data?.message || t('forum.error.loading'));
    } finally {
      setLoading(false);
    }
  };

  const handleSearch = (e) => {
    e.preventDefault();
    fetchTopics(1);
  };

  const handleTopicCreated = () => {
    setIsCreateModalOpen(false);
    fetchTopics();
    fetchCategory();
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
  };

  if (loading && !topics.length) {
    return (
      <div className="min-h-screen bg-gray-50 pt-24 pb-16">
        <div className="container mx-auto px-6">
          <div className="text-center py-12">
            <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <p className="mt-4 text-gray-600">{t('forum.loading')}</p>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-gray-50 pt-24 pb-16">
      <div className="container mx-auto px-6 max-w-6xl">
        {/* Header */}
        <div className="mb-8">
          <Link 
            to="/forum" 
            className="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 mb-4 font-medium transition-colors"
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {t('forum.backToForum')}
          </Link>
          {category && (
            <div className="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-8 text-white shadow-xl">
              <h1 className="text-4xl font-bold mb-3">{category.name}</h1>
              {category.description && (
                <p className="text-blue-100 text-lg">{category.description}</p>
              )}
            </div>
          )}
        </div>

        {/* Search and Create */}
        <div className="mb-6 flex flex-col sm:flex-row gap-4">
          <form onSubmit={handleSearch} className="flex-1 flex gap-2">
            <div className="relative flex-1">
              <input
                type="text"
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                placeholder={t('forum.searchTopics')}
                className="w-full px-4 py-3 pl-11 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm"
              />
              <svg className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <button
              type="submit"
              className="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-6 py-3 rounded-xl hover:from-blue-700 hover:to-blue-900 transition-all font-semibold shadow-md"
            >
              {t('forum.search')}
            </button>
          </form>
          {user && (
            <button
              onClick={() => setIsCreateModalOpen(true)}
              className="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-6 py-3 rounded-xl hover:from-blue-700 hover:to-blue-900 transition-all font-semibold shadow-md flex items-center gap-2 whitespace-nowrap"
            >
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
              </svg>
              {t('forum.createTopic')}
            </button>
          )}
        </div>

        {/* Topics List */}
        <div className="space-y-4">
          {topics.map((topic) => (
            <div
              key={topic.id}
              onClick={() => navigate(`/forum/topic/${topic.id}`)}
              className="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 border-2 border-transparent hover:border-blue-400 cursor-pointer group"
            >
              <div className="flex items-start gap-4">
                {/* Avatar */}
                <div className="flex-shrink-0">
                  <div className="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg">
                    {topic.user?.name?.charAt(0).toUpperCase() || 'U'}
                  </div>
                </div>

                {/* Content */}
                <div className="flex-1 min-w-0">
                  <div className="flex items-start justify-between gap-4 mb-2">
                    <div className="flex-1">
                      <div className="flex items-center gap-2 mb-2">
                        {topic.is_pinned && (
                          <span className="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-lg text-xs font-semibold flex items-center gap-1">
                            <svg className="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                              <path d="M5.5 17.5a.5.5 0 01-1 0v-15a.5.5 0 011 0v15zM9.293 10.707a1 1 0 011.414 0L12 11.586l1.293-.879a1 1 0 011.414 0l2 2a1 1 0 01-1.414 1.414L13.414 13l-1.293.879a1 1 0 01-1.414 0L10 13.414l-1.293.879a1 1 0 01-1.414-1.414l2-2z" />
                            </svg>
                            {t('forum.pinned')}
                          </span>
                        )}
                        {topic.is_locked && (
                          <span className="bg-red-100 text-red-700 px-2 py-1 rounded-lg text-xs font-semibold flex items-center gap-1">
                            <svg className="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                              <path fillRule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clipRule="evenodd" />
                            </svg>
                            {t('forum.locked')}
                          </span>
                        )}
                      </div>
                      <h3 className="text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors mb-2 line-clamp-2">
                        {topic.title}
                      </h3>
                      <div className="flex items-center gap-3 text-sm text-gray-500 mb-3">
                        <span className="flex items-center gap-1">
                          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                          </svg>
                          {topic.user?.name}
                        </span>
                        <span>•</span>
                        <span>{formatDate(topic.created_at)}</span>
                      </div>
                    </div>

                    {/* Stats */}
                    <div className="flex-shrink-0 flex items-center gap-4">
                      <div className="text-center">
                        <div className="text-lg font-bold text-gray-900">{topic.posts_count || 0}</div>
                        <div className="text-xs text-gray-500">{t('forum.replies')}</div>
                      </div>
                      <div className="text-center">
                        <div className="text-lg font-bold text-gray-900">{topic.views_count || 0}</div>
                        <div className="text-xs text-gray-500">{t('forum.views')}</div>
                      </div>
                      <div className="text-blue-600 opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0 transition-all duration-300">
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                        </svg>
                      </div>
                    </div>
                  </div>

                  {/* Last Reply */}
                  {topic.last_reply_at && (
                    <div className="flex items-center gap-2 text-sm text-gray-500 pt-3 border-t border-gray-100">
                      <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span>{t('forum.lastReply')}: {formatDate(topic.last_reply_at)}</span>
                    </div>
                  )}
                </div>
              </div>
            </div>
          ))}
        </div>

        {topics.length === 0 && !loading && (
          <div className="text-center py-16 bg-white rounded-2xl shadow-lg border-2 border-gray-200 mt-6">
            <div className="text-6xl mb-4">💭</div>
            <p className="text-gray-600 text-lg mb-4">{t('forum.noTopics')}</p>
            {user && (
              <button
                onClick={() => setIsCreateModalOpen(true)}
                className="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-blue-900 transition-all font-semibold shadow-md"
              >
                {t('forum.createFirstTopic')}
              </button>
            )}
          </div>
        )}

        {/* Pagination */}
        {pagination.last_page > 1 && (
          <div className="mt-8 flex justify-center gap-2">
            {Array.from({ length: pagination.last_page }, (_, i) => i + 1).map((page) => (
              <button
                key={page}
                onClick={() => fetchTopics(page)}
                className={`px-5 py-2.5 rounded-xl font-semibold transition-all shadow-md ${
                  page === pagination.current_page
                    ? 'bg-gradient-to-r from-blue-600 to-blue-800 text-white shadow-lg scale-105'
                    : 'bg-white text-gray-700 hover:bg-gray-50 border-2 border-gray-300 hover:border-blue-400 hover:shadow-lg'
                }`}
              >
                {page}
              </button>
            ))}
          </div>
        )}

        {/* Create Topic Modal */}
        {isCreateModalOpen && (
          <CreateTopicModal
            isOpen={isCreateModalOpen}
            onClose={() => setIsCreateModalOpen(false)}
            categoryId={id}
            onSuccess={handleTopicCreated}
          />
        )}
      </div>
    </div>
  );
};

export default CategoryView;

