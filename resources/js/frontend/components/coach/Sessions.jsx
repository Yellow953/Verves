import React, { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { bookingsAPI } from '../../services/api';

const Sessions = () => {
  const { t } = useTranslation();
  const [bookings, setBookings] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [filter, setFilter] = useState('upcoming'); // 'upcoming', 'past', 'all'

  useEffect(() => {
    loadBookings();
  }, [filter]);

  const loadBookings = async () => {
    try {
      setLoading(true);
      setError(null);
      const params = {};
      if (filter === 'upcoming') {
        params.upcoming = true;
      } else if (filter === 'past') {
        params.start_date = new Date().toISOString().split('T')[0];
      }
      const response = await bookingsAPI.list(params);
      setBookings(response.data.data.data || []);
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to load sessions');
    } finally {
      setLoading(false);
    }
  };

  const handleStatusUpdate = async (bookingId, newStatus) => {
    try {
      await bookingsAPI.update(bookingId, { status: newStatus });
      loadBookings();
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to update session');
    }
  };

  const handleCancel = async (bookingId) => {
    if (!confirm(t('coach.dashboard.confirmCancel'))) return;
    try {
      await bookingsAPI.cancel(bookingId, 'Cancelled by coach');
      loadBookings();
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to cancel session');
    }
  };

  const getStatusColor = (status) => {
    const colors = {
      pending: 'bg-yellow-100 text-yellow-800',
      confirmed: 'bg-green-100 text-green-800',
      completed: 'bg-blue-100 text-blue-800',
      cancelled: 'bg-red-100 text-red-800',
      no_show: 'bg-gray-100 text-gray-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
  };

  if (loading) {
    return <div className="text-center py-8 text-gray-600">{t('common.loading')}</div>;
  }

  return (
    <div>
      <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 className="text-2xl font-bold text-gray-900">{t('coach.dashboard.mySessions')}</h2>
        <div className="flex gap-2">
          <button
            onClick={() => setFilter('upcoming')}
            className={`px-4 py-2 rounded-lg font-medium transition-colors ${
              filter === 'upcoming'
                ? 'bg-blue-600 text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            }`}
          >
            {t('coach.dashboard.upcoming')}
          </button>
          <button
            onClick={() => setFilter('past')}
            className={`px-4 py-2 rounded-lg font-medium transition-colors ${
              filter === 'past'
                ? 'bg-blue-600 text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            }`}
          >
            {t('coach.dashboard.past')}
          </button>
          <button
            onClick={() => setFilter('all')}
            className={`px-4 py-2 rounded-lg font-medium transition-colors ${
              filter === 'all'
                ? 'bg-blue-600 text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            }`}
          >
            {t('coach.dashboard.all')}
          </button>
        </div>
      </div>

      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
          {error}
        </div>
      )}

      {bookings.length === 0 ? (
        <div className="text-center py-12">
          <p className="text-gray-500">{t('coach.dashboard.noSessions')}</p>
        </div>
      ) : (
        <div className="space-y-4">
          {bookings.map((booking) => (
            <div
              key={booking.id}
              className="bg-gray-50 rounded-lg p-6 border border-gray-200 hover:shadow-md transition-shadow"
            >
              <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div className="flex-1">
                  <div className="flex items-center gap-4 mb-2">
                    <h3 className="text-lg font-semibold text-gray-900">
                      {booking.client?.name || 'Client'}
                    </h3>
                    <span className={`px-3 py-1 rounded-full text-xs font-semibold ${getStatusColor(booking.status)}`}>
                      {t(`booking.${booking.status}`)}
                    </span>
                  </div>
                  <div className="grid md:grid-cols-2 gap-4 text-sm text-gray-600">
                    <div>
                      <span className="font-medium">{t('booking.sessionDate')}:</span>{' '}
                      {new Date(booking.session_date).toLocaleString()}
                    </div>
                    <div>
                      <span className="font-medium">{t('booking.duration')}:</span>{' '}
                      {booking.duration_minutes || 60} {t('booking.minutes')}
                    </div>
                    {booking.session_type && (
                      <div>
                        <span className="font-medium">{t('booking.sessionType')}:</span>{' '}
                        {t(`booking.${booking.session_type}`)}
                      </div>
                    )}
                    {booking.location && (
                      <div>
                        <span className="font-medium">{t('booking.location')}:</span> {booking.location}
                      </div>
                    )}
                  </div>
                  {booking.notes && (
                    <div className="mt-2 text-sm text-gray-600">
                      <span className="font-medium">{t('booking.notes')}:</span> {booking.notes}
                    </div>
                  )}
                </div>
                <div className="flex flex-col gap-2">
                  {booking.status === 'pending' && (
                    <>
                      <button
                        onClick={() => handleStatusUpdate(booking.id, 'confirmed')}
                        className="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors"
                      >
                        {t('coach.dashboard.confirm')}
                      </button>
                      <button
                        onClick={() => handleCancel(booking.id)}
                        className="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors"
                      >
                        {t('coach.dashboard.cancel')}
                      </button>
                    </>
                  )}
                  {booking.status === 'confirmed' && (
                    <button
                      onClick={() => handleStatusUpdate(booking.id, 'completed')}
                      className="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors"
                    >
                      {t('coach.dashboard.markComplete')}
                    </button>
                  )}
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default Sessions;






