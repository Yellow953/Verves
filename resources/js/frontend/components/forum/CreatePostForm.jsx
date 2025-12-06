import React, { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { forumAPI } from '../../services/api';

const CreatePostForm = ({ topicId, onSuccess }) => {
  const { t } = useTranslation();
  const [body, setBody] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError(null);
    setLoading(true);

    try {
      const response = await forumAPI.createPost({
        topic_id: topicId,
        body: body.trim(),
      });
      if (response.data.success) {
        setBody('');
        onSuccess();
      }
    } catch (err) {
      setError(err.response?.data?.message || t('forum.error.creating'));
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      {error && (
        <div className="mb-4 bg-red-50 border border-red-200 rounded-lg p-3 text-red-600 text-sm">
          {error}
        </div>
      )}

      <div className="mb-4">
        <textarea
          value={body}
          onChange={(e) => setBody(e.target.value)}
          required
          rows={6}
          placeholder={t('forum.replyPlaceholder')}
          className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
        />
      </div>

      <div className="flex gap-3">
        <button
          type="submit"
          disabled={loading || !body.trim()}
          className="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-6 py-2 rounded-lg hover:from-blue-700 hover:to-blue-900 transition-all font-semibold shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {loading ? t('forum.posting') : t('forum.postReply')}
        </button>
        <button
          type="button"
          onClick={() => setBody('')}
          className="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
        >
          {t('forum.clear')}
        </button>
      </div>
    </form>
  );
};

export default CreatePostForm;

