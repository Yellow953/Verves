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
    <div className="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-gray-50 pt-24 pb-16">
      <div className="container mx-auto px-6 max-w-5xl">
        {/* Breadcrumb */}
        <div className="mb-6">
          <div className="flex items-center gap-2 text-sm">
            <Link to="/forum" className="text-blue-600 hover:text-blue-700 font-medium transition-colors">
              {t('forum.forum')}
            </Link>
            <svg className="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
            </svg>
            <Link to={`/forum/category/${topic.category?.id}`} className="text-blue-600 hover:text-blue-700 font-medium transition-colors">
              {topic.category?.name}
            </Link>
            <svg className="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
            </svg>
            <span className="text-gray-600 truncate">{topic.title}</span>
          </div>
        </div>

        {/* Topic Header */}
        <div className="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl shadow-xl p-8 mb-6 text-white">
          <div className="flex items-start justify-between mb-4">
            <div className="flex-1">
              <div className="flex items-center gap-3 mb-4">
                {topic.is_pinned && (
                  <span className="bg-yellow-400 text-yellow-900 px-3 py-1 rounded-lg text-sm font-semibold flex items-center gap-1">
                    <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M5.5 17.5a.5.5 0 01-1 0v-15a.5.5 0 011 0v15zM9.293 10.707a1 1 0 011.414 0L12 11.586l1.293-.879a1 1 0 011.414 0l2 2a1 1 0 01-1.414 1.414L13.414 13l-1.293.879a1 1 0 01-1.414 0L10 13.414l-1.293.879a1 1 0 01-1.414-1.414l2-2z" />
                    </svg>
                    {t('forum.pinned')}
                  </span>
                )}
                {topic.is_locked && (
                  <span className="bg-red-400 text-red-900 px-3 py-1 rounded-lg text-sm font-semibold flex items-center gap-1">
                    <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fillRule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clipRule="evenodd" />
                    </svg>
                    {t('forum.locked')}
                  </span>
                )}
              </div>
              <h1 className="text-4xl font-bold mb-4">{topic.title}</h1>
              <div className="flex flex-wrap items-center gap-4 text-blue-100">
                <span className="flex items-center gap-2">
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  <strong>{topic.user?.name}</strong>
                </span>
                <span>•</span>
                <span className="flex items-center gap-2">
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {formatDate(topic.created_at)}
                </span>
                <span>•</span>
                <span className="flex items-center gap-2">
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                  {topic.posts_count || 0} {t('forum.replies')}
                </span>
                <span>•</span>
                <span className="flex items-center gap-2">
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  {topic.views_count || 0} {t('forum.views')}
                </span>
              </div>
            </div>
            {isTopicOwner && (
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
                className="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-md"
              >
                {t('forum.delete')}
              </button>
            )}
          </div>
        </div>

        {/* Posts */}
        <div className="space-y-6 mb-6">
          {posts.map((post, index) => {
            const isPostOwner = user && user.id === post.user_id;
            const isFirstPost = post.is_first_post;
            return (
              <div 
                key={post.id} 
                className={`bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-2 ${
                  isFirstPost ? 'border-blue-300 bg-gradient-to-br from-blue-50/50 to-white' : 'border-transparent hover:border-blue-200'
                }`}
              >
                <div className="flex items-start gap-4">
                  {/* Avatar */}
                  <div className="flex-shrink-0">
                    <div className={`w-16 h-16 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg ${
                      isFirstPost 
                        ? 'bg-gradient-to-br from-blue-600 to-blue-800' 
                        : 'bg-gradient-to-br from-gray-500 to-gray-700'
                    }`}>
                      {post.user?.name?.charAt(0).toUpperCase() || 'U'}
                    </div>
                    {isFirstPost && (
                      <div className="mt-2 text-center">
                        <span className="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded-lg">
                          {t('forum.originalPost')}
                        </span>
                      </div>
                    )}
                  </div>

                  {/* Content */}
                  <div className="flex-1 min-w-0">
                    <div className="flex items-start justify-between mb-3">
                      <div>
                        <div className="flex items-center gap-2 mb-1">
                          <h3 className="font-bold text-gray-900 text-lg">{post.user?.name}</h3>
                          {isFirstPost && (
                            <span className="bg-blue-600 text-white text-xs font-semibold px-2 py-0.5 rounded">
                              OP
                            </span>
                          )}
                        </div>
                        <div className="flex items-center gap-2 text-sm text-gray-500">
                          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                          <span>{formatDate(post.created_at)}</span>
                          {post.updated_at !== post.created_at && (
                            <span className="text-xs bg-gray-100 px-2 py-0.5 rounded">({t('forum.edited')})</span>
                          )}
                        </div>
                      </div>
                      {isPostOwner && !post.is_first_post && (
                        <button
                          onClick={() => handleDeletePost(post.id)}
                          className="text-red-600 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors"
                        >
                          <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                        </button>
                      )}
                    </div>
                    <div className="prose max-w-none text-gray-700 whitespace-pre-wrap leading-relaxed">
                      {post.body}
                    </div>
                  </div>
                </div>
              </div>
            );
          })}
        </div>

        {/* Reply Form */}
        {canReply ? (
          <div className="bg-white rounded-2xl shadow-xl p-8 border-2 border-blue-200">
            <div className="flex items-center gap-3 mb-6">
              <div className="w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-800 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg">
                {user?.name?.charAt(0).toUpperCase() || 'U'}
              </div>
              <div>
                <h2 className="text-2xl font-bold text-gray-900">{t('forum.reply')}</h2>
                <p className="text-sm text-gray-500">Share your thoughts and join the discussion</p>
              </div>
            </div>
            <CreatePostForm topicId={id} onSuccess={handlePostCreated} />
          </div>
        ) : topic.is_locked ? (
          <div className="bg-gradient-to-r from-yellow-50 to-yellow-100 border-2 border-yellow-300 rounded-2xl p-8 text-center shadow-lg">
            <div className="text-5xl mb-4">🔒</div>
            <p className="text-yellow-900 font-semibold text-lg">{t('forum.topicLocked')}</p>
          </div>
        ) : (
          <div className="bg-gradient-to-r from-blue-50 to-blue-100 border-2 border-blue-300 rounded-2xl p-8 text-center shadow-lg">
            <div className="text-5xl mb-4">💬</div>
            <p className="text-blue-900 font-semibold text-lg mb-4">{t('forum.loginToReply')}</p>
            <Link
              to="/"
              className="inline-block bg-gradient-to-r from-blue-600 to-blue-800 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-blue-900 transition-all font-semibold shadow-md"
            >
              {t('auth.login')}
            </Link>
          </div>
        )}

        {/* Pagination */}
        {pagination.last_page > 1 && (
          <div className="mt-8 flex justify-center gap-2">
            {Array.from({ length: pagination.last_page }, (_, i) => i + 1).map((page) => (
              <button
                key={page}
                onClick={() => fetchPosts(page)}
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
      </div>
    </div>
  );
};

export default TopicView;

