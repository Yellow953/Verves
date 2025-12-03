import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { coachesAPI, bookingsAPI } from '../../services/api';
import Header from '../layout/Header';
import Footer from '../layout/Footer';
import AuthModal from '../auth/AuthModal';

const BookSession = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const { t } = useTranslation();

  const [coach, setCoach] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [selectedDate, setSelectedDate] = useState(null);
  const [availableSlots, setAvailableSlots] = useState([]);
  const [loadingSlots, setLoadingSlots] = useState(false);
  const [selectedTime, setSelectedTime] = useState(null);
  const [duration, setDuration] = useState(60);
  const [sessionType, setSessionType] = useState('in_person');
  const [notes, setNotes] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [showAuthModal, setShowAuthModal] = useState(false);
  const [user, setUser] = useState(null);

  // Calendar state
  const [currentMonth, setCurrentMonth] = useState(new Date());
  const [calendarDays, setCalendarDays] = useState([]);

  useEffect(() => {
    loadCoach();
    checkAuth();
  }, [id]);

  const checkAuth = () => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      // Optionally verify token by fetching user
      setUser({ authenticated: true });
    }
  };

  useEffect(() => {
    if (selectedDate) {
      loadAvailableSlots(selectedDate);
    } else {
      setAvailableSlots([]);
      setSelectedTime(null);
    }
  }, [selectedDate, id]);

  useEffect(() => {
    generateCalendar();
  }, [currentMonth]);

  const loadCoach = async () => {
    try {
      setLoading(true);
      const response = await coachesAPI.get(id);
      setCoach(response.data.data);
      setError(null);
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to load coach information');
    } finally {
      setLoading(false);
    }
  };

  const loadAvailableSlots = async (date) => {
    try {
      setLoadingSlots(true);
      const response = await coachesAPI.getAvailableSlots(id, { date });
      setAvailableSlots(response.data.data.available_slots || []);
      setSelectedTime(null);
    } catch (err) {
      setAvailableSlots([]);
      console.error('Failed to load available slots:', err);
    } finally {
      setLoadingSlots(false);
    }
  };

  const generateCalendar = () => {
    const year = currentMonth.getFullYear();
    const month = currentMonth.getMonth();
    
    // First day of the month
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    
    // Start from the first day of the week (Sunday = 0)
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - startDate.getDay());
    
    // End on the last day of the week
    const endDate = new Date(lastDay);
    const daysToAdd = 6 - endDate.getDay();
    endDate.setDate(endDate.getDate() + daysToAdd);
    
    const days = [];
    const current = new Date(startDate);
    
    while (current <= endDate) {
      days.push(new Date(current));
      current.setDate(current.getDate() + 1);
    }
    
    setCalendarDays(days);
  };

  const isToday = (date) => {
    const today = new Date();
    return date.toDateString() === today.toDateString();
  };

  const isPast = (date) => {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return date < today;
  };

  const isSelected = (date) => {
    if (!selectedDate) return false;
    return date.toDateString() === new Date(selectedDate).toDateString();
  };

  const handleDateSelect = (date) => {
    if (isPast(date)) return;
    const dateStr = date.toISOString().split('T')[0];
    setSelectedDate(dateStr);
  };

  const handlePreviousMonth = () => {
    setCurrentMonth(new Date(currentMonth.getFullYear(), currentMonth.getMonth() - 1, 1));
  };

  const handleNextMonth = () => {
    setCurrentMonth(new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    if (!selectedDate || !selectedTime) {
      setError('Please select both date and time');
      return;
    }

    const token = localStorage.getItem('auth_token');
    if (!token) {
      setShowAuthModal(true);
      setError(t('auth.error'));
      return;
    }

    try {
      setSubmitting(true);
      setError(null);

      const sessionDateTime = new Date(`${selectedDate} ${selectedTime}`);
      
      const bookingData = {
        coach_id: parseInt(id),
        session_date: sessionDateTime.toISOString(),
        duration_minutes: duration,
        session_type: sessionType,
        notes: notes || null,
      };

      const response = await bookingsAPI.create(bookingData);
      
      if (response.data.success) {
        // Success - redirect or show success message
        alert(t('booking.bookingSuccess'));
        navigate('/');
      }
    } catch (err) {
      if (err.response?.status === 401) {
        setShowAuthModal(true);
        setError('Please login to book a session');
      } else {
        setError(err.response?.data?.message || t('booking.bookingError'));
      }
    } finally {
      setSubmitting(false);
    }
  };

  const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];

  const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

  if (loading) {
    return (
      <div className="min-h-screen bg-white">
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

  if (error && !coach) {
    return (
      <div className="min-h-screen bg-white">
        <Header />
        <div className="container mx-auto px-6 py-20">
          <div className="text-center">
            <p className="text-red-600">{error}</p>
            <button
              onClick={() => navigate('/')}
              className="mt-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-lg"
            >
              {t('common.back')}
            </button>
          </div>
        </div>
        <Footer />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-white">
      <Header />
      <div className="container mx-auto px-6 py-12">
        <div className="max-w-6xl mx-auto">
          {/* Coach Info */}
          <div className="bg-white rounded-xl shadow-lg p-8 mb-8 border border-gray-200">
            <div className="flex flex-col md:flex-row items-start md:items-center gap-6">
              <div className="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-4xl text-white">
                {coach?.name?.charAt(0) || '👨‍💼'}
              </div>
              <div className="flex-1">
                <h1 className="text-3xl font-bold text-gray-900 mb-2">{coach?.name}</h1>
                {coach?.specialization && (
                  <p className="text-blue-600 font-semibold mb-2">{coach.specialization}</p>
                )}
                {coach?.bio && (
                  <p className="text-gray-600">{coach.bio}</p>
                )}
              </div>
            </div>
          </div>

          {/* Booking Form */}
          <div className="grid md:grid-cols-2 gap-8">
            {/* Calendar */}
            <div className="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
              <h2 className="text-2xl font-bold text-gray-900 mb-6">{t('booking.selectDate')}</h2>
              
              {/* Calendar Header */}
              <div className="flex items-center justify-between mb-4">
                <button
                  onClick={handlePreviousMonth}
                  className="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                  </svg>
                </button>
                <h3 className="text-lg font-semibold text-gray-900">
                  {monthNames[currentMonth.getMonth()]} {currentMonth.getFullYear()}
                </h3>
                <button
                  onClick={handleNextMonth}
                  className="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                  </svg>
                </button>
              </div>

              {/* Day Names */}
              <div className="grid grid-cols-7 gap-2 mb-2">
                {dayNames.map((day) => (
                  <div key={day} className="text-center text-sm font-semibold text-gray-600 py-2">
                    {day}
                  </div>
                ))}
              </div>

              {/* Calendar Days */}
              <div className="grid grid-cols-7 gap-2">
                {calendarDays.map((date, index) => {
                  const isCurrentMonth = date.getMonth() === currentMonth.getMonth();
                  const isPastDate = isPast(date);
                  const isSelectedDate = isSelected(date);
                  const isTodayDate = isToday(date);

                  return (
                    <button
                      key={index}
                      onClick={() => handleDateSelect(date)}
                      disabled={isPastDate || !isCurrentMonth}
                      className={`
                        aspect-square p-2 rounded-lg text-sm font-medium transition-all
                        ${!isCurrentMonth ? 'text-gray-300' : ''}
                        ${isPastDate ? 'text-gray-300 cursor-not-allowed' : 'hover:bg-blue-50 cursor-pointer'}
                        ${isTodayDate && !isSelectedDate ? 'bg-blue-50 text-blue-600 border-2 border-blue-200' : ''}
                        ${isSelectedDate ? 'bg-gradient-to-br from-blue-600 to-purple-600 text-white shadow-lg' : 'text-gray-700'}
                      `}
                    >
                      {date.getDate()}
                    </button>
                  );
                })}
              </div>
            </div>

            {/* Time Slots & Form */}
            <div className="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
              <h2 className="text-2xl font-bold text-gray-900 mb-6">{t('booking.selectTime')}</h2>

              {!selectedDate ? (
                <p className="text-gray-500 text-center py-8">
                  {t('booking.selectDate')} to see available time slots
                </p>
              ) : (
                <>
                  {/* Time Slots */}
                  {loadingSlots ? (
                    <p className="text-gray-500 text-center py-8">{t('booking.loadingSlots')}</p>
                  ) : availableSlots.length === 0 ? (
                    <p className="text-gray-500 text-center py-8">{t('booking.noSlotsAvailable')}</p>
                  ) : (
                    <div className="mb-6">
                      <div className="grid grid-cols-3 gap-3 mb-6">
                        {availableSlots.map((slot, index) => (
                          <button
                            key={index}
                            onClick={() => setSelectedTime(slot.time)}
                            className={`
                              px-4 py-3 rounded-lg font-medium transition-all
                              ${selectedTime === slot.time
                                ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg'
                                : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200'
                              }
                            `}
                          >
                            {slot.time}
                          </button>
                        ))}
                      </div>
                    </div>
                  )}

                  {/* Booking Form */}
                  {selectedTime && (
                    <form onSubmit={handleSubmit} className="space-y-4">
                      {/* Duration */}
                      <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                          {t('booking.duration')} ({t('booking.minutes')})
                        </label>
                        <select
                          value={duration}
                          onChange={(e) => setDuration(parseInt(e.target.value))}
                          className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                          <option value={30}>30 {t('booking.minutes')}</option>
                          <option value={60}>60 {t('booking.minutes')}</option>
                          <option value={90}>90 {t('booking.minutes')}</option>
                          <option value={120}>120 {t('booking.minutes')}</option>
                        </select>
                      </div>

                      {/* Session Type */}
                      <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                          {t('booking.sessionType')}
                        </label>
                        <select
                          value={sessionType}
                          onChange={(e) => setSessionType(e.target.value)}
                          className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                          <option value="in_person">{t('booking.inPerson')}</option>
                          <option value="online">{t('booking.online')}</option>
                          <option value="hybrid">{t('booking.hybrid')}</option>
                        </select>
                      </div>

                      {/* Notes */}
                      <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                          {t('booking.notes')}
                        </label>
                        <textarea
                          value={notes}
                          onChange={(e) => setNotes(e.target.value)}
                          placeholder={t('booking.notesPlaceholder')}
                          rows={3}
                          className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                      </div>

                      {/* Error Message */}
                      {error && (
                        <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                          {error}
                        </div>
                      )}

                      {/* Submit Button */}
                      <button
                        type="submit"
                        disabled={submitting}
                        className="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                      >
                        {submitting ? t('common.loading') : t('booking.confirmBooking')}
                      </button>
                    </form>
                  )}
                </>
              )}
            </div>
          </div>
        </div>
      </div>
      <Footer />
      <AuthModal 
        isOpen={showAuthModal} 
        onClose={() => {
          setShowAuthModal(false);
          checkAuth();
        }} 
        initialMode="login"
      />
    </div>
  );
};

export default BookSession;

