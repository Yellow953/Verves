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
    <div className="min-h-screen bg-gray-50 pt-24 pb-16">
      <div className="container mx-auto px-6">
        {/* Header */}
        <div className="mb-6 flex items-center justify-between">
          <div>
            <Link to="/forum" className="text-blue-600 hover:text-blue-700 mb-2 inline-block">
              ← {t('forum.backToForum')}
            </Link>
            {category && (
              <>
                <h1 className="text-4xl font-bold text-gray-900 mb-2">{category.name}</h1>
                {category.description && (
                  <p className="text-gray-600">{category.description}</p>
                )}
              </>
            )}
          </div>
          {user && (
            <button
              onClick={() => setIsCreateModalOpen(true)}
              className="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-6 py-3 rounded-lg hover:from-blue-700 hover:to-blue-900 transition-all font-semibold shadow-md"
            >
              {t('forum.createTopic')}
            </button>
          )}
        </div>

        {/* Search */}
        <div className="mb-6">
          <form onSubmit={handleSearch} className="flex gap-2">
            <input
              type="text"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              placeholder={t('forum.searchTopics')}
              className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <button
              type="submit"
              className="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-6 py-2 rounded-lg hover:from-blue-700 hover:to-blue-900 transition-all font-semibold"
            >
              {t('forum.search')}
            </button>
          </form>
        </div>

        {/* Topics List */}
        <div className="bg-white rounded-xl shadow-md overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-4 text-left text-sm font-semibold text-gray-900">{t('forum.topic')}</th>
                  <th className="px-6 py-4 text-center text-sm font-semibold text-gray-900">{t('forum.replies')}</th>
                  <th className="px-6 py-4 text-center text-sm font-semibold text-gray-900">{t('forum.views')}</th>
                  <th className="px-6 py-4 text-left text-sm font-semibold text-gray-900">{t('forum.lastReply')}</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {topics.map((topic) => (
                  <tr
                    key={topic.id}
                    className="hover:bg-gray-50 cursor-pointer"
                    onClick={() => navigate(`/forum/topic/${topic.id}`)}
                  >
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        {topic.is_pinned && (
                          <span className="text-yellow-500" title={t('forum.pinned')}>
                            📌
                          </span>
                        )}
                        {topic.is_locked && (
                          <span className="text-red-500" title={t('forum.locked')}>
                            🔒
                          </span>
                        )}
                        <div>
                          <h3 className="font-semibold text-gray-900 hover:text-blue-600">
                            {topic.title}
                          </h3>
                          <p className="text-sm text-gray-500">
                            {t('forum.by')} {topic.user?.name} • {formatDate(topic.created_at)}
                          </p>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4 text-center text-gray-600">
                      {topic.posts_count || 0}
                    </td>
                    <td className="px-6 py-4 text-center text-gray-600">
                      {topic.views_count || 0}
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      {topic.last_reply_at ? formatDate(topic.last_reply_at) : t('forum.noReplies')}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

        {topics.length === 0 && !loading && (
          <div className="text-center py-12 bg-white rounded-xl shadow-md mt-6">
            <p className="text-gray-600">{t('forum.noTopics')}</p>
            {user && (
              <button
                onClick={() => setIsCreateModalOpen(true)}
                className="mt-4 bg-gradient-to-r from-blue-600 to-blue-800 text-white px-6 py-2 rounded-lg hover:from-blue-700 hover:to-blue-900 transition-all font-semibold"
              >
                {t('forum.createFirstTopic')}
              </button>
            )}
          </div>
        )}

        {/* Pagination */}
        {pagination.last_page > 1 && (
          <div className="mt-6 flex justify-center gap-2">
            {Array.from({ length: pagination.last_page }, (_, i) => i + 1).map((page) => (
              <button
                key={page}
                onClick={() => fetchTopics(page)}
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

