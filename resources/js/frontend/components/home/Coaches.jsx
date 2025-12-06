import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { coachesAPI } from '../../services/api';

const Coaches = () => {
  const { t } = useTranslation();
  const [coaches, setCoaches] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadCoaches();
  }, []);

  const loadCoaches = async () => {
    try {
      setLoading(true);
      const response = await coachesAPI.list({ per_page: 8 });
      setCoaches(response.data.data.data || []);
    } catch (err) {
      console.error('Failed to load coaches:', err);
      // Fallback to sample data if API fails
      setCoaches([
        {
          id: 1,
          name: 'John Smith',
          specialization: 'Strength Training',
          bio: 'Experienced fitness coach',
        },
        {
          id: 2,
          name: 'Sarah Johnson',
          specialization: 'Yoga & Flexibility',
          bio: 'Certified yoga instructor',
        },
        {
          id: 3,
          name: 'Mike Davis',
          specialization: 'Cardio Fitness',
          bio: 'Cardio specialist',
        },
        {
          id: 4,
          name: 'Emily Wilson',
          specialization: 'Nutrition',
          bio: 'Nutrition expert',
        },
      ]);
    } finally {
      setLoading(false);
    }
  };

  return (
    <section id="coaches" className="py-20 bg-gray-50">
      <div className="container mx-auto px-6">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold mb-4 text-gray-900">
              Meet Our Coaches
            </h2>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto mb-6">
              Expert trainers dedicated to helping you achieve your fitness goals
            </p>
            <a
              href="/coaches"
              className="inline-block bg-gradient-to-r from-blue-600 to-blue-800 text-white px-8 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition-all shadow-md"
            >
              View All Coaches →
            </a>
          </div>

          {loading ? (
            <div className="text-center py-12">
              <p className="text-gray-600">{t('common.loading')}</p>
            </div>
          ) : (
            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
              {coaches.map((coach) => (
                <div
                  key={coach.id}
                  className="bg-white rounded-xl p-6 border-2 border-gray-200 hover:border-blue-500 hover:shadow-xl transition-all transform hover:-translate-y-2 text-center"
                >
                  <div className="w-24 h-24 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl text-white">
                    {coach.name?.charAt(0) || '👨‍💼'}
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
                    <p className="text-gray-500 text-sm mb-4 line-clamp-2">
                      {coach.bio}
                    </p>
                  )}
                  <Link
                    to={`/coaches/${coach.id}/book`}
                    className="inline-block bg-gradient-to-r from-blue-600 to-blue-800 text-white px-6 py-2 rounded-lg font-semibold text-sm hover:from-blue-700 hover:to-blue-900 transition-all"
                  >
                    {t('coach.bookSession')}
                  </Link>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    </section>
  );
};

export default Coaches;

