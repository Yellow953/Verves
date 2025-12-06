import React, { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { forumAPI } from '../../services/api';
import CreatePostForm from './CreatePostForm';

const TopicView = () => {
  const { t } = useTranslation();
  const { id } = useParams();
  const navigate = useNavigate();
  const [topic, setTopic] = useState(null);
  const [posts, setPosts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
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
    fetchTopic();
    fetchPosts();
  }, [id]);

  const fetchTopic = async () => {
    try {
      const response = await forumAPI.getTopic(id);
      if (response.data.success) {
        setTopic(response.data.data);
      }
    } catch (err) {
      setError(err.response?.data?.message || t('forum.error.loading'));
    }
  };

  const fetchPosts = async (page = 1) => {
    try {
      setLoading(true);
      const response = await forumAPI.getPosts({
        topic_id: id,
        per_page: 15,
        page,
      });
      if (response.data.success) {
        setPosts(response.data.data.data || []);
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

  const handlePostCreated = () => {
    fetchPosts();
    fetchTopic();
  };

  const handleDeletePost = async (postId) => {
    if (!window.confirm(t('forum.confirmDeletePost'))) return;
    
    try {
      await forumAPI.deletePost(postId);
      fetchPosts();
      fetchTopic();
    } catch (err) {
      alert(err.response?.data?.message || t('forum.error.deleting'));
    }
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  if (loading && !topic) {
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

  if (error || !topic) {
    return (
      <div className="min-h-screen bg-gray-50 pt-24 pb-16">
        <div className="container mx-auto px-6">
          <div className="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
            <p className="text-red-600">{error || t('forum.error.notFound')}</p>
            <Link to="/forum" className="text-blue-600 hover:text-blue-700 mt-2 inline-block">
              {t('forum.backToForum')}
            </Link>
          </div>
        </div>
      </div>
    );
  }

  const canReply = user && !topic.is_locked;
  const isTopicOwner = user && user.id === topic.user_id;

  return (
    <div className="min-h-screen bg-gray-50 pt-24 pb-16">
      <div className="container mx-auto px-6 max-w-5xl">
        {/* Breadcrumb */}
        <div className="mb-6">
          <Link to="/forum" className="text-blue-600 hover:text-blue-700">
            {t('forum.forum')}
          </Link>
          {' / '}
          <Link to={`/forum/category/${topic.category?.id}`} className="text-blue-600 hover:text-blue-700">
            {topic.category?.name}
          </Link>
          {' / '}
          <span className="text-gray-600">{topic.title}</span>
        </div>

        {/* Topic Header */}
        <div className="bg-white rounded-xl shadow-md p-6 mb-6">
          <div className="flex items-start justify-between mb-4">
            <div className="flex-1">
              <div className="flex items-center gap-3 mb-2">
                {topic.is_pinned && (
                  <span className="text-yellow-500 text-lg" title={t('forum.pinned')}>
                    📌
                  </span>
                )}
                {topic.is_locked && (
                  <span className="text-red-500 text-lg" title={t('forum.locked')}>
                    🔒
                  </span>
                )}
                <h1 className="text-3xl font-bold text-gray-900">{topic.title}</h1>
              </div>
              <div className="flex items-center gap-4 text-sm text-gray-600">
                <span>{t('forum.by')} <strong>{topic.user?.name}</strong></span>
                <span>•</span>
                <span>{formatDate(topic.created_at)}</span>
                <span>•</span>
                <span>{topic.posts_count || 0} {t('forum.replies')}</span>
                <span>•</span>
                <span>{topic.views_count || 0} {t('forum.views')}</span>
              </div>
            </div>
            {isTopicOwner && (
              <div className="flex gap-2">
                <button
                  onClick={async () => {
                    if (window.confirm(t('forum.confirmDeleteTopic'))) {
                      try {
                        await forumAPI.deleteTopic(id);
                        navigate(`/forum/category/${topic.category?.id}`);
                      } catch (err) {
                        alert(err.response?.data?.message || t('forum.error.deleting'));
                      }
                    }
                  }}
                  className="text-red-600 hover:text-red-700 text-sm"
                >
                  {t('forum.delete')}
                </button>
              </div>
            )}
          </div>
        </div>

        {/* Posts */}
        <div className="space-y-4 mb-6">
          {posts.map((post, index) => {
            const isPostOwner = user && user.id === post.user_id;
            return (
              <div key={post.id} className="bg-white rounded-xl shadow-md p-6">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <div className="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-700 rounded-full flex items-center justify-center text-white font-bold">
                      {post.user?.name?.charAt(0).toUpperCase() || 'U'}
                    </div>
                  </div>
                  <div className="flex-1">
                    <div className="flex items-center justify-between mb-2">
                      <div>
                        <h3 className="font-semibold text-gray-900">{post.user?.name}</h3>
                        <p className="text-sm text-gray-500">
                          {formatDate(post.created_at)}
                          {post.updated_at !== post.created_at && (
                            <span className="ml-2 text-xs">({t('forum.edited')})</span>
                          )}
                        </p>
                      </div>
                      {isPostOwner && !post.is_first_post && (
                        <div className="flex gap-2">
                          <button
                            onClick={() => handleDeletePost(post.id)}
                            className="text-red-600 hover:text-red-700 text-sm"
                          >
                            {t('forum.delete')}
                          </button>
                        </div>
                      )}
                    </div>
                    <div className="prose max-w-none text-gray-700 whitespace-pre-wrap">
                      {post.body}
                    </div>
                    {post.is_first_post && (
                      <span className="inline-block mt-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">
                        {t('forum.originalPost')}
                      </span>
                    )}
                  </div>
                </div>
              </div>
            );
          })}
        </div>

        {/* Reply Form */}
        {canReply ? (
          <div className="bg-white rounded-xl shadow-md p-6">
            <h2 className="text-xl font-bold text-gray-900 mb-4">{t('forum.reply')}</h2>
            <CreatePostForm topicId={id} onSuccess={handlePostCreated} />
          </div>
        ) : topic.is_locked ? (
          <div className="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
            <p className="text-yellow-800">{t('forum.topicLocked')}</p>
          </div>
        ) : (
          <div className="bg-blue-50 border border-blue-200 rounded-xl p-6 text-center">
            <p className="text-blue-800 mb-2">{t('forum.loginToReply')}</p>
            <Link
              to="/"
              className="text-blue-600 hover:text-blue-700 font-semibold"
            >
              {t('auth.login')}
            </Link>
          </div>
        )}

        {/* Pagination */}
        {pagination.last_page > 1 && (
          <div className="mt-6 flex justify-center gap-2">
            {Array.from({ length: pagination.last_page }, (_, i) => i + 1).map((page) => (
              <button
                key={page}
                onClick={() => fetchPosts(page)}
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
      </div>
    </div>
  );
};

export default TopicView;

